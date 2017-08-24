# Maleficarum Proxy installation

This document describes all the installation process for the Maleficarum Proxy starting from scratch.

Please remember to replace `/var/www/project` with the proper project path e.g. `/var/www/campaign_service`

## Requirements
* PHP 7.1
* Phalcon 3.2
* Composer

## Installation
1. Create project directory structure
    ```shell
    mkdir -p /var/www/project/proxy/config/{local,development,staging,uat,production}
    mkdir -p /var/www/project/proxy/src/Route
    mkdir -p /var/www/project/proxy/src/Controller/Status
    mkdir -p /var/www/project/proxy/public
    mkdir -p /var/www/project/proxy/templates/exceptions
    mkdir -p /var/www/project/proxy/cache/templates
    ```

2. Create `.gitignore` file `/var/www/project/proxy/.gitignore` and add the following content
    ```
    /vendor/
    /config/local/
    /cache/
    !/cache/templates/.gitkeep
    .idea
    ```

3. Put `.gitkeep` file to `/var/www/project/proxy/cache/templates` directory
    ```shell
    touch /var/www/project/proxy/cache/templates/.gitkeep
    ```

4. Create exception template file `/var/www/project/proxy/templates/exceptions/generic.html` and add the following content
    ```twig
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        
        <title>{{ statusCode }} - {{ reasonPhrase }}</title>
    </head>
    
    <body>
        <h1>{{ statusCode }} - {{ reasonPhrase }}</h1>
        
        <p>{{ message }}</p>
        
        <pre>{{ details|json_encode }}</pre>
    </body>
    </html>
    ```

5. Create config file template `/var/www/project/proxy/config/__example-config.ini` and add the following content
    ```ini
    ;##
    ;#   GLOBAL application settings
    ;##
    [global]
    ; Enable global setting section.
    ; Possible Values: true - enabled, false - disabled
    enabled = true
    
    [templates]
    directory = '/var/www/project/proxy/templates'
    cache_directory = '/var/www/project/proxy/cache/templates'
    ```

6. Create config file for each environment:
    ```shell
    cp /var/www/project/proxy/config/__example-config.ini /var/www/project/proxy/config/local/config.ini
    cp /var/www/project/proxy/config/__example-config.ini /var/www/project/proxy/config/development/config.ini
    cp /var/www/project/proxy/config/__example-config.ini /var/www/project/proxy/config/staging/config.ini
    cp /var/www/project/proxy/config/__example-config.ini /var/www/project/proxy/config/uat/config.ini
    cp /var/www/project/proxy/config/__example-config.ini /var/www/project/proxy/config/production/config.ini
    ```

7. Create composer file `/var/www/project/proxy/composer.json` and add the following content
    ```json
    {
        "name": "service_name-proxy",
        "description": "service_name - Proxy",
        "license": "proprietary",
        "autoload": {
            "psr-4": {
                "": "src/"
            }
        },
        "require": {
        },
        "repositories": [
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-proxy.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-ioc.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-http-client.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-config.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-profiler.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-environment.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-request.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-response.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-http-response.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-handler.git"
            },
            {
                "type": "vcs",
                "url": "git@github.com:pharaun13/maleficarum-logger.git"
            }
        ]
    }
    ```

    **Please remember to update project name and description like in the example listed below**
    ```json
    {
       "name": "campaign_service-proxy",
       "description": "Campaign service - Proxy",
    }
    ```

8. Install dependencies by running composer
    ```shell
    cd /var/www/project/proxy/
    composer require maleficarum/proxy
    composer require maleficarum/profiler
    composer require maleficarum/logger
    ```

9. Create status controller file `/var/www/project/proxy/src/Controller/Status/Controller.php` and add the following content
    ```php
    <?php
    declare(strict_types=1);
    
    namespace Controller\Status;
    
    use Maleficarum\Proxy\Controller\Generic;
    
    /**
     * This controller handles status reporting.
     */
    class Controller extends Generic {
        /**
         * Send system status.
         */
        public function getAction() {
            $jsonHandler = \Maleficarum\Ioc\Container::get('Maleficarum\Response\Http\Handler\JsonHandler');
            $this->getResponse()->setHandler($jsonHandler);
    
            return $this->getResponse()->render([
                'name' => 'service_name-proxy',
                'status' => 'OK'
            ]);
        }
    }
    ```

    **Please remember to update service name like in the example listed below**
    ```php
    return $this->getResponse()->render([
        'name' => 'campaign-service-proxy',
        'status' => 'OK'
    ]);
    ```

10. Create status route file `/var/www/project/proxy/src/Route/Status.php` and add the following content
    ```php
    <?php
    /**
     * Route definitions for the /status resource
     */
    declare(strict_types=1);

    /** @var \Maleficarum\Request\Request $request */
    $app->map('/status', function () use ($request) {
        \Maleficarum\Ioc\Container::get('Controller\Status\Controller')->__remap('get');
    })->via(['GET']);
    ```

11. Create front controller file `/var/www/project/proxy/public/index.php` and add the following content
    ```php
    <?php
    declare (strict_types=1);
    
    // initialize time profiling
    $start = microtime(true);
    
    // define path constants
    define('CONFIG_PATH', realpath('../config'));
    define('VENDOR_PATH', realpath('../vendor'));
    define('SRC_PATH', realpath('../src'));
    
    // add vendor based autoloading
    require_once VENDOR_PATH . '/autoload.php';
    
    // create Phalcon micro application
    $app = \Maleficarum\Ioc\Container::get('Phalcon\Mvc\Micro');
    $app->getRouter()->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
    
    // create the bootstrap object and run internal init
    $bootstrap = \Maleficarum\Ioc\Container::get('Maleficarum\Proxy\Bootstrap')
        ->setParamContainer([
            'app' => $app,
            'routes' => SRC_PATH . DIRECTORY_SEPARATOR . 'Route',
            'start' => $start,
            'builders' => [
                'response' => ['handler' => 'template']
            ],
            'prefix' => 'service_name-Proxy',
            'logger.message_prefix' => '[PHP] '
        ])
        ->setInitializers([
            \Maleficarum\Proxy\Bootstrap::INITIALIZER_ERRORS,
            [\Maleficarum\Handler\Initializer\Initializer::class, 'initialize'],
            [\Maleficarum\Profiler\Initializer\Initializer::class, 'initializeTime'],
            [\Maleficarum\Profiler\Initializer\Initializer::class, 'initializeDatabase'],
            [\Maleficarum\Environment\Initializer\Initializer::class, 'initialize'],
            \Maleficarum\Proxy\Bootstrap::INITIALIZER_DEBUG_LEVEL,
            [\Maleficarum\Config\Initializer\Initializer::class, 'initialize'],
            [\Maleficarum\Request\Initializer\Initializer::class, 'initialize'],
            [\Maleficarum\Response\Initializer\Initializer::class, 'initialize'],
            \Maleficarum\Proxy\Bootstrap::INITIALIZER_ROUTES,
            [\Maleficarum\Logger\Initializer\Initializer::class, 'initialize'],
            \Maleficarum\Proxy\Bootstrap::INITIALIZER_CONTROLLER
        ])
        ->initialize();
    
    // run the app
    $app->handle();
    
    // conclude application run
    $bootstrap->conclude();
    ```

    **Please remember to update log prefix like in the example listed below**
    ```php
    // ...
    $bootstrap = \Maleficarum\Ioc\Container::get('Maleficarum\Proxy\Bootstrap')
        ->setParamContainer([
            // ...
            'prefix' => 'service_name-Proxy',
            // ...
        ])
    // ...
    ```
