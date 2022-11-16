<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Content\Product\Events\ProductListingCollectFilterEvent;
use Shopware\Core\Content\Product\SalesChannel\Listing\Filter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Content\Product\Events\ProductSearchResultEvent;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Framework\Adapter\Translation\Translator;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\TermsAggregation;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\Tree\Tree;
use Shopware\Core\Content\Category\Tree\TreeItem;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Bucket\BucketResult;

class Subscriber implements EventSubscriberInterface
{
    const SEPARATOR = '%separator%';

    /**
     * @var TreeItem
     */
    private $treeItem;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var SalesChannelRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var Translator
     */
    private $translator;

    private $settings;

    private $buckets;

    public function __construct(
        SystemConfigService $systemConfigService,
        SalesChannelRepositoryInterface $categoryRepository,
        Translator $translator
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->categoryRepository = $categoryRepository;
        $this->translator = $translator;
        $this->treeItem = new TreeItem(null, []);
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductListingCollectFilterEvent::class => 'addFilter',
            ProductListingResultEvent::class => 'handleResult',
            ProductSearchResultEvent::class => 'handleResult',
        ];
    }

    public function addFilter(ProductListingCollectFilterEvent $event)
    {
        $filters = $event->getFilters();

        $request = $event->getRequest();

        $ids = $this->getCategoryIds($request);

        $aggregation = new TermsAggregation('category', 'product.categoriesRo.id');

        $aggregationStream = new TermsAggregation('streamCategory', 'product.streamCategories.id');

        /*$filter = new Filter(
            'category',
            !empty($ids),
            [
                $aggregation,
                $aggregationStream,
            ],
            new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsAnyFilter('product.categoriesRo.id', $ids),
                new EqualsAnyFilter('product.streamCategories.id', $ids),
            ]),
            $ids
        );

        $filters->add($filter);*/
    }

    private function getCategoryIds(Request $request): array
    {
        $ids = $request->query->get('categories', '');
        if ($request->isMethod(Request::METHOD_POST)) {
            $ids = $request->request->get('categories', '');
        }

        if (\is_string($ids)) {
            $ids = explode('|', $ids);
        }

        return array_filter($ids);
    }

    public function handleResult(ProductListingResultEvent $event)
    {
        $this->setSettings($event->getSalesChannelContext()->getSalesChannel());
        if (!$this->getSetting('showCategoryFilter')) {
            return;
        }

        $showTree = true;
        $result = $event->getResult();
        try {
            $buckets = $result->getAggregations()->get('category');
            if (!$this->getSetting('hideDynamicCategory')) {
                $bucketsStream = $result->getAggregations()->get('streamCategory');
            }
        } catch (\Exception $e) {
            $buckets = false;
        }

        if (!$buckets) {
            return;
        }

        if (!$this->getSetting('hideDynamicCategory')) {
            $buckets = new BucketResult(
                'category',
                array_merge($buckets->getBuckets(), $bucketsStream->getBuckets())
            );
        }


        $this->buckets = $buckets;

        $categoryIds = $buckets->getKeys();

        if (!$categoryIds) {
            return;
        }

        $categoryIds = array_filter($categoryIds);

        $categories = $this
            ->categoryRepository
            ->search(new Criteria($categoryIds), $event->getSalesChannelContext());

        $rootCategoryId = $event->getSalesChannelContext()->getSalesChannel()->getNavigationCategoryId();

        if (!$categories) {
            return;
        }

        $curentCategory = null;
        $currentCategoryId = $result->getCurrentFilter('navigationId');
        if ($currentCategoryId) {
            /** @var CategoryEntity $category */
            $curentCategory = $this
                ->categoryRepository
                ->search(new Criteria([$currentCategoryId]), $event->getSalesChannelContext())
                ->getEntities()
                ->first();
        }

        $items = new CategoryCollection();

        foreach ($categories->getEntities() as $category) {

            //exlude hidden categories
            if (!$category->getActive() || !$category->getVisible()) {
                continue;
            }

            //exlude corrupted categories (and SalesChannel root category)
            if (is_null($category->getPath())) {
                continue;
            }

            //exclude categories from other sailsChannel
            if (stripos($category->getPath(), $rootCategoryId) === false) {
                continue;
            }

            //exclude current category (if configured so)
//            if ($curentCategory
//                && $this->getSetting('hideCurrentCategory')
//                && $curentCategory->getId()==$category->getId()
//            ) {
//                continue;
//            }


            //exclude categories from other branches (if in category and configured so)
            if ($curentCategory
                && $this->getSetting('hideOtherBranches')
                && stripos($category->getPath().$category->getId(), $curentCategory->getPath().$curentCategory->getId()) === false
                && stripos($curentCategory->getPath().$curentCategory->getId(), $category->getPath().$category->getId()) === false) {
                continue;
            }

            $parentIds = explode('|', trim($category->getPath(), '|'));

            $parentCategoryNames = [];

            foreach ($parentIds as $id) {
                $cat = $categories->get($id);
                if ($cat) {
                    $parentCategoryNames[] = $cat->getTranslated()['name'];
                }
            }

            $parentCategoryNames[] = $category->getTranslated()['name'];

            $category->addExtension(
                'bucket',
                new ArrayEntity([
                    'count' => $buckets->get($category->getId())->getCount()
                    ,'parentNames' => $parentCategoryNames
                ])
            );

            $items->add($category);
        }


        $itemsTree = $this->getTree($rootCategoryId, $items, null);

        $itemsTree = $this->hideUnpopular($itemsTree);
        $itemsTree = $this->deleteParents($itemsTree);
        $itemsTree = $this->joinParents($itemsTree);

        $itemsTree = $this->simplifyPath($itemsTree);

        $itemsTree = $this->processNames($itemsTree);
        $itemsTree = $this->sortCategories($itemsTree);

        if ($this->getSetting('hideSingleCategory') && count($this->getSortedEndCategoryIds($itemsTree->getTree()))==1) {
            return;
        }

        $result->addExtension('categoryTree', $itemsTree);
    }

    public function setSettings($salesChannel)
    {
        $this->settings = $this->systemConfigService->get(
            'TopdataCategoryFilterSW6.config',
            $salesChannel->getId()
        );
    }

    private function getSetting(string $key)
    {
        if (!isset($this->settings)) {
            $this->settings = $this->systemConfigService->get('ZeobvCmsElements.config');
        }

        return isset($this->settings[$key]) ? $this->settings[$key] : false;
    }

    private function getTree(?string $parentId, CategoryCollection $categories, ?CategoryEntity $active): Tree
    {
        $tree = $this->buildTree($parentId, $categories->getElements());
        return new Tree($active, $tree);
    }

    /**
     * @param CategoryEntity[] $categories
     *
     * @return TreeItem[]
     */
    private function buildTree(?string $parentId, array $categories): array
    {
        $children = new CategoryCollection();
        foreach ($categories as $key => $category) {
            if ($category->getParentId() !== $parentId) {
                continue;
            }

            unset($categories[$key]);

            $children->add($category);
        }

        $children->sortByPosition();

        $items = [];
        foreach ($children as $child) {
            if (!$child->getActive() || !$child->getVisible()) {
                continue;
            }

            $item = clone $this->treeItem;
            $item->setCategory($child);

            $item->setChildren(
                $this->buildTree($child->getId(), $categories)
            );

            $items[$child->getId()] = $item;
        }

        return $items;
    }


    private function simplifyPath(Tree $tree) : Tree
    {
        if (!$this->getSetting('manyMainCategories') || (count($tree->getTree()) > 1)) {
            return $tree;
        }


        $tree = $this->checkAndSimplifyPath($tree->getTree());


        $newTree = new Tree(null, $tree);
        return $newTree;
    }

    private function checkAndSimplifyPath(array $tree) : array
    {
        foreach ($tree as $key => $item) {
            $translatedPath = $item->getCategory()->getTranslated();
            $childrens = $item->getChildren();
            foreach ($childrens as $nestedKey => $nested) {
                $translatedNested = $nested->getCategory()->getTranslated();
                $translatedNested['name'] = $translatedPath['name']
                                            . self::SEPARATOR
                                            . $translatedNested['name'];
                $nested->getCategory()->setTranslated($translatedNested);
                $childrens[$nestedKey] = $nested;
            }

            if (count($childrens) == 1) {
                $childrens = $this->checkAndSimplifyPath($childrens);
            } elseif (count($childrens) == 0) {
                return $tree;
            }
            return $childrens;
        }

        return $tree;
    }


    private function processNames(Tree $tree) : Tree
    {
        $maxParents = (int)$this->getSetting('showParentCategory');
        $separator = $this->translator->trans('topdata-category-filter.parentCategoriesSeparator');
        $tree = $this->processCountNames($tree->getTree(), $maxParents, $separator);


        $newTree = new Tree(null, $tree);
        return $newTree;
    }


    private function processCountNames(array $tree, int $maxParents, string $separator) : array
    {
        foreach ($tree as $key => $item) {
            $children = $item->getChildren();
            $children = $this->processCountNames($children, $maxParents, $separator);
            $item->setChildren($children);

            $translated = $item->getCategory()->getTranslated();

            $translatedNameArray = explode(self::SEPARATOR, $translated['name']);

            $translated['name'] = implode(
                $separator,
                array_slice($translatedNameArray, -(1+$maxParents))
            );

            $item->getCategory()->setTranslated($translated);
        }

        return $tree;
    }


    private function joinParents(Tree $tree) : Tree
    {
        if (!$this->getSetting('joinParents')) {
            return $tree;
        }

        $tree = $this->checkAndJoin($tree->getTree());


        $newTree = new Tree(null, $tree);
        return $newTree;
    }

    private function checkAndJoin(array $tree) : array
    {
        foreach ($tree as $key => $item) {
            $children = $item->getChildren();
            $children = $this->checkAndJoin($children);
            $item->setChildren($children);
            if (count($item->getChildren())==1) {
                $nestedChildrens = $item->getChildren();
                $nested = array_pop($nestedChildrens);
                unset($tree[$key]);

                $translatedParent = $item->getCategory()->getTranslated();
                $translatedNested = $nested->getCategory()->getTranslated();
                $translatedNested['name'] = $translatedParent['name']
                                                . self::SEPARATOR
                                                . $translatedNested['name'];
                $nested->getCategory()->setTranslated($translatedNested);
                $tree[$nested->getCategory()->getId()] = $nested;
            }
        }

        return $tree;
    }

    private function deleteParents(Tree $tree) : Tree
    {
        if (!$this->getSetting('hideParents')) {
            return $tree;
        }

        $tree = $this->checkAndDelete($tree->getTree());

        $newTree = new Tree(null, $tree);
        return $newTree;
    }

    private function checkAndDelete(array $tree) : array
    {
        $result = [];
        foreach ($tree as $key => $item) {
            $children = $item->getChildren();
            if (!$children) {
                $result = array_merge($result, [$key=>$item]);
                continue;
            }
            $children = $this->checkAndDelete($children);
            $result = array_merge($result, $children);
        }

        return $result;
    }


    private function getSortedEndCategoryIds(array $tree, int $count = 3): array
    {
        $tree = $this->checkAndDelete($tree);
        $tree = $this->sortByCount($tree);
        $ids = [];
        foreach ($tree as $key=>$item) {
            $ids[] = $key;
            if (count($ids) >= $count) {
                break;
            }
        }
        return $ids;
    }

    private function hideUnpopular(Tree $tree) : Tree
    {
        if (!$this->getSetting('popularCount')) {
            return $tree;
        }



        $popularIds = $this->getSortedEndCategoryIds($tree->getTree(), (int)$this->getSetting('popularCount'));

        $tree = $this->hideUnpopularWalker($tree->getTree(), $popularIds);

        $newTree = new Tree(null, $tree);
        return $newTree;
    }

    private function hideUnpopularWalker(array $tree, array $ids) : array
    {
        $result = [];
        foreach ($tree as $key => $item) {
            $children = $item->getChildren();
            if (!$children && in_array($key, $ids)) {
                $result = array_merge($result, [$key=>$item]);
                continue;
            }

            $children = $this->hideUnpopularWalker($children, $ids);

            if ($this->existInBranch($children, $ids)) {
                $item->setChildren($children);
                $result = array_merge($result, [$key=>$item]);
                continue;
            }
        }

        return $result;
    }

    private function existInBranch(array $tree, array $ids): bool
    {
        foreach ($tree as $key => $item) {
            $children = $item->getChildren();
            if (!$children) {
                return in_array($key, $ids);
            }

            if ($this->existInBranch($children, $ids)) {
                return true;
            }
        }

        return false;
    }


    private function sortCategories(Tree $tree) : Tree
    {
        if ($this->getSetting('sort') == 'name') {
            $tree = $this->sortByName($tree->getTree());
        } elseif ($this->getSetting('sort') == 'count') {
            $tree = $this->sortByCount($tree->getTree());
        } else {
            return $tree;
        }

        $newTree = new Tree(null, $tree);
        return $newTree;
    }


    private function sortByCount(array $tree) : array
    {
        $buckets = $this->buckets;

        uksort($tree, function ($id1, $id2) use ($buckets) {
            $a = $buckets->get($id1)->getCount();
            $b = $buckets->get($id2)->getCount();

            if ($a == $b) {
                return 0;
            }
            return ($a > $b) ? -1 : 1;
        });

        foreach ($tree as $key => $item) {
            $children = $item->getChildren();
            if (!$children) {
                continue;
            }
            $children = $this->sortByCount($children);
            $tree[$key]->setChildren($children);
        }

        return $tree;
    }


    private function sortByName(array $tree) : array
    {
        uasort($tree, function ($a, $b) {
            return strnatcasecmp($a->getCategory()->getTranslated()['name'], $b->getCategory()->getTranslated()['name']);
        });

        foreach ($tree as $key => $item) {
            $children = $item->getChildren();
            if (!$children) {
                continue;
            }
            $children = $this->sortByName($children);
            $tree[$key]->setChildren($children);
        }

        return $tree;
    }
}
