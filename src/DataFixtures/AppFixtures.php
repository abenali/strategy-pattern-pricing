<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\Customer;
use App\Domain\Entity\Product;
use App\Domain\Entity\PromotionalEvent;
use App\Domain\ValueObject\CustomerType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ========== CUSTOMERS ==========

        // Standard Customer
        $standardCustomer = new Customer(
            email: 'john.doe@example.com',
            type: CustomerType::STANDARD,
            totalPurchases: 1200.00
        );
        $manager->persist($standardCustomer);
        $this->addReference('customer-standard', $standardCustomer);

        // VIP Customer (> 5000€)
        $vipCustomer = new Customer(
            email: 'jane.smith@example.com',
            type: CustomerType::VIP,
            totalPurchases: 12500.00
        );
        $manager->persist($vipCustomer);
        $this->addReference('customer-vip', $vipCustomer);

        // Student Customer
        $studentCustomer = new Customer(
            email: 'alice.student@university.edu',
            type: CustomerType::STUDENT,
            totalPurchases: 450.00
        );
        $manager->persist($studentCustomer);
        $this->addReference('customer-student', $studentCustomer);

        // Another Standard
        $customer4 = new Customer(
            email: 'bob.martin@example.com',
            type: CustomerType::STANDARD,
            totalPurchases: 800.00
        );
        $manager->persist($customer4);

        // ========== PRODUCTS ==========

        $laptop = new Product(
            name: 'MacBook Pro 14" M3',
            price: 2499.00
        );
        $manager->persist($laptop);
        $this->addReference('product-laptop', $laptop);

        $smartphone = new Product(
            name: 'iPhone 15 Pro',
            price: 1199.00
        );
        $manager->persist($smartphone);
        $this->addReference('product-smartphone', $smartphone);

        $headphones = new Product(
            name: 'AirPods Pro 2',
            price: 279.00
        );
        $manager->persist($headphones);
        $this->addReference('product-headphones', $headphones);

        $mouse = new Product(
            name: 'Magic Mouse',
            price: 99.00
        );
        $manager->persist($mouse);
        $this->addReference('product-mouse', $mouse);

        $keyboard = new Product(
            name: 'Magic Keyboard',
            price: 149.00
        );
        $manager->persist($keyboard);
        $this->addReference('product-keyboard', $keyboard);

        $monitor = new Product(
            name: 'Dell UltraSharp 27"',
            price: 549.00
        );
        $manager->persist($monitor);

        $webcam = new Product(
            name: 'Logitech Brio 4K',
            price: 199.00
        );
        $manager->persist($webcam);

        // ========== PROMOTIONAL EVENTS ==========

        // Black Friday (fin novembre)
        $blackFriday = new PromotionalEvent(
            name: 'Black Friday 2024',
            code: 'black-friday',
            discountPercentage: 25,
            startDate: new \DateTimeImmutable('2024-11-24'),
            endDate: new \DateTimeImmutable('2024-11-27')
        );
        $manager->persist($blackFriday);
        $this->addReference('event-black-friday', $blackFriday);

        // Summer Sale (juillet-août)
        $summerSale = new PromotionalEvent(
            name: 'Summer Sale 2024',
            code: 'summer-sale',
            discountPercentage: 20,
            startDate: new \DateTimeImmutable('2024-07-01'),
            endDate: new \DateTimeImmutable('2024-08-31')
        );
        $manager->persist($summerSale);
        $this->addReference('event-summer-sale', $summerSale);

        // Cyber Monday
        $cyberMonday = new PromotionalEvent(
            name: 'Cyber Monday 2024',
            code: 'cyber-monday',
            discountPercentage: 30,
            startDate: new \DateTimeImmutable('2024-11-30'),
            endDate: new \DateTimeImmutable('2024-12-02')
        );
        $manager->persist($cyberMonday);

        // Event passé (pour tester qu'il ne s'applique pas)
        $pastEvent = new PromotionalEvent(
            name: 'Spring Sale 2024',
            code: 'spring-sale',
            discountPercentage: 15,
            startDate: new \DateTimeImmutable('2024-03-01'),
            endDate: new \DateTimeImmutable('2024-03-31')
        );
        $manager->persist($pastEvent);

        $manager->flush();
    }
}
