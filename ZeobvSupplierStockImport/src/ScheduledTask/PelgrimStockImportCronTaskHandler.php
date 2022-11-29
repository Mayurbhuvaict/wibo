<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Zeobv\SupplierStockImport\Controller\PelgrimStockImportController;

class PelgrimStockImportCronTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;
    /**
     * @var PelgrimStockImportController
     */
    private $PelgrimStockImportController;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        PelgrimStockImportController $PelgrimStockImportController
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->PelgrimStockImportController = $PelgrimStockImportController;
    }
    public static function getHandledMessages(): iterable
    {
        return [ PelgrimStockImportCronTask::class ];
    }

    public function run(): void
    {
        file_put_contents("PelgrimStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> Start Cron Main Product Import\n",FILE_APPEND);
        $this->PelgrimStockImportController->pelgrimStockImport(Context::createDefaultContext());
        file_put_contents("PelgrimStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> End Cron Main Product Import\n",FILE_APPEND);
    }
}
