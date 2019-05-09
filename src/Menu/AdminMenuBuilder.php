<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final class AdminMenuBuilder
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

        $root->addChild('One-time Keys', ['route' => 'bikeshed_admin_one_time_keys']);

        return $root;
    }
}
