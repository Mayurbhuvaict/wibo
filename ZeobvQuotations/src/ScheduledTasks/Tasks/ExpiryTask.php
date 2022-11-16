<?php declare(strict_types=1);

namespace Zeobv\GetNotified\ScheduledTasks\Tasks\StockSubscriber;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class ExpiryTask extends ScheduledTask
{
    /**
     * @return string
     */
    public static function getTaskName(): string
    {
        return 'zeo_quotations.cancel_expired_quotations';
    }

    /**
     * @return int
     */
    public static function getDefaultInterval(): int
    {
        return 86400; # once a day
    }
}
