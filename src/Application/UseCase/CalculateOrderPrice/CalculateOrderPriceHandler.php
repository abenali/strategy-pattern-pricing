<?php

declare(strict_types=1);

namespace App\Application\UseCase\CalculateOrderPrice;

use App\Domain\Entity\Order;
use App\Domain\Entity\OrderItem;
use App\Domain\Repository\CustomerRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Strategy\StrategyRegistry;

final class CalculateOrderPriceHandler
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StrategyRegistry $strategyRegistry,
    ) {
    }

    public function execute(CalculateOrderPriceCommand $command): CalculateOrderPriceResponse
    {
        // 1. Retrieve customer
        $customer = $this->customerRepository->findById($command->customerId);

        // 2. Build the order
        $order = new Order($customer);

        foreach ($command->items as $itemData) {
            $product = $this->productRepository->findById($itemData['productId']);
            $orderItem = new OrderItem(
                product: $product,
                quantity: $itemData['quantity'],
                unitPrice: $product->getPrice()
            );
            $order->addItem($orderItem);
        }

        // 3. Validate order is not empty
        if ($order->isEmpty()) {
            throw new \InvalidArgumentException('Order cannot be empty');
        }

        // 4. Calculate subtotal
        $subtotal = $order->getSubtotal();

        // 5. Apply strategies successively
        $currentAmount = $subtotal;
        $appliedStrategies = [];

        foreach ($command->strategyCodes as $code) {
            $strategy = $this->strategyRegistry->get($code);

            $amountBefore = $currentAmount;
            $currentAmount = $strategy->calculate($currentAmount);

            $appliedStrategies[] = [
                'name' => $strategy->getName(),
                'discount' => $strategy->getDiscountPercentage(),
                'amountAfter' => round($currentAmount, 2),
            ];
        }

        // 6. Return response
        return new CalculateOrderPriceResponse(
            subtotal: round($subtotal, 2),
            total: round($currentAmount, 2),
            appliedStrategies: $appliedStrategies
        );
    }
}
