<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class InventumStockImportCronTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'supplier_stock.inventum_import';
    }

    public static function getDefaultInterval(): int
    {
        return 86400; // 5 minutes
    }
}
