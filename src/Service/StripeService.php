<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;

class StripeService
{
    private string $secretKey;

    public function __construct()
    {
        // 🔑 Configuration Stripe avec variable d'environnement
        $this->secretKey = $_ENV['STRIPE_SECRET_KEY'] ?? throw new \Exception('STRIPE_SECRET_KEY non configurée');
        
        Stripe::setApiKey($this->secretKey);
    }

    public function createCustomer(array $data): Customer
    {
        return Customer::create([
            'email' => $data['email'] ?? 'candidat@jobyfind.com',
            'name' => $data['name'] ?? 'Candidat JobyFind',
            'description' => 'Candidature pour mission: ' . $data['mission_name'],
            'metadata' => [
                'mission_name' => $data['mission_name'],
                'mission_id' => $data['mission_id'] ?? 'unknown',
                'source' => 'JobyFind_Application'
            ]
        ]);
    }

    public function createPaymentIntent(array $data, string $customerId): PaymentIntent
    {
        return PaymentIntent::create([
            'amount' => $data['amount'], // Montant en centimes
            'currency' => 'eur',
            'customer' => $customerId,
            'description' => 'Frais de candidature JobyFind - ' . $data['mission_name'],
            'metadata' => [
                'mission_name' => $data['mission_name'],
                'mission_id' => $data['mission_id'] ?? 'unknown',
                'customer_name' => $data['name'] ?? 'Candidat JobyFind',
                'source' => 'JobyFind_Application'
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return PaymentIntent::retrieve($paymentIntentId);
    }
} 