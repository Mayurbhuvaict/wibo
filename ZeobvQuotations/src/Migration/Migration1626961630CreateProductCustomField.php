<?php declare(strict_types=1);

namespace Zeobv\Quotations\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Zeobv\Quotations\Migration\Step\PluginMigrationStep;

class Migration1626961630CreateProductCustomField extends PluginMigrationStep
{
    const CUSTOM_FIELD_SET_PRODUCT_QUOTATION = 'zeobv_quotations';

    public function getCreationTimestamp(): int
    {
        return 1626961630;
    }

    public function update(Connection $connection): void
    {
        if (
            $connection->executeQuery(
                "SELECT `id` FROM `custom_field_set` WHERE `name` = ?",
                [self::CUSTOM_FIELD_SET_PRODUCT_QUOTATION]
            )->rowCount() > 0
        ) {
            return;
        }

        $this->createCustomFieldSet($connection, [
            'id' => $setId = Uuid::randomBytes(),
            'name' => self::CUSTOM_FIELD_SET_PRODUCT_QUOTATION,
            'active' => 1,
            'config' => json_encode([
                'label' => [
                    'en-GB' => 'Quotation',
                    'de-DE' => 'Angebot',
                    'nl-NL' => 'Offerte',
                ],
            ]),
        ], ['product']);

        $this->createCustomField($connection, [
            'set_id' => $setId,
            'name' => 'zeobv_quotations_can_request_quote',
            'active' => 1,
            'type' => 'bool',
            'config' => json_encode([
                'label' => [
                    'en-GB' => 'Quotation request available',
                    'de-DE' => 'Angebotsanfrage verfügbar',
                    'nl-NL' => 'Offerte aanvraag beschikbaar',
                ],
                "type" => "switch",
                "componentName" => "sw-field",
                "customFieldType" => "switch",
                "customFieldPosition" => 10,
            ])
        ]);
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

        $connection->executeStatement(
            "DELETE FROM `custom_field_set` WHERE `name` = ?",
            [self::CUSTOM_FIELD_SET_PRODUCT_QUOTATION]
        );
    }
}
