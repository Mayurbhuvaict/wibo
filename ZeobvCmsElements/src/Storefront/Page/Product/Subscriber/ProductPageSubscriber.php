<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Storefront\Page\Product\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;
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

    protected EntityRepositoryInterface $propertyGroupRepository;

    public function __construct(
        SalesChannelRepositoryInterface $repository,
        EntityRepositoryInterface       $pageProductNumber,
        EntityRepositoryInterface       $productRepository,
        EntityRepositoryInterface       $propertyGroupRepository
    )
    {
        $this->repository = $repository;
        $this->pageProductNumber = $pageProductNumber;
        $this->productRepository = $productRepository;
        $this->propertyGroupRepository = $propertyGroupRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
            ProductEvents::PRODUCT_LISTING_RESULT => 'onProductLoaded'
        ];
    }

    public function onProductLoaded(ProductListingResultEvent $event): void
    {
        $context = $event->getSalesChannelContext()->getContext();
        foreach ($event->getResult()->getElements() as $element) {
            $propertyGroup = array();
            if (array_key_exists('product_property_group', $element->getCustomFields())) {
                foreach ($element->getCustomFields()['product_property_group'] as $v) {
                    $criteria = new Criteria();
                    $criteria->addAssociation('property_group_translation');
                    $criteria->addFilter(new EqualsFilter('id', $v));

                    $propertyGroup[] = $this->propertyGroupRepository->search($criteria, $context)->getEntities()->getElements();
                }
            }
            $element->addExtension('propertyGroup', new ArrayStruct([$propertyGroup]));
//            dd($element);
        }
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
