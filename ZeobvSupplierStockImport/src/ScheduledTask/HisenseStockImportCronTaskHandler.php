<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Zeobv\SupplierStockImport\Controller\HisenseStockImportController;

class HisenseStockImportCronTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;
    /**
     * @var HisenseStockImportController
     */
    private $HisenseStockImportController;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        HisenseStockImportController $HisenseStockImportController
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->HisenseStockImportController = $HisenseStockImportController;
    }
    public static function getHandledMessages(): iterable
    {
        return [ HisenseStockImportCronTask::class ];
    }

    public function run(): void
    {
        file_put_contents(
            "HisenseStockImportControllerLog.txt",
            date("l jS \of F Y h:i:sA")."> Start Cron Main Product Import\n",
            FILE_APPEND
        );
        $this->HisenseStockImportController->hisenseStockImport(Context::createDefaultContext());
        file_put_contents(
            "HisenseStockImportControllerLog.txt",
            date("l jS \of F Y h:i:sA")."> End Cron Main Product Import\n",
            FILE_APPEND
        );
    }
}
