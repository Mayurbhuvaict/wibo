<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Zeobv\SupplierStockImport\Controller\InventumStockImportController;

class InventumStockImportCronTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;
    /**
     * @var InventumStockImportController
     */
    private $InventumStockImportController;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        InventumStockImportController $InventumStockImportController
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->InventumStockImportController = $InventumStockImportController;
    }
    public static function getHandledMessages(): iterable
    {
        return [ InventumStockImportCronTask::class ];
    }

    public function run(): void
    {
        $cron = 1;
        file_put_contents("InventumStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> Start Cron Main Product Import\n",FILE_APPEND);
        $this->InventumStockImportController->inventumStockImport($cron, Context::createDefaultContext());
        file_put_contents("InventumStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> End Cron Main Product Import\n",FILE_APPEND);
    }
}
