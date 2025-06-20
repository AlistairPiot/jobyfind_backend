<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SimpleTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    public function testHealthCheck(): void
    {
        // Création d'un client HTTP simulé
        $client = static::createClient();
        
        // Test d'un endpoint de santé
        $client->request('GET', '/api/health');
        
        // Vérifier que la réponse est un succès HTTP, entre 200 et 299 
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        // Vérifier que certaines clés existent dans la réponse
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('timestamp', $response);
        $this->assertArrayHasKey('version', $response);
        $this->assertEquals('ok', $response['status']);
    }
} 