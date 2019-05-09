<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Asset;

use Symfony\Component\Asset\Packages;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Symfony\WebpackEncoreBundle\Asset\IntegrityDataProviderInterface;

final class TagRenderer
{
    private $entrypointLookupCollection;
    private $packages;
    /** @var string[] */
    private $defaultAttributes;

    public function __construct(
        EntrypointLookupCollectionInterface $entrypointLookupCollection,
        Packages $packages,
        array $defaultAttributes = []
    ) {
        $this->entrypointLookupCollection = $entrypointLookupCollection;
        $this->packages = $packages;
        $this->defaultAttributes = $defaultAttributes;
    }

    public function renderPreloadWebpackScriptTags(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default'
    ): string {
        $scriptTags = [];
        $entryPointLookup = $this->getEntrypointLookup($entrypointName);
        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];

        foreach ($entryPointLookup->getJavaScriptFiles($entryName) as $filename) {
            $assetPath = $this->getAssetPath($filename, $packageName);
            $attributes = $this->defaultAttributes;
            $attributes['as'] = 'script';
            $attributes['href'] = $assetPath;
            $attributes['crossorigin'] = 'anonymous';

            if (isset($integrityHashes[$filename])) {
                $attributes['integrity'] = $integrityHashes[$filename];
            }
            $scriptTags[] = sprintf('<link rel="preload" %s>', $this->convertArrayToAttributes($attributes));
        }

        return '    ' . implode(\PHP_EOL . '    ', $scriptTags);
    }

    public function renderPreloadWebpackLinkTags(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default'
    ): string {
        $scriptTags = [];
        $entryPointLookup = $this->getEntrypointLookup($entrypointName);
        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];

        foreach ($entryPointLookup->getCssFiles($entryName) as $filename) {
            $attributes = $this->defaultAttributes;
            $attributes['as'] = 'style';
            $attributes['href'] = $this->getAssetPath($filename, $packageName);
            $attributes['crossorigin'] = 'anonymous';

            if (isset($integrityHashes[$filename])) {
                $attributes['integrity'] = $integrityHashes[$filename];
            }
            $scriptTags[] = sprintf(
                '<link rel="preload" %s onload="%s">',
                $this->convertArrayToAttributes($attributes),
                "this.onload=null;this.rel='stylesheet';"
            );
            unset($attributes['as']);
            $scriptTags[] = sprintf(
                '<noscript><link rel="stylesheet" %s></noscript>',
                $this->convertArrayToAttributes($attributes)
            );
        }

        return '    ' . implode(\PHP_EOL . '    ', $scriptTags);
    }

    public function renderWebpackScriptTags(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default'
    ): string {
        $scriptTags = [];
        $entryPointLookup = $this->getEntrypointLookup($entrypointName);
        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];

        foreach ($entryPointLookup->getJavaScriptFiles($entryName) as $filename) {
            $attributes = $this->defaultAttributes;
            $attributes['src'] = $this->getAssetPath($filename, $packageName);
            $attributes['crossorigin'] = 'anonymous';
            $attributes['async'] = 'true';

            if (isset($integrityHashes[$filename])) {
                $attributes['integrity'] = $integrityHashes[$filename];
            }

            $scriptTags[] = sprintf('<script %s></script>', $this->convertArrayToAttributes($attributes));
        }

        return '    ' . implode(\PHP_EOL . '    ', $scriptTags);
    }

    public function renderWebpackLinkTags(
        string $entryName,
        string $packageName = null,
        string $entrypointName = '_default'
    ): string {
        $scriptTags = [];
        $entryPointLookup = $this->getEntrypointLookup($entrypointName);
        $integrityHashes = ($entryPointLookup instanceof IntegrityDataProviderInterface) ? $entryPointLookup->getIntegrityData() : [];

        foreach ($entryPointLookup->getCssFiles($entryName) as $filename) {
            $attributes = $this->defaultAttributes;
            $attributes['rel'] = 'stylesheet';
            $attributes['href'] = $this->getAssetPath($filename, $packageName);
            $attributes['crossorigin'] = 'anonymous';

            if (isset($integrityHashes[$filename])) {
                $attributes['integrity'] = $integrityHashes[$filename];
            }

            $scriptTags[] = sprintf('<link %s>', $this->convertArrayToAttributes($attributes));
        }

        return '    ' . implode(\PHP_EOL . '    ', $scriptTags);
    }

    private function getAssetPath(string $assetPath, string $packageName = null): string
    {
        return $this->packages->getUrl(
            $assetPath,
            $packageName
        );
    }

    private function getEntrypointLookup(string $buildName): EntrypointLookupInterface
    {
        return $this->entrypointLookupCollection->getEntrypointLookup($buildName);
    }

    private function convertArrayToAttributes(array $attributesMap): string
    {
        return implode(' ', array_map(
            function ($key, $value) {
                return sprintf('%s="%s"', $key, htmlentities($value));
            },
            array_keys($attributesMap),
            $attributesMap
        ));
    }
}
