<?php declare(strict_types=1);

namespace Zeobv\Quotations\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Zeobv\Quotations\Migration\Step\PluginMigrationStep;

class Migration1626706978CreateQuoteTable extends PluginMigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1626706978;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("
            CREATE TABLE `zeobv_quote` (
                `id` BINARY(16) NOT NULL,
                `quote_number` VARCHAR(64) NOT NULL,
                `order_id` BINARY(16) NOT NULL,
                `order_version_id` BINARY(16) NOT NULL,
                `quotation_state_id` BINARY(16) NULL,
                `expiry_date` DATE NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                KEY `fk.zeobv_quote.quotation_state_id` (`quotation_state_id`),
                CONSTRAINT `fk.zeobv_quote.quotation_state_id` FOREIGN KEY (`quotation_state_id`) REFERENCES `state_machine_state` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                KEY `fk.zeobv_quote.order_id` (`order_id`),
                CONSTRAINT `fk.zeobv_quote.order_id` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
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

        $connection->executeStatement("DROP TABLE IF EXISTS `zeobv_quote`");
    }
}
