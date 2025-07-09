<?php
/**
 * This class contains default initializers used as Maleficarum bootstrap methods.
 */
declare (strict_types=1);

namespace Maleficarum\Proxy\Basic;

class Initializer {
    /* ------------------------------------ Class Methods START ---------------------------------------- */

    /**
     * Set up error/exception handling.
     * @return string
     */
    static public function setUpErrorHandling(): string {
        $handler = function (\Throwable $throwable) {
            /** @var \Maleficarum\Handler\Http\Strategy\HtmlStrategy $strategy */
            $strategy = \Maleficarum\Ioc\Container::get('Maleficarum\Handler\Http\Strategy\HtmlStrategy');

            /** @var \Maleficarum\Handler\Http\ExceptionHandler $handler */
            $handler = \Maleficarum\Ioc\Container::get('Maleficarum\Handler\Http\ExceptionHandler', [$strategy]);
            $handler->handle($throwable);
        };

        set_exception_handler($handler);
        set_error_handler([\Maleficarum\Ioc\Container::get('Maleficarum\Handler\ErrorHandler'), 'handle']);

        // return initializer name
        return __METHOD__;
    }

    /**
     * Detect application environment.
     *
     * @param array $opts
     *
     * @throws \RuntimeException
     * @return string
     */
    static public function setUpDebugLevel(array $opts = []): string {
        try {
            $environment = \Maleficarum\Ioc\Container::retrieveShare('Maleficarum\Environment');
            $environment = $environment->getCurrentEnvironment();
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Environment object not initialized. \%s', __METHOD__));
        }

        if (stripos($environment, 'uat') === 0) {
            \Maleficarum\Handler\AbstractHandler::setDebugLevel(\Maleficarum\Handler\AbstractHandler::DEBUG_LEVEL_LIMITED);

            return __METHOD__;
        }

        \preg_match('/^production|development|sandbox|staging|uat|local/', $environment, $matches);

        // set handler debug level and error display value based on env
        switch (\reset($matches)) {
            case 'local':
            case 'development':
            case 'staging':
                \Maleficarum\Handler\AbstractHandler::setDebugLevel(\Maleficarum\Handler\AbstractHandler::DEBUG_LEVEL_FULL);
                break;
            case 'uat':
            case 'sandbox':
                \Maleficarum\Handler\AbstractHandler::setDebugLevel(\Maleficarum\Handler\AbstractHandler::DEBUG_LEVEL_LIMITED);
                break;
            case 'production':
                \Maleficarum\Handler\AbstractHandler::setDebugLevel(\Maleficarum\Handler\AbstractHandler::DEBUG_LEVEL_CRUCIAL);
                break;
            default:
                throw new \RuntimeException(sprintf('Unrecognised environment. \%s', __METHOD__));
        }

        // return initializer name
        return __METHOD__;
    }

    /**
     * Prepare and register the security object.
     *
     * @param array $opts
     *
     * @return string
     */
    static public function setUpSecurity(array $opts = []): string {
        // load default builder if skip not requested
        $builders = $opts['builders'] ?? [];
        is_array($builders) or $builders = [];
        isset($builders['security']['skip']) or \Maleficarum\Ioc\Container::get('Maleficarum\Proxy\Basic\Builder')->register('security');

        /** @var \Maleficarum\Proxy\Security\Manager $security */
        $security = \Maleficarum\Ioc\Container::get('Maleficarum\Proxy\Security\Manager');
        try {
            $security->verify();
        } catch (\Maleficarum\Exception\SecurityException $e) {
            throw new \Maleficarum\Exception\SecurityException('');
        }

        // return initializer name
        return __METHOD__;
    }

    /**
     * Bootstrap step method - prepare and register application routes.
     *
     * @param array $opts
     *
     * @throws \RuntimeException
     * @return string
     */
    static public function setUpRoutes(array $opts = []): string {
        try {
            $request = \Maleficarum\Ioc\Container::retrieveShare('Maleficarum\Request');
        } catch (\RuntimeException $e) {
            throw new \RuntimeException(sprintf('Request object not initialized. \%s', __METHOD__));
        }

        // validate input container
        $app = $opts['app'] ?? null;
        $routesPath = $opts['routes'] ?? null;

        if (!is_object($app)) {
            throw new \RuntimeException(sprintf('Phalcon application not defined for bootstrap. \%s()', __METHOD__));
        }
        if (!is_readable($routesPath)) {
            throw new \RuntimeException(sprintf('Routes path not readable. \%s()', __METHOD__));
        }

        // include outside routes
        $route = explode('?', strtolower($request->getUri()))[0];
        $route = explode('/', preg_replace('/^\//', '', $route));
        $route = ucfirst(array_shift($route));

        // set route filename for root path
        if (0 === mb_strlen($route)) {
            $route = 'Generic';
        }

        $path = $routesPath . DIRECTORY_SEPARATOR . $route . '.php';
        if (is_readable($path)) {
            require_once $path;
        }

        /** DEFAULT Route: call the default controller to check for redirect SEO entries **/
        $app->notFound(function () {
            \Maleficarum\Ioc\Container::get('Maleficarum\Proxy\Controller\Fallback')->__remap('notFound');
        });

        // return initializer name
        return __METHOD__;
    }

    /**
     * Register default Controller builder function.
     *
     * @param array $opts
     *
     * @return string
     */
    static public function setUpController(array $opts = []): string {
        // load default builder if skip not requested
        $builders = $opts['builders'] ?? [];
        is_array($builders) or $builders = [];
        isset($builders['controller']['skip']) or \Maleficarum\Ioc\Container::get('Maleficarum\Proxy\Basic\Builder')->register('controller');

        // return initializer name
        return __METHOD__;
    }

    /* ------------------------------------ Class Methods END ------------------------------------------ */
}

