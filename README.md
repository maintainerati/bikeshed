Maintainerati Bikeshed
======================

Requirements
------------

  - PHP 7.2+ with the following extensions: 
    - `intl`
    - `json`
    - `pdo`
  - Composer 1.5+
  - Yarn 1.10+
  - NodeJS 10+

Installation
------------

### Option A — Site project (recommended)

Change to the base of where you want to create the new site install and run:

```bash
composer create-project maintainerati/bikeshed-skeleton my-bikeshed-site
```

### Option B — Symfony Bundle (existing project)

From the project root, run:

```bash
composer require  maintainerati/bikeshed-bundle
```

### Option C — Standalone library (existing project)

From the project root, run:

```bash
composer require  maintainerati/bikeshed
```

Configuration
-------------

### Routes

**NOTE:** If you are **not** using the skeleton or the bundle the routes are automatically configured.

You need to configure the following named routes:

| Property     | Value                                                             |
|--------------|-------------------------------------------------------------------|
| Route Name   | bikeshed_homepage                                                 |
| Path         | /                                                                 |
| Path Regex   | #^/$#sDu                                                          |
| Host         | ANY                                                               |
| Host Regex   |                                                                   |
| Scheme       | ANY                                                               |
| Method       | ANY                                                               |
| Requirements | NO CUSTOM                                                         |
| Class        | Symfony\Component\Routing\Route                                   |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\HomepageController |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler           |
|              | utf8: true                                                        |

| Property     | Value                                                          |
|--------------|----------------------------------------------------------------|
| Route Name   | bikeshed_focus                                                 |
| Path         | /focus                                                         |
| Path Regex   | #^/focus$#sDu                                                  |
| Host         | ANY                                                            |
| Host Regex   |                                                                |
| Scheme       | ANY                                                            |
| Method       | ANY                                                            |
| Requirements | NO CUSTOM                                                      |
| Class        | Symfony\Component\Routing\Route                                |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\FocusController |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler        |
|              | utf8: true                                                     |

| Property     | Value                                                                                                                                           |
|--------------|-------------------------------------------------------------------------------------------------------------------------------------------------|
| Route Name   | bikeshed_refocus                                                                                                                                |
| Path         | /refocus/{event}/{session}/{space}                                                                                                              |
| Path Regex   | #^/refocus/(?P<event>\w{8}-\w{4}-\w{4}-\w{4}-\w{12})/(?P<session>\w{8}-\w{4}-\w{4}-\w{4}-\w{12})/(?P<space>\w{8}-\w{4}-\w{4}-\w{4}-\w{12})$#sDu |
| Host         | ANY                                                                                                                                             |
| Host Regex   |                                                                                                                                                 |
| Scheme       | ANY                                                                                                                                             |
| Method       | ANY                                                                                                                                             |
| Requirements | event: \w{8}-\w{4}-\w{4}-\w{4}-\w{12}                                                                                                           |
|              | session: \w{8}-\w{4}-\w{4}-\w{4}-\w{12}                                                                                                         |
|              | space: \w{8}-\w{4}-\w{4}-\w{4}-\w{12}                                                                                                           |
| Class        | Symfony\Component\Routing\Route                                                                                                                 |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\ReFocusController                                                                                |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler                                                                                         |
|              | utf8: true                                                                                                                                      |

| Property     | Value                                                                 |
|--------------|-----------------------------------------------------------------------|
| Route Name   | bikeshed_register                                                     |
| Path         | /register                                                             |
| Path Regex   | #^/register$#sDu                                                      |
| Host         | ANY                                                                   |
| Host Regex   |                                                                       |
| Scheme       | ANY                                                                   |
| Method       | ANY                                                                   |
| Requirements | NO CUSTOM                                                             |
| Class        | Symfony\Component\Routing\Route                                       |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\RegistrationController |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler               |
|              | utf8: true                                                            |

| Property     | Value                                                             |
|--------------|-------------------------------------------------------------------|
| Route Name   | bikeshed_login                                                    |
| Path         | /login                                                            |
| Path Regex   | #^/login$#sDu                                                     |
| Host         | ANY                                                               |
| Host Regex   |                                                                   |
| Scheme       | ANY                                                               |
| Method       | ANY                                                               |
| Requirements | NO CUSTOM                                                         |
| Class        | Symfony\Component\Routing\Route                                   |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\SecurityController |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler           |
|              | utf8: true                                                        |

| Property     | Value                                                   |
|--------------|---------------------------------------------------------|
| Route Name   | bikeshed_logout                                         |
| Path         | /logout                                                 |
| Path Regex   | #^/logout$#sDu                                          |
| Host         | ANY                                                     |
| Host Regex   |                                                         |
| Scheme       | ANY                                                     |
| Method       | ANY                                                     |
| Requirements | NO CUSTOM                                               |
| Class        | Symfony\Component\Routing\Route                         |
| Defaults     | NONE                                                    |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler |
|              | utf8: true                                              |

| Property     | Value                                                                                            |
|--------------|--------------------------------------------------------------------------------------------------|
| Route Name   | bikeshed_admin_editor                                                                            |
| Path         | /admin/edit/{type}/{id}                                                                          |
| Path Regex   | #^/admin/edit/(?P<type>(?:event|session|space|note))/(?P<id>\w{8}-\w{4}-\w{4}-\w{4}-\w{12})$#sDu |
| Host         | ANY                                                                                              |
| Host Regex   |                                                                                                  |
| Scheme       | ANY                                                                                              |
| Method       | ANY                                                                                              |
| Requirements | id: \w{8}-\w{4}-\w{4}-\w{4}-\w{12}                                                               |
|              | type: (event|session|space|note)                                                                 |
| Class        | Symfony\Component\Routing\Route                                                                  |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\Admin\EditorController                            |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler                                          |
|              | utf8: true                                                                                       |

| Property     | Value                                                                      |
|--------------|----------------------------------------------------------------------------|
| Route Name   | bikeshed_admin_one_time_keys                                               |
| Path         | /admin/one-time-keys                                                       |
| Path Regex   | #^/admin/one\-time\-keys$#sDu                                              |
| Host         | ANY                                                                        |
| Host Regex   |                                                                            |
| Scheme       | ANY                                                                        |
| Method       | ANY                                                                        |
| Requirements | NO CUSTOM                                                                  |
| Class        | Symfony\Component\Routing\Route                                            |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\Admin\OneTimeKeysController |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler                    |
|              | utf8: true                                                                 |

| Property     | Value                                                                                                                                                                                                           |
|--------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Route Name   | bikeshed_async_form                                                                                                                                                                                             |
| Path         | /async/form/{event}/{session}/{space}/{note}                                                                                                                                                                    |
| Path Regex   | #^/async/form(?:/(?P<event>\w{8}-\w{4}-\w{4}-\w{4}-\w{12})(?:/(?P<session>\w{8}-\w{4}-\w{4}-\w{4}-\w{12})(?:/(?P<space>\w{8}-\w{4}-\w{4}-\w{4}-\w{12})(?:/(?P<note>\w{8}-\w{4}-\w{4}-\w{4}-\w{12}))?)?)?)?$#sDu |
| Host         | ANY                                                                                                                                                                                                             |
| Host Regex   |                                                                                                                                                                                                                 |
| Scheme       | ANY                                                                                                                                                                                                             |
| Method       | ANY                                                                                                                                                                                                             |
| Requirements | event: \w{8}-\w{4}-\w{4}-\w{4}-\w{12}                                                                                                                                                                           |
|              | note: \w{8}-\w{4}-\w{4}-\w{4}-\w{12}                                                                                                                                                                            |
|              | session: \w{8}-\w{4}-\w{4}-\w{4}-\w{12}                                                                                                                                                                         |
|              | space: \w{8}-\w{4}-\w{4}-\w{4}-\w{12}                                                                                                                                                                           |
| Class        | Symfony\Component\Routing\Route                                                                                                                                                                                 |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\AsyncFormController                                                                                                                                              |
|              | _format: json                                                                                                                                                                                                   |
|              | event: NULL                                                                                                                                                                                                     |
|              | note: NULL                                                                                                                                                                                                      |
|              | session: NULL                                                                                                                                                                                                   |
|              | space: NULL                                                                                                                                                                                                     |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler                                                                                                                                                         |
|              | utf8: true                                                                                                                                                                                                      |

| Property     | Value                                                          |
|--------------|----------------------------------------------------------------|
| Route Name   | bikeshed_space                                                 |
| Path         | /space                                                         |
| Path Regex   | #^/space$#sDu                                                  |
| Host         | ANY                                                            |
| Host Regex   |                                                                |
| Scheme       | ANY                                                            |
| Method       | ANY                                                            |
| Requirements | NO CUSTOM                                                      |
| Class        | Symfony\Component\Routing\Route                                |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\SpaceController |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler        |
|              | utf8: true                                                     |

| Property     | Value                                                         |
|--------------|---------------------------------------------------------------|
| Route Name   | bikeshed_note                                                 |
| Path         | /note/{id}                                                    |
| Path Regex   | #^/note/(?P<id>\w{8}-\w{4}-\w{4}-\w{4}-\w{12})$#sDu           |
| Host         | ANY                                                           |
| Host Regex   |                                                               |
| Scheme       | ANY                                                           |
| Method       | ANY                                                           |
| Requirements | id: \w{8}-\w{4}-\w{4}-\w{4}-\w{12}                            |
| Class        | Symfony\Component\Routing\Route                               |
| Defaults     | _controller: Maintainerati\Bikeshed\Controller\NoteController |
| Options      | compiler_class: Symfony\Component\Routing\RouteCompiler       |
|              | utf8: true                                                    |
