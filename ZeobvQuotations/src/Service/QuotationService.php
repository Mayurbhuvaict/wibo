<?php


namespace Zeobv\Quotations\Service;


use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\NumberRange\ValueGenerator\NumberRangeValueGeneratorInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateEntity;
use Shopware\Core\System\StateMachine\Exception\StateMachineStateNotFoundException;
use Shopware\Core\System\StateMachine\StateMachineRegistry;
use Shopware\Core\System\StateMachine\Transition;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Zeobv\Quotations\Core\Content\Quote\QuoteDefinition;
use Zeobv\Quotations\Core\StateMachine\QuotationStates;
use Zeobv\Quotations\Struct\Quotation;

class QuotationService
{
    protected ConfigService $configService;
    protected EntityRepository $quoteRepository;
    protected NumberRangeValueGeneratorInterface $numberRangeValueGenerator;
    protected StateMachineRegistry $stateMachineRegistry;

    public function __construct(
        ConfigService $configService,
        EntityRepository $zeobvQuoteRepository,
        NumberRangeValueGeneratorInterface $numberRangeValueGenerator,
        StateMachineRegistry $stateMachineRegistry
    )
    {
        $this->configService = $configService;
        $this->quoteRepository = $zeobvQuoteRepository;
        $this->numberRangeValueGenerator = $numberRangeValueGenerator;
        $this->stateMachineRegistry = $stateMachineRegistry;
    }

    public function finalizeQuotation(Request $request, Context $context)
    {
        // TODO: Mark quotation as 'definitive'
        // TODO: Generate PDF
        // TODO: Notify the customer
    }

    public function transitionQuotationState(Request $request, Context $context, string $quotationId, string $actionName): StateMachineStateEntity
    {
        return $this->quotationStateTransition($quotationId, $actionName, $request->request, $context);
    }

    public function acceptQuotation(string $quotationId, SalesChannelContext $context)
    {
        // TODO: Accept the quotation on behalf of the customer
    }

    public function declineQuotation(string $quotationId, SalesChannelContext $context)
    {
        // TODO: Decline the quotation on behalf of the customer
    }

    public function markQuotationAsExpired(string $quotationId, SalesChannelContext $context)
    {
        // TODO: Decline the quotation on behalf of the admin (marking it as expired)
        // TODO: Notify customer
    }

    public function createQuotationForOrder(OrderEntity $order, SalesChannelContext $salesChannelContext)
    {
        $quoteNumber = $this->numberRangeValueGenerator->getValue(
            QuoteDefinition::ENTITY_NAME,
            $salesChannelContext->getContext(),
            $salesChannelContext->getSalesChannel()->getId()
        );

        $this->quoteRepository->create([
            [
                'quoteNumber' => $quoteNumber,
                'orderId' => $order->getId(),
                'quotationStateId' => $this->stateMachineRegistry->getInitialState(QuotationStates::STATE_MACHINE, $salesChannelContext->getContext())->getId(),
            ]
        ], $salesChannelContext->getContext());
    }

    public function getQuotationContext(SalesChannelContext $salesChannelContext)
    {
        return new SalesChannelContext(
            $salesChannelContext->getContext(),
            Quotation::QUOTATION_CART_TOKEN_PREFIX . $salesChannelContext->getToken(),
            $salesChannelContext->getDomainId(),
            $salesChannelContext->getSalesChannel(),
            $salesChannelContext->getCurrency(),
            $salesChannelContext->getCurrentCustomerGroup(),
            $salesChannelContext->getCurrentCustomerGroup(),
            $salesChannelContext->getTaxRules(),
            $salesChannelContext->getPaymentMethod(),
            $salesChannelContext->getShippingMethod(),
            $salesChannelContext->getShippingLocation(),
            $salesChannelContext->getCustomer(),
            $salesChannelContext->getItemRounding(),
            $salesChannelContext->getTotalRounding(),
            $salesChannelContext->getRuleIds()
        );
    }

    protected function quotationStateTransition(
        string $quotationId,
        string $transition,
        ParameterBag $data,
        Context $context
    ): StateMachineStateEntity {
        $stateFieldName = $data->get('stateFieldName', 'quotationStateId');

        $stateMachineStates = $this->stateMachineRegistry->transition(
            new Transition(
                'zeobv_quote',
                $quotationId,
                $transition,
                $stateFieldName
            ),
            $context
        );

        $toPlace = $stateMachineStates->get('toPlace');

        if (!$toPlace) {
            throw new StateMachineStateNotFoundException('order_transaction', $transition);
        }

        return $toPlace;
    }
}
