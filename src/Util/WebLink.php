<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Util;

use Fig\Link\GenericLinkProvider;
use Fig\Link\Link;
use Symfony\Component\HttpFoundation\RequestStack;

final class WebLink
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Adds a "Link" HTTP header.
     *
     * @param string $uri        The relation URI
     * @param string $rel        The relation type (e.g. "preload", "prefetch", "prerender" or "dns-prefetch")
     * @param array  $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The relation URI
     */
    public function link(string $uri, string $rel, array $attributes = []): string
    {
        if (!$request = $this->requestStack->getMasterRequest()) {
            return $uri;
        }

        $link = new Link($rel, $uri);
        foreach ($attributes as $key => $value) {
            $link = $link->withAttribute($key, $value);
        }

        $linkProvider = $request->attributes->get('_links', new GenericLinkProvider());
        $request->attributes->set('_links', $linkProvider->withLink($link));

        return $uri;
    }

    /**
     * Preloads a resource.
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "['as' => true]", "['crossorigin' => 'use-credentials']")
     *
     * @return string The path of the asset
     */
    public function preload(string $uri, array $attributes = []): string
    {
        return $this->link($uri, 'preload', $attributes);
    }

    /**
     * Resolves a resource origin as early as possible.
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The path of the asset
     */
    public function dnsPrefetch(string $uri, array $attributes = []): string
    {
        return $this->link($uri, 'dns-prefetch', $attributes);
    }

    /**
     * Initiates a early connection to a resource (DNS resolution, TCP handshake, TLS negotiation).
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The path of the asset
     */
    public function preconnect(string $uri, array $attributes = []): string
    {
        return $this->link($uri, 'preconnect', $attributes);
    }

    /**
     * Indicates to the client that it should prefetch this resource.
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The path of the asset
     */
    public function prefetch(string $uri, array $attributes = []): string
    {
        return $this->link($uri, 'prefetch', $attributes);
    }

    /**
     * Indicates to the client that it should prerender this resource .
     *
     * @param string $uri        A public path
     * @param array  $attributes The attributes of this link (e.g. "['as' => true]", "['pr' => 0.5]")
     *
     * @return string The path of the asset
     */
    public function prerender(string $uri, array $attributes = []): string
    {
        return $this->link($uri, 'prerender', $attributes);
    }
}
