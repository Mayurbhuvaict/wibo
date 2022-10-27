<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1665466581 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1665466581;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("
                            CREATE TABLE IF NOT EXISTS `product_page` (
                            `id` BINARY(16) NOT NULL,
                            `media_id` BINARY(16) NULL,
                            `product_id` BINARY(16) NULL,
                            `product_version_id` BINARY(16) NULL,
                            `created_at` DATETIME(3) NOT NULL,
                            `updated_at` DATETIME(3) NULL,
                            PRIMARY KEY (`id`),
                            CONSTRAINT `json.product_page.translated` CHECK (JSON_VALID(`translated`)),
                            KEY `fk.product_page.media_id` (`media_id`),
                            CONSTRAINT `fk.product_page.media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                                ");

        $connection->executeStatement("
                                CREATE TABLE IF NOT EXISTS `product_page_translation` (
                                `main_title` VARCHAR(255) NOT NULL,
                                `main_description` VARCHAR(255) NULL,
                                `sub_title_one` VARCHAR(255) NULL,
                                `sub_description_one` VARCHAR(255) NULL,
                                `sub_title_two` VARCHAR(255) NULL,
                                `sub_description_two` VARCHAR(255) NULL,
                                `sub_title_three` VARCHAR(255) NULL,
                                `sub_description_three` VARCHAR(255) NULL,
                                `sub_title_four` VARCHAR(255) NULL,
                                `sub_description_four` VARCHAR(255) NULL,
                                `sub_title_five` VARCHAR(255) NULL,
                                `sub_description_five` VARCHAR(255) NULL,
                                `sub_title_six` VARCHAR(255) NULL,
                                `sub_description_six` VARCHAR(255) NULL,
                                `sub_title_seven` VARCHAR(255) NULL,
                                `sub_description_seven` VARCHAR(255) NULL,
                                `sub_title_eight` VARCHAR(255) NULL,
                                `sub_description_eight` VARCHAR(255) NULL,
                                `created_at` DATETIME(3) NOT NULL,
                                `updated_at` DATETIME(3) NULL,
                                `product_page_id` BINARY(16) NOT NULL,
                                `language_id` BINARY(16) NOT NULL,
                                PRIMARY KEY (`product_page_id`,`language_id`),
                                KEY `fk.product_page_translation.product_page_id` (`product_page_id`),
                                KEY `fk.product_page_translation.language_id` (`language_id`),
                                CONSTRAINT `fk.product_page_translation.product_page_id` FOREIGN KEY (`product_page_id`) REFERENCES `product_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                CONSTRAINT `fk.product_page_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                            ");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
