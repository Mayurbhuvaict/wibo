<?php declare(strict_types=1);

namespace Zeobv\Quotations\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Zeobv\Quotations\Core\Content\Quote\QuoteDefinition;
use Zeobv\Quotations\Migration\Step\PluginMigrationStep;

class Migration1627308262CreateNewNumberRange extends PluginMigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1627308262;
    }

    public function update(Connection $connection): void
    {
        $this->insert($connection, 'number_range_type', [
            'id' => $rangeTypeId = Uuid::randomBytes(),
            'global' => 0,
            'technical_name' => QuoteDefinition::ENTITY_NAME,
        ]);

        $this->insert($connection, 'number_range', [
            'id' => $rangeId = Uuid::randomBytes(),
            'type_id' => $rangeTypeId,
            'global' => 1,
            'pattern' => '{n}',
            'start' => '10000',
        ]);

        $this->insertTranslations($connection, 'number_range_type_translation', [
            'nl-NL' => [
                [
                    'number_range_type_id' => $rangeTypeId,
                    'type_name' => 'Offerte',
                ]
            ],
            'en-GB' => [
                [
                    'number_range_type_id' => $rangeTypeId,
                    'type_name' => 'Quotation',
                ]
            ],
            'de-DE' => [
                [
                    'number_range_type_id' => $rangeTypeId,
                    'type_name' => 'Angebot',
                ]
            ],
        ]);

        $this->insertTranslations($connection, 'number_range_translation', [
            'nl-NL' => [
                [
                    'number_range_id' => $rangeId,
                    'name' => 'Offerte',
                ]
            ],
            'en-GB' => [
                [
                    'number_range_id' => $rangeId,
                    'name' => 'Quotation',
                ]
            ],
            'de-DE' => [
                [
                    'number_range_id' => $rangeId,
                    'name' => 'Angebot',
                ]
            ],
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    public function down(Connection $connection, bool $keepUserData): void
    {
        $connection->executeStatement("DELETE number_range, number_range_type FROM number_range_type LEFT JOIN number_range ON number_range_type.id = number_range.type_id WHERE technical_name = ?", [
            QuoteDefinition::ENTITY_NAME
        ]);
    }
}
