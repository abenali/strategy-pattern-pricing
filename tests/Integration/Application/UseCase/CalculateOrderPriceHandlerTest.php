<?php

declare(strict_types=1);

namespace App\Tests\Integration\Application\UseCase;

use App\Application\UseCase\CalculateOrderPrice\CalculateOrderPriceCommand;
use App\Application\UseCase\CalculateOrderPrice\CalculateOrderPriceHandler;
use App\Domain\Entity\Customer;
use App\Domain\Entity\Product;
use App\Domain\Entity\PromotionalEvent;
use App\Domain\Repository\CustomerRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\Repository\PromotionalEventRepositoryInterface;
use App\Domain\ValueObject\CustomerType;
use App\Infrastructure\Strategy\StrategyRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CalculateOrderPriceHandlerTest extends TestCase
{
    private CalculateOrderPriceHandler $handler;
    private CustomerRepositoryInterface&MockObject $customerRepository;
    private ProductRepositoryInterface&MockObject $productRepository;
    private PromotionalEventRepositoryInterface&MockObject $eventRepository;

    protected function setUp(): void
    {
        // Create mocks
        $this->customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->eventRepository = $this->createMock(PromotionalEventRepositoryInterface::class);
        $this->handler = $this->createHandler();
    }

    private function createHandler(?\DateTimeInterface $currentDate = null): CalculateOrderPriceHandler
    {
        $strategyRegistry = new StrategyRegistry($this->eventRepository, $currentDate);

        return new CalculateOrderPriceHandler(
            $this->customerRepository,
            $this->productRepository,
            $strategyRegistry
        );
    }

    public function testShouldCalculatePriceWithNoStrategy(): void
    {
        // Arrange
        $customer = new Customer('john@example.com', CustomerType::STANDARD, 0.0, 'customer-1');
        $product = new Product('Laptop', 1000.0, 'product-1');

        $this->customerRepository
            ->expects($this->once())
            ->method('findById')
            ->with('customer-1')
            ->willReturn($customer);

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with('product-1')
            ->willReturn($product);

        $command = new CalculateOrderPriceCommand(
            customerId: 'customer-1',
            items: [['productId' => 'product-1', 'quantity' => 1]],
            strategyCodes: []
        );

        // Act
        $response = $this->handler->execute($command);

        // Assert
        $this->assertEquals(1000.0, $response->subtotal);
        $this->assertEquals(1000.0, $response->total);
        $this->assertEmpty($response->appliedStrategies);
    }

    public function testShouldCalculatePriceWithVipStrategyOnly(): void
    {
        // Arrange
        $customer = new Customer('jane@example.com', CustomerType::VIP, 10000.0, 'customer-2');
        $product = new Product('Laptop', 1000.0, 'product-1');

        $this->customerRepository
            ->method('findById')
            ->willReturn($customer);

        $this->productRepository
            ->method('findById')
            ->willReturn($product);

        $command = new CalculateOrderPriceCommand(
            customerId: 'customer-2',
            items: [['productId' => 'product-1', 'quantity' => 1]],
            strategyCodes: ['vip']
        );

        // Act
        $response = $this->handler->execute($command);

        // Assert
        $this->assertEquals(1000.0, $response->subtotal);
        $this->assertEquals(850.0, $response->total);
        $this->assertCount(1, $response->appliedStrategies);
        $this->assertEquals('VIP', $response->appliedStrategies[0]['name']);
        $this->assertEquals(15, $response->appliedStrategies[0]['discount']);
        $this->assertEquals(850.0, $response->appliedStrategies[0]['amountAfter']);
    }

    public function testShouldCalculatePriceWithStudentStrategyOnly(): void
    {
        // Arrange
        $customer = new Customer('alice@university.edu', CustomerType::STUDENT, 500.0, 'customer-3');
        $product = new Product('Laptop', 500.0, 'product-2');

        $this->customerRepository
            ->method('findById')
            ->willReturn($customer);

        $this->productRepository
            ->method('findById')
            ->willReturn($product);

        $command = new CalculateOrderPriceCommand(
            customerId: 'customer-3',
            items: [['productId' => 'product-2', 'quantity' => 1]],
            strategyCodes: ['student']
        );

        // Act
        $response = $this->handler->execute($command);

        // Assert
        $this->assertEquals(500.0, $response->subtotal);
        $this->assertEquals(450.0, $response->total);
        $this->assertCount(1, $response->appliedStrategies);
        $this->assertEquals('Student', $response->appliedStrategies[0]['name']);
        $this->assertEquals(10, $response->appliedStrategies[0]['discount']);
    }

    public function testShouldApplyStrategiesSuccessively(): void
    {
        // Arrange - Simulate that we are DURING Black Friday
        $blackFridayDate = new \DateTime('2024-11-25'); // During Black Friday
        $this->handler = $this->createHandler($blackFridayDate);
        $customer = new Customer('vip@example.com', CustomerType::VIP, 15000.0, 'customer-4');
        $product = new Product('Laptop', 1000.0, 'product-1');

        $event = new PromotionalEvent(
            name: 'Black Friday',
            code: 'black-friday',
            discountPercentage: 25,
            startDate: new \DateTimeImmutable('2024-11-24'),
            endDate: new \DateTimeImmutable('2024-11-27')
        );

        $this->customerRepository
            ->method('findById')
            ->willReturn($customer);

        $this->productRepository
            ->method('findById')
            ->willReturn($product);

        $this->eventRepository
            ->method('findByCode')
            ->with('black-friday')
            ->willReturn($event);

        $command = new CalculateOrderPriceCommand(
            customerId: 'customer-4',
            items: [['productId' => 'product-1', 'quantity' => 1]],
            strategyCodes: ['vip', 'black-friday']
        );

        // Act
        $response = $this->handler->execute($command);

        // Assert
        $this->assertEquals(1000.0, $response->subtotal);
        // VIP: 1000 * 0.85 = 850
        // Black Friday: 850 * 0.75 = 637.50
        $this->assertEquals(637.50, $response->total);
        $this->assertCount(2, $response->appliedStrategies);

        // First strategy: VIP
        $this->assertEquals('VIP', $response->appliedStrategies[0]['name']);
        $this->assertEquals(850.0, $response->appliedStrategies[0]['amountAfter']);

        // Second strategy: Black Friday
        $this->assertEquals('Black Friday', $response->appliedStrategies[1]['name']);
        $this->assertEquals(637.50, $response->appliedStrategies[1]['amountAfter']);
    }

    public function testShouldCalculateMultipleItems(): void
    {
        // Arrange
        $customer = new Customer('john@example.com', CustomerType::STANDARD, 0.0, 'customer-1');
        $laptop = new Product('Laptop', 1000.0, 'product-1');
        $mouse = new Product('Mouse', 50.0, 'product-2');

        $this->customerRepository
            ->method('findById')
            ->willReturn($customer);

        $this->productRepository
            ->method('findById')
            ->willReturnMap([
                ['product-1', $laptop],
                ['product-2', $mouse],
            ]);

        $command = new CalculateOrderPriceCommand(
            customerId: 'customer-1',
            items: [
                ['productId' => 'product-1', 'quantity' => 2],
                ['productId' => 'product-2', 'quantity' => 1],
            ],
            strategyCodes: ['vip']
        );

        // Act
        $response = $this->handler->execute($command);

        // Assert
        // Subtotal: (1000 * 2) + (50 * 1) = 2050
        $this->assertEquals(2050.0, $response->subtotal);
        // VIP: 2050 * 0.85 = 1742.50
        $this->assertEquals(1742.50, $response->total);
    }

    public function testShouldThrowExceptionWhenOrderIsEmpty(): void
    {
        // Arrange
        $customer = new Customer('john@example.com', CustomerType::STANDARD, 0.0, 'customer-1');

        $this->customerRepository
            ->method('findById')
            ->willReturn($customer);

        $command = new CalculateOrderPriceCommand(
            customerId: 'customer-1',
            items: [],
            strategyCodes: []
        );

        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order cannot be empty');

        // Act
        $this->handler->execute($command);
    }
}
