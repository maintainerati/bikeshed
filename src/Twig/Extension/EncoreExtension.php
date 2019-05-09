<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Twig\Extension;

use Maintainerati\Bikeshed\Asset\TagRenderer;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EncoreExtension extends AbstractExtension
{
    private $entrypointLookup;
    private $tagRenderer;

    public function __construct(EntrypointLookupCollectionInterface $entrypointLookup, TagRenderer $tagRenderer)
    {
        $this->entrypointLookup = $entrypointLookup;
        $this->tagRenderer = $tagRenderer;
    }

    public function getFunctions(): array
    {
        return [
            //new TwigFunction('encore_entry_script_tags', [$this, 'renderWebpackScriptTags'], ['is_safe' => ['html']]),
            //new TwigFunction('encore_entry_link_tags', [$this, 'renderWebpackLinkTags'], ['is_safe' => ['html']]),
            //
            //new TwigFunction('encore_entry_preload_script_tags', [$this, 'renderPreloadWebpackScriptTags'], ['is_safe' => ['html']]),
            //new TwigFunction('encore_entry_preload_link_tags', [$this, 'renderPreloadWebpackLinkTags'], ['is_safe' => ['html']]),

            new TwigFunction('encore_entry_reset', [$this, 'resetService']),
        ];
    }

    public function renderWebpackScriptTags(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default'
    ): string {
        return $this->tagRenderer->renderWebpackScriptTags($entryName, $packageName, $entrypointName);
    }

    public function renderWebpackLinkTags(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default'
    ): string {
        return $this->tagRenderer->renderWebpackLinkTags($entryName, $packageName, $entrypointName);
    }

    public function renderPreloadWebpackScriptTags(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default'
    ): string {
        return $this->tagRenderer->renderPreloadWebpackScriptTags($entryName, $packageName, $entrypointName);
    }

    public function renderPreloadWebpackLinkTags(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default'
    ): string {
        return $this->tagRenderer->renderPreloadWebpackLinkTags($entryName, $packageName, $entrypointName);
    }

    public function resetService(string $entrypointName = '_default'): void
    {
        $this->entrypointLookup->getEntrypointLookup($entrypointName)->reset();
    }
}
