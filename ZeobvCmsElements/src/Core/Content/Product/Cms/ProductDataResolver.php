<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\Product\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\CrossSellingStruct;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ImageStruct;
use Shopware\Core\Content\Cms\SalesChannel\Struct\TextStruct;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Product\Cms\AbstractProductDetailCmsElementResolver;
use Shopware\Core\Content\Product\SalesChannel\CrossSelling\AbstractProductCrossSellingRoute;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Feature;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;

class ProductDataResolver extends AbstractProductDetailCmsElementResolver
{
    /**
     * @var SalesChannelRepositoryInterface
     */
    private $productRepository;
    private EntityRepositoryInterface $orderLineItemRepository;



    /**
     * @internal
     */
    public function __construct(AbstractProductCrossSellingRoute $crossSellingLoader,SalesChannelRepositoryInterface $productRepository,EntityRepositoryInterface $orderLineItemRepository)
    {
        $this->crossSellingLoader = $crossSellingLoader;
        $this->productRepository = $productRepository;
        $this->orderLineItemRepository = $orderLineItemRepository;
    }

    public function getType(): string
    {
        return 'image-text';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $mediaConfig = $slot->getFieldConfig()->get('media');
        if ($mediaConfig === null || $mediaConfig->isMapped() || $mediaConfig->getValue() === null) {
            return null;
        }

        $criteria = new Criteria([$mediaConfig->getStringValue()]);

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('media_' . $slot->getUniqueIdentifier(), MediaDefinition::class, $criteria);

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $image = new ImageStruct();
//        $slot->setData($image);

        $urlConfig = $config->get('url');
        if ($urlConfig !== null) {
            if ($urlConfig->isStatic()) {
                $image->setUrl($urlConfig->getStringValue());
            }

            $newTabConfig = $config->get('newTab');
            if ($newTabConfig !== null) {
                $image->setNewTab($newTabConfig->getBoolValue());
            }
        }

        $mediaConfig = $config->get('media');
        if ($mediaConfig && $mediaConfig->getValue()) {
            $this->addMediaEntity($slot, $image, $result, $mediaConfig, $resolverContext);
        }

        //content
        $text = new TextStruct();
//        $slot->setData($text);

        $config = $slot->getFieldConfig()->get('content');
        if ($config === null) {
            return;
        }

        $content = null;

        if ($config->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $content = $this->resolveEntityValueToString($resolverContext->getEntity(), $config->getStringValue(), $resolverContext);
        }

        if ($config->isStatic()) {
            if ($resolverContext instanceof EntityResolverContext) {
                $content = (string) $this->resolveEntityValues($resolverContext, $config->getStringValue());
            } else {
                $content = $config->getStringValue();
            }
        }

        if ($content !== null) {
            if (Feature::isActive('FEATURE_NEXT_15172')) {
                $text->setContent($this->sanitizer->sanitize($content));
            } else {
                $text->setContent($content);
            }
        }

        $slot->setData(new ArrayStruct([$image, $text]));
    }

    private function addMediaEntity(
        CmsSlotEntity $slot,
        ImageStruct $image,
        ElementDataCollection $result,
        FieldConfig $config,
        ResolverContext $resolverContext
    ): void {
        if ($config->isStatic()) {
            $image->setMediaId($config->getStringValue());

            $searchResult = $result->get('media_' . $slot->getUniqueIdentifier());
            if (!$searchResult) {
                return;
            }

            /** @var MediaEntity|null $media */
            $media = $searchResult->get($config->getValue());
            if (!$media) {
                return;
            }

            $image->setMedia($media);
        }
    }


}
