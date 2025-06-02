<?php

namespace App\Controller;

use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/payment', name: 'api_payment_')]
class PaymentController extends AbstractController
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    #[Route('/create-payment-intent', name: 'create_payment_intent', methods: ['POST'])]
    public function createPaymentIntent(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            // Validation des données
            if (!isset($data['amount']) || !isset($data['mission_name'])) {
                return new JsonResponse(['error' => 'Données manquantes'], 400);
            }

            // Créer un Customer Stripe
            $customer = $this->stripeService->createCustomer($data);

            // Créer un PaymentIntent
            $paymentIntent = $this->stripeService->createPaymentIntent($data, $customer->id);

            return new JsonResponse([
                'client_secret' => $paymentIntent->client_secret,
                'customer_id' => $customer->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'status' => $paymentIntent->status
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la création du PaymentIntent: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/confirm-payment', name: 'confirm_payment', methods: ['POST'])]
    public function confirmPayment(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['payment_intent_id'])) {
                return new JsonResponse(['error' => 'Payment Intent ID manquant'], 400);
            }

            // Récupérer le PaymentIntent pour vérifier son statut
            $paymentIntent = $this->stripeService->retrievePaymentIntent($data['payment_intent_id']);

            return new JsonResponse([
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'customer_id' => $paymentIntent->customer,
                'metadata' => $paymentIntent->metadata
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la confirmation du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/webhook', name: 'webhook', methods: ['POST'])]
    public function handleWebhook(Request $request): JsonResponse
    {
        // Endpoint pour les webhooks Stripe (optionnel pour l'instant)
        $payload = $request->getContent();
        $sig_header = $request->headers->get('stripe-signature');
        
        // TODO: Implémenter la vérification du webhook si nécessaire
        
        return new JsonResponse(['status' => 'success']);
    }
} 