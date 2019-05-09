<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Routing;

use Maintainerati\Bikeshed\Controller\Admin\EditorController;
use Maintainerati\Bikeshed\Controller\Admin\OneTimeKeysController;
use Maintainerati\Bikeshed\Controller\AsyncFormController;
use Maintainerati\Bikeshed\Controller\FocusController;
use Maintainerati\Bikeshed\Controller\HomepageController;
use Maintainerati\Bikeshed\Controller\NoteController;
use Maintainerati\Bikeshed\Controller\ReFocusController;
use Maintainerati\Bikeshed\Controller\RegistrationController;
use Maintainerati\Bikeshed\Controller\SecurityController;
use Maintainerati\Bikeshed\Controller\SpaceController;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class BikeshedLoader extends Loader
{
    private const UUID_REGEX = '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}';
    private const ENTITY_REGEX = '(event|session|space|note)';

    private $isLoaded = false;

    public function load($resource, $type = null)
    {
        if ($this->isLoaded === true) {
            throw new \RuntimeException('Do not add the "bikeshed" loader twice');
        }

        $routes = new RouteCollection();

        $this->addHomeRoute($routes);
        $this->addSessionRoutes($routes);
        $this->addSecurityRoutes($routes);
        $this->addAdminRoutes($routes);

        // Async
        $path = '/async/form/{event}/{session}/{space}/{note}';
        $defaults = [
            '_controller' => AsyncFormController::class,
            '_format' => 'json',
            'event' => null,
            'session' => null,
            'space' => null,
            'note' => null,
        ];
        $requirements = [
            'event' => self::UUID_REGEX,
            'session' => self::UUID_REGEX,
            'space' => self::UUID_REGEX,
            'note' => self::UUID_REGEX,
        ];
        $route = new Route($path, $defaults, $requirements);
        $routes->add('bikeshed_async_form', $route);

        // Space
        $path = '/space';
        $defaults = ['_controller' => SpaceController::class];
        $route = new Route($path, $defaults, []);
        $routes->add('bikeshed_space', $route);

        // Note
        $path = '/note/{id}';
        $defaults = ['_controller' => NoteController::class];
        $requirements = [
            'id' => self::UUID_REGEX,
        ];
        $route = new Route($path, $defaults, $requirements);
        $routes->add('bikeshed_note', $route);

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'bikeshed';
    }

    private function addHomeRoute(RouteCollection $routes): void
    {
        // Homepage
        $path = '/';
        $defaults = ['_controller' => HomepageController::class];
        $route = new Route($path, $defaults, []);
        $routes->add('bikeshed_homepage', $route);
    }

    private function addSessionRoutes(RouteCollection $routes): void
    {
        // Focus
        $path = '/focus';
        $defaults = ['_controller' => FocusController::class];
        $route = new Route($path, $defaults, []);
        $routes->add('bikeshed_focus', $route);

        // ReFocus
        $path = '/refocus/{event}/{session}/{space}';
        $defaults = ['_controller' => ReFocusController::class];
        $requirements = [
            'event' => self::UUID_REGEX,
            'session' => self::UUID_REGEX,
            'space' => self::UUID_REGEX,
        ];
        $route = new Route($path, $defaults, $requirements);
        $routes->add('bikeshed_refocus', $route);
    }

    private function addSecurityRoutes(RouteCollection $routes): void
    {
        // Registration
        $path = '/register';
        $defaults = ['_controller' => RegistrationController::class];
        $route = new Route($path, $defaults, []);
        $routes->add('bikeshed_register', $route);

        // Login
        $path = '/login';
        $defaults = ['_controller' => SecurityController::class];
        $route = new Route($path, $defaults, []);
        $routes->add('bikeshed_login', $route);

        // Logout
        $path = '/logout';
        $route = new Route($path, [], []);
        $routes->add('bikeshed_logout', $route);
    }

    private function addAdminRoutes(RouteCollection $routes): void
    {
        // Editor
        $path = '/admin/edit/{type}/{id}';
        $defaults = ['_controller' => EditorController::class];
        $requirements = [
            'type' => self::ENTITY_REGEX,
            'id' => self::UUID_REGEX,
        ];
        $route = new Route($path, $defaults, $requirements);
        $routes->add('bikeshed_admin_editor', $route);

        // One-time keys
        $path = '/admin/one-time-keys';
        $defaults = ['_controller' => OneTimeKeysController::class];
        $route = new Route($path, $defaults, []);
        $routes->add('bikeshed_admin_one_time_keys', $route);
    }
}
