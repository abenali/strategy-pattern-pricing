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
            'customerId' => '3515465c-3c4d-44c8-a26c-6e3be8476a3c',
            'items' => [
                ['productId' => 'd5394396-0fd0-4951-afc3-13e5a91ff69d', 'quantity' => 2],
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
            'customerId' => '3beedbda-cf5d-488b-b445-cdcf1a92281a',
            'items' => [
                ['productId' => 'd5394396-0fd0-4951-afc3-13e5a91ff69d', 'quantity' => 2],
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
            'customerId' => '3beedbda-cf5d-488b-b445-cdcf1a92281a',
            'items' => [
                ['productId' => 'd5394396-0fd0-4951-afc3-13e5a91ff69d', 'quantity' => 2],
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
