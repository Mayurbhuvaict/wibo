<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Zeobv\SchouwWitgoed\Migration\Step\PluginMigrationStep;

class Migration1661430599CreateDownloadUrlsCustomFields extends PluginMigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1661430599;
    }

    public function update(Connection $connection): void
    {
        $customFieldSetId = $this->getCustomFieldSetIdByName($connection, self::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME, false);

        for ($i=1; $i <= 5; $i++) {
            $this->createCustomField($connection, [
                'name' => self::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME . '_url_' . $i,
                'type' => 'text',
                'set_id' => $customFieldSetId,
                'active' => true,
                'config' => json_encode([
                    "componentName" => "sw-field",
                    "customFieldType" => "text",
                    "customFieldPosition" => ($i * 10) - 2,
                    "label" => [
                        "de-DE" => 'Download URL ' . $i,
                        "en-GB" => 'Download URL ' . $i,
                        "nl-NL" => 'Download URL ' . $i,
                    ],
                ]),
            ]);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    public function down(Connection $connection, bool $keepUserData): void
    {
        if ($keepUserData) {
            return;
        }

        $connection->executeStatement('DELETE FROM `custom_field` WHERE `name` LIKE :customFieldNamePrefix', [
            'customFieldNamePrefix' => self::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME . '_url_%',
        ]);
    }
}
