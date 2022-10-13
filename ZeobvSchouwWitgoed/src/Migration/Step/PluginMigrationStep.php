<?php


namespace Zeobv\SchouwWitgoed\Migration\Step;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;


abstract class PluginMigrationStep extends MigrationStep
{
    const PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME = 'zeobv_schouw_witgoed_custom_product_downloads';

    abstract public function down(Connection $connection, bool $keepUserData): void;

    protected function createCustomField(Connection $connection, array $customField, ?string $customFieldSetName = null): void
    {
        if ($customFieldSetName) {
            $customField['set_id'] = $this->getCustomFieldSetIdByName($connection, $customFieldSetName);
        }

        $this->insert(
            $connection,
            'custom_field',
            $customField
        );
    }

    protected function createCustomFields(Connection $connection, array $customFields, ?string $customFieldSetName = null): void
    {
        foreach ($customFields as $customField) {
            $this->createCustomField($connection, $customField, $customFieldSetName);
        }
    }

    protected function createCustomFieldSet(Connection $connection, array $customFieldSet, array $entityRelations = null): void
    {
        $this->insert($connection, 'custom_field_set', $customFieldSet);

        if (is_array($entityRelations)) {
            foreach ($entityRelations as $entity) {
                $this->insert($connection, 'custom_field_set_relation', [
                    'set_id' => $customFieldSet['id'],
                    'entity_name' => $entity,
                ]);
            }
        }
    }

    protected function getCustomFieldSetIdByName(Connection $connection, string $customFieldSetName, bool $hexValue = true)
    {
        $selectFieldsStatement = $hexValue ? 'HEX(id)' : 'id';
        return $connection->executeQuery("SELECT {$selectFieldsStatement} FROM custom_field_set WHERE name = :name", [
            'name' => $customFieldSetName,
        ])->fetchOne();
    }

    protected function insert(Connection $connection, string $table, array $entityData)
    {
        if (!array_key_exists('id', $entityData)) {
            $entityData['id'] = Uuid::randomBytes();
        }

        if (!array_key_exists('created_at', $entityData)) {
            $entityData['created_at'] = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);
        }

        $connection->insert($table, $entityData);
    }

    /**
     * @param Connection $connection
     * @param string $table
     * @param array $data is a multidimensional array with locale as key and the translation data as MULTIPLE values
     *
     * @example $this->insertTranslations($connection, 'entity_translations', [
     *     'en-GB' => [
     *         ["name" => "English name 1"],
     *         ["name" => "English name 2"],
     *     ],
     *     'de-DE' => [
     *         ["name" => "German name 1"],
     *         ["name" => "German name 2"],
     *     ],
     *     'nl-NL' => [
     *         ["name" => "Dutch name 1"],
     *         ["name" => "Dutch name 2"],
     *     ],
     * ])
     */
    protected function insertTranslations(Connection $connection, string $table, array $data)
    {
        foreach ($data as $locale => $translations) {
            foreach ($translations as $translationData) {

                // A locale can be assigned to multiple languages
                foreach ($this->getLanguageIds($connection, $locale) as $languageId) {
                    $translationData['language_id'] = $languageId;
                    $translationData['created_at'] = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);
                    $connection->insert($table, $translationData);
                }
            }
        }
    }

    protected function getLanguageIds(Connection $connection, string $locale): array
    {
        $ids = $connection->fetchAll('
            SELECT `language`.id as id
            FROM `language`
            INNER JOIN locale
                ON `language`.`locale_id` = `locale`.`id`
                AND locale.code = :locale
        ', ['locale' => $locale]);

        return array_unique(array_filter(array_column($ids, 'id')));
    }

    protected function columnExists(Connection $connection, string $table, string $column): bool
    {
        return is_null($connection->executeQuery("
            SELECT NULL
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE table_name = '{$table}'
            AND table_schema = '{$connection->getDatabase()}'
            AND column_name = '{$column}'")->fetchOne());
    }
}

