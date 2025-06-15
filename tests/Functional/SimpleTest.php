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
        $client = static::createClient();
        
        // Test d'un endpoint de santé
        $client->request('GET', '/api/health');
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('timestamp', $response);
        $this->assertArrayHasKey('version', $response);
        $this->assertEquals('ok', $response['status']);
    }
} 