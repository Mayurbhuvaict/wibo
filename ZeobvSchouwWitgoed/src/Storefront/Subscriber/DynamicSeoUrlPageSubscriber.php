<?php declare(strict_types=1);

namespace Zeobv\SchouwWitgoed\Storefront\Subscriber;

use Shopware\Core\Content\Media\MediaEvents;
use Shopware\Core\Content\Seo\SeoUrlUpdater;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\Event\ShopwareEvent;
use Zeobv\SchouwWitgoed\Storefront\Framework\Seo\SeoUrlRoute\PdfSeoUrlRoute;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DynamicSeoUrlPageSubscriber implements EventSubscriberInterface
{
    private SeoUrlUpdater $seoUrlUpdater;

    public function __construct(
        SeoUrlUpdater $seoUrlUpdater
    ) {
        $this->seoUrlUpdater = $seoUrlUpdater;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MediaEvents::MEDIA_WRITTEN_EVENT => 'onMediaEntityWritten',
            MediaEvents::MEDIA_DELETED_EVENT => 'onMediaEntityDeleted',
        ];
    }

    public function onMediaEntityWritten(EntityWrittenEvent $event): void
    {
        $this->updateSeoUrls($event);
    }

    public function onMediaEntityDeleted(EntityWrittenEvent $event): void
    {
        $this->updateSeoUrls($event);
    }

    /** @param EntityWrittenEvent|EntityDeletedEvent $event */
    protected function updateSeoUrls(ShopwareEvent $event)
    {
        $this->seoUrlUpdater->update(PdfSeoUrlRoute::ROUTE_NAME, $event->getIds());
    }
}
