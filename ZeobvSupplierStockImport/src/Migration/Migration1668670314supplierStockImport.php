<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1668670314supplierStockImport extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1668670314;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('CREATE TABLE `supplier_stock_import` (
    `id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `product_version_id` BINARY(16) NOT NULL,
    `api_record` JSON NOT NULL,
    `extra_field` VARCHAR(255) NULL,
    `last_usage_at` DATETIME(3) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `json.supplier_stock_import.api_record` CHECK (JSON_VALID(`api_record`)),
    KEY `fk.supplier_stock_import.product_id` (`product_id`,`product_version_id`),
    CONSTRAINT `fk.supplier_stock_import.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
