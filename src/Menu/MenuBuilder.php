<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final class MenuBuilder
{
    /** @var FactoryInterface */
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMenu(array $options): ItemInterface
    {
        $root = $this->factory->createItem('root', ['currentClass' => 'is-active']);

        $root->addChild('Home', ['route' => 'bikeshed_homepage']);
        $root->addChild('Find a Space', ['route' => 'bikeshed_focus']);
        $root->addChild('My Current Discussion', ['route' => 'bikeshed_space']);

        return $root;
    }
}
