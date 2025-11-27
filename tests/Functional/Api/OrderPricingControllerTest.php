<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderPricingControllerTest extends WebTestCase
{
    public function testCalculatePrice(): void
    {
        $client = static::createClient();

        $data = [
            'customerId' => 'f3bbc09f-7160-4b5a-ac50-e67ff736275c',
            'items' => [
                ['productId' => 'cbd32b35-ac2c-4405-a135-837902d54516', 'quantity' => 2],
            ],
        ];

        $client->request(
            'POST',
            '/api/orders/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('subtotal', $responseData);
        $this->assertArrayHasKey('total', $responseData);
        $this->assertArrayHasKey('appliedStrategies', $responseData);
        $this->assertEmpty($responseData['appliedStrategies']);
    }

    public function testCalculatePriceWithVipDiscount(): void
    {
        $client = static::createClient();

        $data = [
            'customerId' => '624a5d62-f7ad-46a2-be6b-8695e857aebe',
            'items' => [
                ['productId' => '656608b3-420b-4973-b0cc-fc04e2a773d6', 'quantity' => 2],
            ],
            'strategies' => ['vip'],
        ];

        $client->request(
            'POST',
            '/api/orders/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('subtotal', $responseData);
        $this->assertArrayHasKey('total', $responseData);
        $this->assertArrayHasKey('appliedStrategies', $responseData);
        $this->assertCount(1, $responseData['appliedStrategies']);
    }

    public function testCalculatePriceWithInvalidStrategy(): void
    {
        $client = static::createClient();

        $data = [
            'customerId' => 'd0c284c8-02fd-4c5b-994a-f66822e165d3',
            'items' => [
                ['productId' => '5ea229d3-6065-4b85-8f38-424465f7aac3', 'quantity' => 2],
            ],
            'strategies' => ['INVALID_STRATEGY'],
        ];

        $client->request(
            'POST',
            '/api/orders/calculate-price',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
