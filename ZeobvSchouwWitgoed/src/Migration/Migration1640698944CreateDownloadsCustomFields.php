<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Zeobv\SchouwWitgoed\Migration\Step\PluginMigrationStep;

class Migration1640698944CreateDownloadsCustomFields extends PluginMigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1640698944;
    }

    public function update(Connection $connection): void
    {
        // Add custom field set for downloads
        $this->createCustomFieldSet($connection, [
            'id' => $customFieldSetId = Uuid::randomBytes(),
            'name' => self::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME,
            'active' => true,
            'config' => json_encode([
                "label" => [
                    "de-DE" => "Downloads",
                    "en-GB" => "Downloads",
                    "nl-NL" => "Downloads",
                ],
                "translated" => true,
            ]),
        ], [
            'product'
        ]);

        // Add 5 download custom fields
        for ($i=1; $i <= 5; $i++) {
            $this->createCustomField($connection, [
                'name' => self::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME . '_' . $i,
                'type' => 'media',
                'set_id' => $customFieldSetId,
                'active' => true,
                'config' => json_encode([
                    "componentName" => "sw-media-field",
                    "customFieldType" => "media",
                    "customFieldPosition" => $i * 10,
                    "label" => [
                        "de-DE" => 'Download ' . $i,
                        "en-GB" => 'Download ' . $i,
                        "nl-NL" => 'Download ' . $i,
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

        $customFieldSetName = self::PRODUCT_DOWNLOADS_CUSTOM_FIELD_SET_NAME;
        $connection->executeStatement('DELETE FROM `custom_field_set` WHERE `name` = :setName', [
            'setName' => $customFieldSetName,
        ]);
    }
}
