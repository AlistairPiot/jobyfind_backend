<?php

namespace App\Tests\Entity;

use App\Entity\RequestBadge;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RequestBadgeTest extends TestCase
{
    public function testSetRequestDate()
    {
        $requestBadge = new RequestBadge();
        $requestDate = new \DateTimeImmutable('2024-01-01 10:00:00');
        
        $requestBadge->setRequestDate($requestDate);
        
        $this->assertEquals($requestDate, $requestBadge->getRequestDate());
    }

    public function testSetStatus()
    {
        $requestBadge = new RequestBadge();
        
        $requestBadge->setStatus('PENDING');
        
        $this->assertEquals('PENDING', $requestBadge->getStatus());
    }

    public function testSetUserAndSchool()
    {
        $requestBadge = new RequestBadge();
        $user = new User();
        $school = new User();

        $requestBadge->setUser($user);
        $requestBadge->setSchool($school);

        $this->assertEquals($user, $requestBadge->getUser());
        $this->assertEquals($school, $requestBadge->getSchool());
    }
} 