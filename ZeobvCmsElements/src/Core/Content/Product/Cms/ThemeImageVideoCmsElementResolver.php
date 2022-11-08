<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\Product\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\CrossSellingStruct;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageStruct;
use Shopware\Core\Content\Cms\SalesChannel\Struct\TextStruct;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\Cms\AbstractProductDetailCmsElementResolver;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\AbstractProductCrossSellingRoute;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;


class ThemeImageVideoCmsElementResolver extends AbstractProductDetailCmsElementResolver
{
    /**
     * @var SalesChannelRepositoryInterface
     */
    private $productRepository;

    private SystemConfigService $systemConfigService;

    /**
     * @internal
     */
    public function __construct(SalesChannelRepositoryInterface $productRepository, SystemConfigService $systemConfigService)
    {
        $this->productRepository = $productRepository;
        $this->systemConfigService = $systemConfigService;
    }

    public function getType(): string
    {
        return 'text-product-video';
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $context = $resolverContext->getSalesChannelContext();


        $productvideoHeader = $slot->getFieldConfig()->get('videoHeader');
        $productvideoID = $slot->getFieldConfig()->get('videoID');
        $productContent = $slot->getFieldConfig()->get('content');

        $productsContext = null;
        $productsvideoHeader = null;
        $productsvideoID = null;

        //products context

        $productsContext = new TextStruct();
        //$slot->setData($textContent);

        if ($productContent->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $productId = $this->resolveEntityValue($resolverContext->getEntity(), $productContent->getStringValue());

            $criteria = new Criteria();
            $criteria->addFilter(
                new EqualsAnyFilter('id', [$productId]),
            );
            $productsCon = $this->productRepository->search($criteria, $context)->getElements();
            foreach($productsCon as $value){
                $productsContext = $value->getCustomFields();
            }
        }

        if ($productContent->isStatic()) {
            if ($resolverContext instanceof EntityResolverContext) {
                $productsContext = (string)$this->resolveEntityValues($resolverContext, $productContent->getStringValue());

            } else {
                $productsContext = $productContent->getStringValue();
            }
        }

        //product video
        $productsvideoID = null;
       // $slot->setData($image);
        if ($productvideoID->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $productId = $this->resolveEntityValue($resolverContext->getEntity(), $productvideoID->getStringValue());

            $result = null;
            $criteria = new Criteria();
            $criteria->addFilter(
                new EqualsAnyFilter('id', [$productId]),
            );
            $productsvideo = $this->productRepository->search($criteria, $context)->getElements();
            foreach($productsvideo as $value){
                $productsvideoID = $value->getCustomFields();
            }

        }

        if ($productvideoID->isStatic()) {
            if ($resolverContext instanceof EntityResolverContext) {
                $productsvideoID = (string)$this->resolveEntityValues($resolverContext, $productvideoID->getStringValue());

            } else {
                $productsvideoID = $productvideoID->getStringValue();
            }
        }

        //product video header
        $productsvideoHeader = new TextStruct();
       // $slot->setData($textHeader);

        if ($productvideoHeader->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $productId = $this->resolveEntityValue($resolverContext->getEntity(), $productvideoHeader->getStringValue());


            $criteria = new Criteria();
            $criteria->addFilter(
                new EqualsAnyFilter('id', [$productId]),
            );
            $productsvideo = $this->productRepository->search($criteria, $context)->getElements();
            foreach($productsvideo as $value){
                $productsvideoHeader = $value->getCustomFields();
            }

        }

        if ($productvideoHeader->isStatic()) {
            if ($resolverContext instanceof EntityResolverContext) {
                $productsvideoHeader = (string)$this->resolveEntityValues($resolverContext, $productvideoHeader->getStringValue());

            } else {
                $productsvideoHeader = $productvideoHeader->getStringValue();
            }
        }

        $slot->setData(new ArrayStruct(["content"=>$productsContext, "videoHeader"=>$productsvideoHeader, "videoId"=>$productsvideoID]));
    }
}
