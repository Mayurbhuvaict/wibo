<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1668670314supplierStockImport extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1668670314;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->executeStatement('CREATE TABLE `supplier_stock_import` (
    `id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `product_version_id` BINARY(16) NOT NULL,
    `ean_number` VARCHAR(255) NULL,
    `atag_api_record` LONGTEXT NULL,
    `etna_api_record` LONGTEXT NULL,
    `pelgrim_api_record` LONGTEXT NULL,
    `hisense_api_record` LONGTEXT NULL,
    `asko_api_record` LONGTEXT NULL,
    `amacom_api_record` LONGTEXT NULL,
    `boretti_api_record` LONGTEXT NULL,
    `inventum_api_record` LONGTEXT NULL,
    `smeg_api_record` LONGTEXT NULL,
    `last_usage_at` DATETIME(3) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    KEY `fk.supplier_stock_import.product_id` (`product_id`,`product_version_id`),
    CONSTRAINT `fk.supplier_stock_import.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
        } catch (Exception $e) {
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
