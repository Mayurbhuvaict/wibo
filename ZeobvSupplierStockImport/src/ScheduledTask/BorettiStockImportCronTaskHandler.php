<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Zeobv\SupplierStockImport\Controller\BorettiStockImportController;

class BorettiStockImportCronTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;
    /**
     * @var BorettiStockImportController
     */
    private $BorettiStockImportController;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        BorettiStockImportController $BorettiStockImportController
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->BorettiStockImportController = $BorettiStockImportController;
    }
    public static function getHandledMessages(): iterable
    {
        return [ BorettiStockImportCronTask::class ];
    }

    public function run(): void
    {
        file_put_contents(
            "BorettiStockImportControllerLog.txt",
            date("l jS \of F Y h:i:sA")."> Start Cron Main Product Import\n",
            FILE_APPEND
        );
        $this->BorettiStockImportController->borettiStockImport(Context::createDefaultContext());
        file_put_contents(
            "BorettiStockImportControllerLog.txt",
            date("l jS \of F Y h:i:sA")."> End Cron Main Product Import\n",
            FILE_APPEND
        );
    }
}
