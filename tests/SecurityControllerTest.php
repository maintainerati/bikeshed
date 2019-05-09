<?php

declare(strict_types=1);

namespace Maintainerti\Bikeshed\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $this->markTestIncomplete();

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Sign in', $crawler->filter('h1')->text());
    }
}
