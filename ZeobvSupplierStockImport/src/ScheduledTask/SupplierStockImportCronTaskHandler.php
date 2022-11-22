<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class SupplierStockImportCronTaskHandler extends ScheduledTaskHandler
{
    public static function getHandledMessages(): iterable
    {
        return [ SupplierStockImportCronTask::class ];
    }

    public function run(): void
    {
        echo 'Do stuff!';
    }
}
