<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class SmegStockImportCronTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'supplier_stock.smeg_import';
    }

    public static function getDefaultInterval(): int
    {
        return 300; // 5 minutes
    }
}
