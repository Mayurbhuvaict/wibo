<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Zeobv\SchouwWitgoed\Storefront\Framework\Seo\SeoUrlRoute\PdfSeoUrlRoute;

class Migration1661159949AddMediaSeoUrlTemplate extends Step\PluginMigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1661159949;
    }

    public function update(Connection $connection): void
    {
        $connection->insert('seo_url_template', [
            'id' => Uuid::randomBytes(),
            'sales_channel_id' => null,
            'route_name' => PdfSeoUrlRoute::ROUTE_NAME,
            'entity_name' => 'media',
            'template' => PdfSeoUrlRoute::DEFAULT_TEMPLATE,
            'created_at' => (new \DateTimeImmutable())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    public function down(Connection $connection, bool $keepUserData): void
    {
        $connection->delete('seo_url_template', [
            'entity_name' => 'media',
            'route_name' => PdfSeoUrlRoute::ROUTE_NAME,
        ]);
    }
}
