<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Zeobv\SupplierStockImport\Controller\AskoStockImportController;

class AskoStockImportCronTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;
    /**
     * @var AskoStockImportController
     */
    private $AskoStockImportController;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        AskoStockImportController $AskoStockImportController
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->AskoStockImportController = $AskoStockImportController;
    }
    public static function getHandledMessages(): iterable
    {
        return [ AskoStockImportCronTask::class ];
    }

    public function run(): void
    {
        file_put_contents(
            "AskoStockImportControllerLog.txt",
            date("l jS \of F Y h:i:sA")."> Start Cron Main Product Import\n",
            FILE_APPEND
        );
        $this->AskoStockImportController->askoStockImport(Context::createDefaultContext());
        file_put_contents(
            "AskoStockImportControllerLog.txt",
            date("l jS \of F Y h:i:sA")."> End Cron Main Product Import\n",
            FILE_APPEND
        );
    }
}
