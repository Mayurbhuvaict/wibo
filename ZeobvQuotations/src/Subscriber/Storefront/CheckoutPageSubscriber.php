<?php declare(strict_types=1);

namespace Zeobv\Quotations\Subscriber\Storefront;

use Shopware\Core\Checkout\Cart\Exception\CartTokenNotFoundException;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoadedEvent;
use Shopware\Storefront\Page\PageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Checkout\Cart\CartPersisterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zeobv\Quotations\Struct\Quotation;

class CheckoutPageSubscriber implements EventSubscriberInterface
{
    private CartPersisterInterface $cartPersister;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;

    public function __construct(
        CartPersisterInterface $cartPersister,
        RequestStack $requestStack,
        TranslatorInterface $translator
    )
    {
        $this->cartPersister = $cartPersister;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            CheckoutCartPageLoadedEvent::class => 'onCartPageLoaded',

            OffcanvasCartPageLoadedEvent::class => 'onOffcanvasCartPageLoaded',
        ];
    }

    public function onCartPageLoaded(PageLoadedEvent $event)
    {
        try {
            $quotationCart = $this->cartPersister->load(Quotation::QUOTATION_CART_TOKEN_PREFIX . $event->getPage()->getCart()->getToken(), $event->getSalesChannelContext());
        } catch (CartTokenNotFoundException $exception) {
            $quotationCart = null;
        }

        if ($quotationCart) {
            $event->getPage()->assign(['zeobvQuotationCart' => $quotationCart]);
        }
    }

    public function onOffcanvasCartPageLoaded(OffcanvasCartPageLoadedEvent $event)
    {
        $this->convertFlashMessage($event);

        $this->onCartPageLoaded($event);
    }

    protected function convertFlashMessage(OffcanvasCartPageLoadedEvent $event)
    {
        if (!$this->requestStack->getSession()->get('zeobvQuotationRequest', false)) {
            return;
        }
        $this->requestStack->getSession()->set('zeobvQuotationRequest', false);

        $this->requestStack->getSession()->getFlashBag()->set('success', $this->translator->trans('zeoQuotations.checkout.addToQuotationSuccess'));
    }
}
