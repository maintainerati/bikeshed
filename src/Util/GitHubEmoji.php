<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Util;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GitHubEmoji
{
    /** @var CacheItemPoolInterface */
    private $cache;
    /** @var HttpClientInterface */
    private $client;

    public function __construct(CacheItemPoolInterface $cache, HttpClientInterface $client = null)
    {
        $this->cache = $cache;
        $this->client = $client ?: HttpClient::create();
    }

    public function getJson(): string
    {
        return $this->load();
    }

    private function load(): string
    {
        $item = $this->cache->getItem('github_emoji');
        if (!$item->isHit()) {
            $item->set($this->fetch());
            $item->expiresAfter(86400);
            $this->cache->save($item);
        }

        return $item->get();
    }

    private function fetch(): string
    {
        $response = $this->client->request(Request::METHOD_GET, 'https://api.github.com/emojis');

        return $response->getContent(false);
    }
}
