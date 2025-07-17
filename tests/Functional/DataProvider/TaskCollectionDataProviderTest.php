<?php

namespace App\Tests\Functional\DataProvider;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskCollectionDataProviderTest extends WebTestCase
{
    public function testProvideAsAdmin()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['username' => 'test@admin.ru', 'password' => 'pass']),
        );

        $response = $client->getResponse();

        $content = $response->getContent();

        $token = json_decode($content, true)['token'];

        $client->setServerParameters(['HTTP_AUTHORIZATION' => 'Bearer '.$token]);

        $client->request('GET', '/api/tasks');

        $this->assertResponseIsSuccessful();
    }
}
