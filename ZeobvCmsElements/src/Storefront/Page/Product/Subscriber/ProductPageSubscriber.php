<?php declare(strict_types=1);

namespace Zeobv\CmsElements\Storefront\Page\Product\Subscriber;

<<<<<<< HEAD
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
=======
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;
>>>>>>> 7a0b0e32bfc639e0e01a992b360889bb29e3002c
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

<<<<<<< HEAD
    public function __construct(
        SalesChannelRepositoryInterface $repository,
        EntityRepositoryInterface       $pageProductNumber,
        EntityRepositoryInterface       $productRepository
=======
    protected EntityRepositoryInterface $propertyGroupRepository;

    public function __construct(
        SalesChannelRepositoryInterface $repository,
        EntityRepositoryInterface       $pageProductNumber,
        EntityRepositoryInterface       $productRepository,
        EntityRepositoryInterface       $propertyGroupRepository
>>>>>>> 7a0b0e32bfc639e0e01a992b360889bb29e3002c
    )
    {
        $this->repository = $repository;
        $this->pageProductNumber = $pageProductNumber;
        $this->productRepository = $productRepository;
<<<<<<< HEAD
=======
        $this->propertyGroupRepository = $propertyGroupRepository;
>>>>>>> 7a0b0e32bfc639e0e01a992b360889bb29e3002c
    }

    public static function getSubscribedEvents(): array
    {
        return [
<<<<<<< HEAD
            ProductPageLoadedEvent::class => 'onProductPageLoaded'
        ];
    }

=======
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
        }
    }

>>>>>>> 7a0b0e32bfc639e0e01a992b360889bb29e3002c
    public function onProductPageLoaded(ProductPageLoadedEvent $event): void
    {
        $context = $event->getSalesChannelContext()->getContext();
        $criteriaData = new Criteria();
        $criteriaData->addAssociation('media');
        $criteriaData->addFilter(new EqualsFilter('productId', $event->getPage()->getProduct()->getId()));
        /* @var $productRepository EntityRepositoryInterface */
        $productDatas = $this->pageProductNumber->search($criteriaData, $context)->getEntities();
        $event->getPage()->getProduct()->addExtension('pageProduct', $productDatas);
<<<<<<< HEAD
//        dd($event->getPage()->getProduct());
=======
>>>>>>> 7a0b0e32bfc639e0e01a992b360889bb29e3002c
    }
}
