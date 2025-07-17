<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiLoginControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function testLoginFail(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testLoginSuccess(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['username' => 'test@user.ru', 'password' => 'pass']),
        );

        $this->assertResponseIsSuccessful();
    }
}
