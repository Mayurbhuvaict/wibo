<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed\Storefront\Service;

use Shopware\Core\Content\Product\ProductEntity;

/**
 * Class MigrationAttributeService
 *
 * This service is used to grab the first value of a given custom field in throughout multiple custom field sets.
 *
 * @package Zeobv\SchouwWitgoed\Storefront\Service
 */
class MigrationAttributeService {
    /**
     * @var string|null
     */
    private static ?string $customFieldPrefix = null;

    /**
     * Retrieves the first filled custom field value from the fieldset.
     *
     * @param ProductEntity $productEntity
     * @param string $migrationAttribute
     * @return mixed|null
     */
    public function getMigrationAttributeFromFieldset(ProductEntity $productEntity, string $migrationAttribute): ?string
    {
        if (is_null($productEntity->getCustomFields())) {
            return null;
        }

        if (self::$customFieldPrefix && array_key_exists(self::$customFieldPrefix . $migrationAttribute, $productEntity->getCustomFields())) {
            return $productEntity->getCustomFields()[self::$customFieldPrefix . $migrationAttribute];
        }

        foreach ($productEntity->getCustomFields() as $customFieldKey => $customFieldValue) {
            if ($this->strEndsWith($customFieldKey, $migrationAttribute)) {
                $this->setCustomFieldPrefix($customFieldKey, $migrationAttribute);

                return $customFieldValue;
            }
        }

        return null;
    }

    /**
     * Checks if a given needle matches the end of haystack.
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    protected function strEndsWith($haystack, $needle): bool
    {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    /**
     * Sets the custom prefix with the given value to avoid multiple iterations.
     *
     * @param $string
     * @param $suffix
     */
    protected function setCustomFieldPrefix($string, $suffix): void
    {
        preg_match("/(.*){$suffix}/", $string, $matches);

        if (!$matches || !array_key_exists(1, $matches)) {
            return;
        }

        self::$customFieldPrefix = $matches[1];
    }
}
