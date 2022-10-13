<?php

namespace Zeobv\SchouwWitgoed\Storefront\Subscriber;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\Event\NavigationLoadedEvent;
use Shopware\Core\Content\Category\Tree\TreeItem;
use Shopware\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Product\Events\ProductCrossSellingsLoadedEvent;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Storefront\Page\Navigation\NavigationPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zeobv\SchouwWitgoed\Storefront\Service\CustomFieldsService;

class CategoryPageSubscriber implements EventSubscriberInterface
{
    private EntityRepositoryInterface $languageRepository;
    private EntityRepositoryInterface $manufacturerRepository;
    private CustomFieldsService $customFieldsService;

    public function __construct(
        EntityRepositoryInterface $languageRepository,
        EntityRepositoryInterface $manufacturerRepository,
        CustomFieldsService $customFieldsService
    )
    {
        $this->languageRepository = $languageRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->customFieldsService = $customFieldsService;
    }

    public static function getSubscribedEvents()
    {
        return [
            NavigationPageLoadedEvent::class => 'onNavigationPageLoaded',
            ProductCrossSellingsLoadedEvent::class => 'adaptCustomFieldsCrossSellings',
            NavigationLoadedEvent::class => 'onMenuOffcanvasPageletLoaded'
        ];
    }

    public function adaptCustomFieldsCrossSellings(ProductCrossSellingsLoadedEvent $event)
    {
        $criteria = new Criteria([$event->getSalesChannelContext()->getSalesChannel()->getLanguageId()]);
        $criteria->addAssociation('locale');
        $languages = $this->languageRepository->search($criteria, $event->getContext());

        if (!($language = $languages->first()) || !($locale = $language->getLocale())) {
            return;
        }

        $allSelectCustomFields = $this->customFieldsService->getCustomFieldsFromSelectType($event->getContext(), $locale->getCode(), 'en-GB');

        foreach ($event->getCrossSellings() as $crossSelling) {
            $this->customFieldsService->convertProductsCustomFields($crossSelling->getProducts()->getElements(), $allSelectCustomFields);
        }
    }

    public function onNavigationPageLoaded(NavigationPageLoadedEvent $event)
    {
        $this->addSeoOpenGraphUrl($event);
        $this->adaptCustomFieldsNavigationPage($event);
    }

    protected function addSeoOpenGraphUrl(NavigationPageLoadedEvent $event)
    {
        $robotsInfo = $event->getPage()->getMetaInformation()->getRobots();

        if (strpos($robotsInfo, 'noindex') !== false) {
            return;
        }

        $baseUrl = $event->getRequest()->attributes->get('sw-sales-channel-absolute-base-url');
        $requestUri = $event->getRequest()->attributes->get('sw-original-request-uri');
        $openGraphUrl = $baseUrl . $requestUri;

        $event->getPage()->addExtension('seoOpenGraph', new ArrayStruct([
            'url' => $openGraphUrl,
        ]));
    }

    /**
     * @param NavigationPageLoadedEvent $event
     */
    protected function adaptCustomFieldsNavigationPage(NavigationPageLoadedEvent $event)
    {
        $allSelectCustomFields = $this->customFieldsService->getCustomFieldsFromSelectType($event->getContext(), $event->getRequest()->getLocale(), $event->getRequest()->getDefaultLocale());

        $cmsPage = $event->getPage()->getCmsPage();

        if (method_exists($cmsPage, 'getBlocks')) {
            $partials = $cmsPage->getBlocks();
        } elseif (method_exists($cmsPage, 'getSections')) {
            $partials = $cmsPage->getSections()->getBlocks();
        } else {
            return;
        }

        if (!$partials instanceof CmsBlockCollection) return;

        /** @var CmsSlotEntity $slot */
        foreach ($partials->getSlots() as $slot) {
            $slotData = $slot->getData();

            switch ($slot->getType()) {
                case 'product-listing':
                    $listing = $slotData->getListing();
                    if ($listing instanceof EntitySearchResult) {
                        $products = $listing->getElements();
                    }
                    break;
                case 'product-slider':
                    $productCollection = $slotData->getProducts();
                    if ($productCollection instanceof ProductCollection) {
                        $products = $productCollection->getElements();
                    }
                    break;
                case 'product-box':
                    $product = $slotData->getProduct();
                    $products = [$product];
                    break;
                default:
                    continue 2;
            }

            if (empty($products)) {
                continue;
            }

            $this->customFieldsService->convertProductsCustomFields($products, $allSelectCustomFields);
        }
    }

    /**
     * Add manufacturer to menu data.
     *
     * @param NavigationLoadedEvent $event
     */
    public function onMenuOffcanvasPageletLoaded(NavigationLoadedEvent $event): void
    {
        /** @var TreeItem[] $tree */
        $tree = $event->getNavigation()->getTree();
        $customFieldName = 'custom_categorie_field_set_menu_manufacturer';
        $manufacturers = [];

        foreach ($tree as $item) {
            /** @var CategoryEntity $category */
            $category = $item->getCategory();
            $customFields = $category->getCustomFields();

            if (!empty($customFields[$customFieldName])) {
                $criteria = new Criteria($customFields[$customFieldName]);
                $criteria->addAssociation('media');
                $manufacturers = $this->manufacturerRepository->search($criteria, $event->getContext())->getElements();
            }

            $category->addExtension('menu_manufacturer', new ArrayEntity($manufacturers));
        }
    }
}
