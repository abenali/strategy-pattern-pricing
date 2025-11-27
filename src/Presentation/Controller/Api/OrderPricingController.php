<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Api;

use App\Application\UseCase\CalculateOrderPrice\CalculateOrderPriceCommand;
use App\Application\UseCase\CalculateOrderPrice\CalculateOrderPriceHandler;
use App\Infrastructure\Strategy\StrategyNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/orders', name: 'api_orders_')]
final class OrderPricingController extends AbstractController
{
    public function __construct(
        private readonly CalculateOrderPriceHandler $handler,
    ) {
    }

    #[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculatePrice(Request $request): JsonResponse
    {
        try {
            // 1. Parse request body
            $data = json_decode($request->getContent(), true);

            if (!is_array($data)) {
                return $this->json(
                    ['error' => 'Invalid JSON'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            // 2. Validate required fields
            if (!isset($data['customerId']) || !isset($data['items'])) {
                return $this->json(
                    ['error' => 'Missing required fields: customerId, items'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            // 3. Create command
            $command = new CalculateOrderPriceCommand(
                customerId: $data['customerId'],
                items: $data['items'],
                strategyCodes: $data['strategies'] ?? []
            );

            // 4. Execute use case
            $response = $this->handler->execute($command);

            // 5. Return response
            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (StrategyNotFoundException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\InvalidArgumentException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\RuntimeException $e) {
            // Customer not found, Product not found, etc.
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => 'Internal server error: '.$e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
