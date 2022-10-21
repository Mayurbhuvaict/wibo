<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Storefront\Page\Product\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepositoryInterface;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductPageSubscriber implements EventSubscriberInterface
{
    /**
     * @var SalesChannelRepositoryInterface
     */
    private SalesChannelRepositoryInterface $repository;

    protected EntityRepositoryInterface $pageProductNumber;

    protected EntityRepositoryInterface $productRepository;

    public function __construct(
        SalesChannelRepositoryInterface $repository,
        EntityRepositoryInterface       $pageProductNumber,
        EntityRepositoryInterface       $productRepository
    )
    {
        $this->repository = $repository;
        $this->pageProductNumber = $pageProductNumber;
        $this->productRepository = $productRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded'
        ];
    }

    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext()->getContext();
        $criteriaData = new Criteria();
        $criteriaData->addAssociation('media');
        $criteriaData->addFilter(new EqualsFilter('productId', $event->getPage()->getProduct()->getId()));
        /* @var $productRepository EntityRepositoryInterface */
        $productDatas = $this->pageProductNumber->search($criteriaData, $context)->getEntities();
        $event->getPage()->getProduct()->addExtension('pageProduct', $productDatas);
//        dd($event->getPage()->getProduct());
    }
}
