<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Twig\Extension;

use Symfony\Component\Intl\Countries;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class CountryExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('country', [$this, 'getCountryName']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('country', [$this, 'getCountryName']),
            new TwigFunction('counties', [$this, 'getCountryNames']),
        ];
    }

    public function getCountryName($country): string
    {
        return Countries::getName($country);
    }

    public function getCountryNames(): array
    {
        return Countries::getNames();
    }
}
