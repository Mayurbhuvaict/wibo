<?php declare(strict_types=1);

namespace Zeobv\SupplierStockImport\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Zeobv\SupplierStockImport\Controller\EtnaStockImportController;

class EtnaStockImportCronTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;
    /**
     * @var EtnaStockImportController
     */
    private $EtnaStockImportController;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        EtnaStockImportController $EtnaStockImportController
    ) {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->EtnaStockImportController = $EtnaStockImportController;
    }
    public static function getHandledMessages(): iterable
    {
        return [ EtnaStockImportCronTask::class ];
    }

    public function run(): void
    {
        file_put_contents("EtnaStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> Start Cron Main Product Import\n",FILE_APPEND);
        $this->EtnaStockImportController->etnaStockImport(Context::createDefaultContext());
        file_put_contents("EtnaStockImportControllerLog.txt",date("l jS \of F Y h:i:sA")."> End Cron Main Product Import\n",FILE_APPEND);
    }
}
