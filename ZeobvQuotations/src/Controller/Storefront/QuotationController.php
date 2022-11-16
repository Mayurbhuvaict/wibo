<?php declare(strict_types=1);

namespace Zeobv\Quotations\Controller\Storefront;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartPersisterInterface;
use Shopware\Core\Checkout\Cart\Exception\CartTokenNotFoundException;
use Shopware\Core\Checkout\Cart\SalesChannel\AbstractCartOrderRoute;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Storefront\Controller\CartLineItemController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Zeobv\Quotations\Service\QuotationService;
use Zeobv\Quotations\Struct\Quotation;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class QuotationController extends StorefrontController
{
    private CartPersisterInterface $cartPersister;

    private QuotationService $quotationService;

    private AbstractCartOrderRoute $orderRoute;

    /** @var CartLineItemController */
    private StorefrontController $cartLineItemController;

    public function __construct(
        CartPersisterInterface $cartPersister,
        QuotationService $quotationService,
        AbstractCartOrderRoute $orderRoute,
        StorefrontController $cartLineItemController
    )
    {
        $this->quotationService = $quotationService;
        $this->cartPersister = $cartPersister;
        $this->orderRoute = $orderRoute;
        $this->cartLineItemController = $cartLineItemController;
    }

    /**
     * @Route("/zeobv/quotation/", name="frontend.zeobv.quotation.show", options={"seo"="false"},
     *                                     methods={"GET"})
     *
     * @param Request             $request
     * @param Context             $context
     * @param SalesChannelContext $salesChannelContext
     */
    public function show(Request $request, Context $context, SalesChannelContext $salesChannelContext)
    {
        try {
            $quotationCart = $this->cartPersister->load(Quotation::QUOTATION_CART_TOKEN_PREFIX . $salesChannelContext->getToken(), $salesChannelContext);
        } catch (CartTokenNotFoundException $exception) {
            $quotationCart = null;
        }

        return $this->renderStorefront('@ZeobvQuotations/zeobvQuotations/page/checkout/quotation-overview.html.twig', [
            'page' => [
                'zeobvQuotationCart' => $quotationCart
            ],
            'controllerAction' => 'cartPage'
        ]);
    }

    /**
     * @Route("/zeobv/quotation/request", name="frontend.zeobv.quotation.request", options={"seo"="false"},
     *                                     methods={"GET","POST"})
     *
     * @param Request             $request
     * @param Context             $context
     * @param SalesChannelContext $salesChannelContext
     */
    public function request(Request $request, RequestDataBag $requestDataBag, Context $context, SalesChannelContext $salesChannelContext)
    {
        if ($request->isMethod(Request::METHOD_GET)) {
            return new RedirectResponse($this->generateUrl("frontend.zeobv.quotation.show"), Response::HTTP_FOUND);
        }

        $quotationCart = $this->cartPersister->load(Quotation::QUOTATION_CART_TOKEN_PREFIX . $salesChannelContext->getToken(), $salesChannelContext);
        $quotationContext = $this->quotationService->getQuotationContext($salesChannelContext);

        $cartOrder = $this->orderRoute->order($quotationCart, $quotationContext, $requestDataBag);

        $this->quotationService->createQuotationForOrder($cartOrder->getOrder(), $salesChannelContext);

        $this->addFlash('success', 'Quotation requested successfully');
        $request->request->set('redirectTo', '');
        return $this->createActionResponse($request);
    }

    /**
     * @Route("/zeobv/quotation/line-item/delete/{id}", name="frontend.zeobv.quotation.line-item.delete", defaults={"XmlHttpRequest": true}, methods={"POST"})
     *
     * @param Request             $request
     * @param Context             $context
     * @param SalesChannelContext $salesChannelContext
     */
    public function remove(string $id, Request $request, SalesChannelContext $salesChannelContext)
    {
        $quotationCart = $this->cartPersister->load(Quotation::QUOTATION_CART_TOKEN_PREFIX . $salesChannelContext->getToken(), $salesChannelContext);

        return $this->cartLineItemController->deleteLineItem($quotationCart, $id, $request, $salesChannelContext);
    }
}
