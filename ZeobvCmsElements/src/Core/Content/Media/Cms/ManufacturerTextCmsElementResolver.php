<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Core\Content\Media\Cms;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\TextStruct;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Util\HtmlSanitizer;
use Shopware\Core\System\CustomField\CustomFieldDefinition;

class ManufacturerTextCmsElementResolver extends AbstractCmsElementResolver
{
    private HtmlSanitizer $sanitizer;

    public function __construct(HtmlSanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function getType(): string
    {
        return 'manufacturer-text';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $customFieldConfig = $config->get('customField');
        if (
            (!$resolverContext instanceof EntityResolverContext && !$resolverContext->getEntity() instanceof SalesChannelProductEntity)
            || !$customFieldConfig
            || is_null($resolverContext->getEntity()->getManufacturerId())
        ) {
            return null;
        }

        $customFieldId = $customFieldConfig->getValue();
        $criteria = new Criteria([$customFieldId]);

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('custom_field_' . $slot->getUniqueIdentifier(), CustomFieldDefinition::class, $criteria);

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        if (
            (!$resolverContext instanceof EntityResolverContext && !$resolverContext->getEntity() instanceof SalesChannelProductEntity)
            || is_null($customFieldsSearchResult = $result->get('custom_field_' . $slot->getUniqueIdentifier()))
            || is_null($customField = $customFieldsSearchResult->getEntities()->first())
            || is_null($productManufacturer = $resolverContext->getEntity()->getManufacturer())
            || !array_key_exists($customField->getName(), $productManufacturer->getCustomFields() ?? [])
        ) {
            return;
        }

        $text = new TextStruct();
        $slot->setData($text);

        $content = (string) $this->resolveEntityValues($resolverContext, $productManufacturer->getCustomFields()[$customField->getName()]);
        if (Feature::isActive('FEATURE_NEXT_15172')) {
            $text->setContent($this->sanitizer->sanitize($content));
        } else {
            $text->setContent($content);
        }
    }
}
