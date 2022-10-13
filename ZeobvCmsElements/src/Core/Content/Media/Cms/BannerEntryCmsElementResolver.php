<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\Media\Cms;

use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\MinAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric\MinResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;

class BannerEntryCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'banner-entry';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $typeConfigValue = $config->get('type') ? $config->get('type')->getValue() : null;
        $idConfigValue = $config->get('id') ? $config->get('id')->getValue() : null;
        if (
            is_null($typeConfigValue) ||
            is_null($idConfigValue)
        ) {
            return null;
        }

        $criteria = new Criteria([$idConfigValue]);

        $key = 'banner_entry_entity_' . $slot->getUniqueIdentifier();
        $criteriaCollection = new CriteriaCollection();
        switch ($typeConfigValue) {
            case ProductDefinition::ENTITY_NAME:
                $criteriaCollection->add($key, ProductDefinition::class, $criteria);
                break;
            case CategoryDefinition::ENTITY_NAME:
                $criteria->addAssociation('media');
                $this->enrichCategoryCriteriaWithCheapestPrice($criteria);
                $criteriaCollection->add($key, CategoryDefinition::class, $criteria);
                break;
            default:
                throw new \RuntimeException($typeConfigValue . ' is not a valid banner entry type');
        }

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        if (
            (!$resolverContext instanceof EntityResolverContext && !$resolverContext->getEntity() instanceof SalesChannelProductEntity)
            || is_null($entitySearchResult = $result->get('banner_entry_entity_' . $slot->getUniqueIdentifier()))
            || is_null($entity = $entitySearchResult->getEntities()->first())
        ) {
            return;
        }

        /** @var MinResult $cheapestPrice */
        $cheapestPrice = $entitySearchResult->getAggregations()->get('cheapestPrice');

        $slot->setData(new ArrayStruct([
            'type' => $slot->getFieldConfig()->get('type')->getValue(),
            'product' => $entity instanceof ProductEntity ? $entity : null,
            'category' => $entity instanceof CategoryEntity ? $entity : null,
            'cheapestPrice' => $cheapestPrice ? (float) $cheapestPrice->getMin() : null,
        ]));
    }

    protected function enrichCategoryCriteriaWithCheapestPrice(Criteria $criteria): void
    {
        $criteria->addAssociation('products');

        $criteria->getAssociation('products')
            ->addFilter(new EqualsFilter('active', true))
            ->addFilter(new EqualsFilter('available', true))
            ->setLimit(1);

        $criteria->addAggregation(new MinAggregation('cheapestPrice', 'products.cheapestPrice'));
    }
}
