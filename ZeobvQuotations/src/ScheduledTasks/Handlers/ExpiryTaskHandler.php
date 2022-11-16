<?php declare(strict_types=1);

namespace Zeobv\GetNotified\ScheduledTasks\Handlers\StockSubscriber;

use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Throwable;
use Psr\Log\LoggerInterface;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Zeobv\GetNotified\ScheduledTasks\Tasks\StockSubscriber\ExpiryTask;

class ExpiryTaskHandler extends ScheduledTaskHandler
{
    /** @var string */
    protected $environment;

    /** @var LoggerInterface */
    protected $logger;

    /** @var AbstractSalesChannelContextFactory */
    protected $salesChannelContextFactory;

    /**
     * CollectTaskHandler constructor.
     *
     * @param string                           $environment
     * @param LoggerInterface                  $logger
     */
    public function __construct(
        string $environment,
        EntityRepositoryInterface $scheduledTaskRepository,
        LoggerInterface $logger,
        AbstractSalesChannelContextFactory $salesChannelContextFactory
    )
    {
        $this->environment = $environment;
        $this->logger = $logger;
        $this->salesChannelContextFactory = $salesChannelContextFactory;

        parent::__construct($scheduledTaskRepository);
    }

    /**
     * @return iterable
     */
    public static function getHandledMessages(): iterable
    {
        return [ExpiryTask::class];
    }

    /**
     * @throws Throwable
     */
    public function run(): void
    {
        try {
            // ToDo: cancel all expired quotations
        } catch (Throwable $e) {
            // catch exception - otherwise the task will never be called again
            $this->logOrThrowException($e);
        }
    }

    /**
     * @param string $salesChannelId
     * @param array  $options
     *
     * @return SalesChannelContext
     */
    protected function getSalesChannelContext(string $salesChannelId, array $options = []): SalesChannelContext
    {
        return $this->salesChannelContextFactory->create(Uuid::randomHex(), $salesChannelId, $options);
    }

    /**
     * @param Throwable $exception
     *
     * @return false
     * @throws Throwable
     */
    protected function logOrThrowException(Throwable $exception): bool
    {
        if ($this->isTestEnv()) {
            throw $exception;
        }

        $this->logger->critical($exception->getMessage());

        return false;
    }

    /**
     * @return bool
     */
    protected function isTestEnv(): bool
    {
        return $this->environment === 'test' || $this->environment === 'dev';
    }
}
