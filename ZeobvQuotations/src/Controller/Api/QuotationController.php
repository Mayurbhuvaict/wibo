<?php declare(strict_types=1);

namespace Zeobv\Quotations\Controller\Api;

use Shopware\Core\Framework\Api\Converter\ApiVersionConverter;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\StateMachine\StateMachineDefinition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;

/**
 * @RouteScope(scopes={"api"})
 */
class QuotationController extends AbstractController
{
    private ApiVersionConverter $apiVersionConverter;
    private StateMachineDefinition $stateMachineDefinition;

    public function __construct(
        ApiVersionConverter $apiVersionConverter,
        StateMachineDefinition $stateMachineDefinition
    )
    {
        $this->apiVersionConverter = $apiVersionConverter;
        $this->stateMachineDefinition = $stateMachineDefinition;
    }

    /**
     * @Route("/api/_action/zeobv_quote/{quotationId}/state/{actionName}",
     *         defaults={"auth_enabled"=true}, name="api.action.zeo.quotation.state_transition", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function transitionQuotationState(Request $request, Context $context, string $quotationId, string $actionName): JsonResponse
    {
        $quotationService = $this->container->get('Zeobv\Quotations\Service\QuotationService');

        try {
            $newState = $quotationService->transitionQuotationState($request, $context, $quotationId, $actionName);
        } catch (\Throwable $exception) {
            return new JsonResponse(
                ['success' => false, 'error' => $exception->getMessage()],
                500
            );
        }

        return new JsonResponse($this->apiVersionConverter->convertEntity(
            $this->stateMachineDefinition,
            $newState
        ));
    }
}
