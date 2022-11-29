<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Zeobv\SupplierStockImport\Controller\AtagStockImportController;

class AtagStockImportCronTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;
    /**
     * @var AtagStockImportController
     */
    private $AtagStockImportController;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        AtagStockImportController $AtagStockImportController
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->AtagStockImportController = $AtagStockImportController;
    }
    public static function getHandledMessages(): iterable
    {
        return [ AtagStockImportCronTask::class ];
    }

    public function run(): void
    {
        file_put_contents("AtagStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> Start Cron Main Product Import\n",FILE_APPEND);
        $this->AtagStockImportController->atagStockImport(Context::createDefaultContext());
        file_put_contents("AtagStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> End Cron Main Product Import\n",FILE_APPEND);
    }
}
