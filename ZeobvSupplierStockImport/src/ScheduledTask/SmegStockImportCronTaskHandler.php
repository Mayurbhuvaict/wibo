<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Zeobv\SupplierStockImport\Controller\SmegStockImportController;

class SmegStockImportCronTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;
    /**
     * @var SmegStockImportController
     */
    private $SmegStockImportController;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        SmegStockImportController $SmegStockImportController
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->SmegStockImportController = $SmegStockImportController;
    }
    public static function getHandledMessages(): iterable
    {
        return [ SmegStockImportCronTask::class ];
    }

    public function run(): void
    {
        $cron = 1;
        file_put_contents("SmegStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> Start Cron Main Product Import\n",FILE_APPEND);
        $this->SmegStockImportController->smegStockImport($cron,Context::createDefaultContext());
        file_put_contents("SmegStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> End Cron Main Product Import\n",FILE_APPEND);
    }
}
