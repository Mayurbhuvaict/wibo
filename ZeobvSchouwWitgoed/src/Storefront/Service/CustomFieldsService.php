<?php

namespace Zeobv\SchouwWitgoed\Storefront\Service;

use Kiener\MolliePayments\Compatibility\Gateway\CompatibilityGateway;
use Kiener\MolliePayments\Compatibility\Gateway\CompatibilityGatewayInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService as SalesChannelCartService;
use Shopware\Core\Content\Product\Cart\ProductLineItemFactory;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannel\SalesChannelContextSwitcher;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * Class CustomFieldsService
 * @package Zeobv\SchouwWitgoed\Storefront\Service
 */
class CustomFieldsService
{
    /** @var EntityRepositoryInterface */
    private EntityRepositoryInterface $_customFieldRepository;

    /**
     * @param EntityRepositoryInterface $_customFieldRepository
     */
    public function __construct(EntityRepositoryInterface $_customFieldRepository)
    {
        $this->_customFieldRepository = $_customFieldRepository;
    }

    /**
     * @param array $products
     * @param array $allSelectCustomFields
     */
    public function convertProductsCustomFields(array $products, array $allSelectCustomFields): void
    {
        /** @var ProductEntity $product */
        foreach ($products as $product) {
            $customFields = $product->getCustomFields();

            foreach ($customFields as $productCustomFieldKey => $productCustomFieldValue) {
                if (is_array($productCustomFieldValue)) {
                    foreach ($productCustomFieldValue as $productCustomFieldValueItem) {
                        $customFields[$productCustomFieldKey][] = $this->getCustomFieldValue($productCustomFieldValueItem, $productCustomFieldKey, $allSelectCustomFields);
                    }

                    continue;
                }

                $customFields[$productCustomFieldKey] = $this->getCustomFieldValue($productCustomFieldValue, $productCustomFieldKey, $allSelectCustomFields);
            }

            $product->setCustomFields($customFields);
        }
    }

    /**
     * @param Context $context
     * @param string $primaryLocale
     * @param string $fallbackLocale
     *
     * @return CustomFieldEntity[]
     */
    public function getCustomFieldsFromSelectType(Context $context, string $primaryLocale, string $fallbackLocale): array
    {
        $allCustomFields = $this->_customFieldRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('type', 'select'))
                ->addFilter(new EqualsFilter('customFieldSet.relations.entityName', 'product')),
            $context
        );

        $mappedCustomFields = [];

        /** @var CustomFieldEntity $customField */
        foreach ($allCustomFields->getElements() as $customField) {
            $options = $customField->getConfig()['options'] ?? [];

            $mappedOptions = [];
            foreach ($options as $option) {
                if (key_exists($primaryLocale, $option['label'])) {
                    $mappedOptions[$option['value']] = $option['label'][$primaryLocale];
                } else if (key_exists($fallbackLocale, $option['label'])) {
                    $mappedOptions[$option['value']] = $option['label'][$fallbackLocale];
                }
            }

            $mappedCustomFields[$customField->getName()] = $mappedOptions;
        }

        return $mappedCustomFields;
    }

    /**
     * @param $productCustomFieldValue
     * @param $productCustomFieldKey
     * @param $allSelectCustomFields
     * @return string
     */
    private function getCustomFieldValue($productCustomFieldValue, $productCustomFieldKey, $allSelectCustomFields): string
    {
        if (key_exists($productCustomFieldKey, $allSelectCustomFields)
            && is_array($allSelectCustomFields[$productCustomFieldKey])
            && !(
                is_null($productCustomFieldValue) ||
                !key_exists($productCustomFieldValue, $allSelectCustomFields[$productCustomFieldKey])
            )
        ) {
            return $allSelectCustomFields[$productCustomFieldKey][$productCustomFieldValue];
        }

        return $productCustomFieldValue;
    }
}
