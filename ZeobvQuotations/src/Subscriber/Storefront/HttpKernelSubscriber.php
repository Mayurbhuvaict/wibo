<?php declare(strict_types=1);

namespace Zeobv\Quotations\Subscriber\Storefront;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Exception\CartTokenNotFoundException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\CartLineItemController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Shopware\Core\Checkout\Cart\CartPersisterInterface;
use Zeobv\Quotations\Service\QuotationService;
use Zeobv\Quotations\Struct\Quotation;

class HttpKernelSubscriber implements EventSubscriberInterface
{
    private CartPersisterInterface $cartPersister;
    private RequestStack $requestStack;

    public function __construct(
        CartPersisterInterface $cartPersister,
        RequestStack $requestStack
    )
    {
        $this->cartPersister = $cartPersister;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onControllerArguments',
        ];
    }

    public function onControllerArguments(ControllerArgumentsEvent $event)
    {
        if (
            is_array($event->getController())
            && $event->getController()[0] instanceof CartLineItemController
        ) {

            // I've created 'hooks' which I can use to manipulate the arguments passed to a certain action.
            // I use this to replace the actual shopping cart with a quotation
            [$controller, $action] = $event->getController();
            if (method_exists($this, $action . 'Hook')) {
                $this->{$action . 'Hook'}($event);
            }
        }
    }

    public function addLineItemsHook(ControllerArgumentsEvent $event)
    {
        /**
         * @param Cart $cart
         * @param RequestDataBag $requestDataBag
         * @param Request $request
         * @param SalesChannelContext $salesChannelContext
         */
        [$cart, $requestDataBag, $request, $salesChannelContext] = $event->getArguments();

        // If request was a quotation request, replace shopping cart argument with "quotation cart"
        if (!is_null($requestDataBag->get('zeobvAddToQuotation'))) {
            // Set in session that request was a quotation request so we can still identify that in later XHR requests (like loading the offcanvas cart)
            $this->requestStack->getSession()->set('zeobvQuotationRequest', true);
            $cart = $this->getQuotationCart($cart, $salesChannelContext);

            $event->setArguments([
                $cart,
                $requestDataBag,
                $request,
                $salesChannelContext
            ]);
        }
    }

    public function deleteLineItemHook(ControllerArgumentsEvent $event)
    {
        /**
         * @param Cart $cart
         * @param string $id
         * @param Request $request
         * @param SalesChannelContext $salesChannelContext
         */
        [$cart, $id, $request, $salesChannelContext] = $event->getArguments();

        // If request was a quotation request, replace shopping cart argument with "quotation cart"
        if (!is_null($request->request->get('zeobvRemoveFromQuotation'))) {
            $cart = $this->getQuotationCart($cart, $salesChannelContext);

            $event->setArguments([
                $cart,
                $id,
                $request,
                $salesChannelContext,
            ]);
        }
    }

    public function changeQuantityHook(ControllerArgumentsEvent $event)
    {
        /**
         * @param Cart $cart
         * @param string $id
         * @param Request $request
         * @param SalesChannelContext $salesChannelContext
         */
        [$cart, $id, $request, $salesChannelContext] = $event->getArguments();

        // If request was a quotation request, replace shopping cart argument with "quotation cart"
        if (!is_null($request->request->get('zeobvChangeFromQuotation'))) {
            $cart = $this->getQuotationCart($cart, $salesChannelContext);

            $event->setArguments([
                $cart,
                $id,
                $request,
                $salesChannelContext,
            ]);
        }
    }

    private function getQuotationCart(Cart $cart, SalesChannelContext $salesChannelContext)
    {
        $quotationToken = Quotation::QUOTATION_CART_TOKEN_PREFIX . $cart->getToken();

        try {
            $cart = $this->cartPersister->load($quotationToken, $salesChannelContext);
        } catch (CartTokenNotFoundException $exception) {
            $cart = new Cart('zeobv_quotation_cart', $quotationToken);
        }

        return $cart;
    }
}
