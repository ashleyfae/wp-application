<?php
/**
 * App.php
 *
 * @package   wp-application
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   MIT
 */

namespace Ashleyfae\AppWP;

use Ashleyfae\AppWP\Container\Container;
use Ashleyfae\AppWP\Exceptions\InvalidServiceProviderException;
use Ashleyfae\AppWP\ServiceProviders\ServiceProvider;

/**
 * @method void singleton(string $abstract, \Closure|string|null $concrete = null)
 * @method void bind(string $abstract, \Closure|string|null $concrete = null)
 * @method object make(string $abstract, array $parameters = [])
 */
class App
{

    /**
     * @var App|null
     */
    private static $instance = null;

    /**
     * @var Container
     */
    private $container;

    private $serviceProviders = [];

    private $serviceProvidersLoaded = false;

    public function __construct()
    {
        $this->container = new Container();
    }

    public static function getInstance(): App
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
            self::$instance->boot();
        }

        return self::$instance;
    }

    /**
     * Properties are loaded from the service container.
     *
     * @since 1.0
     *
     * @param  string  $property
     *
     * @return mixed
     * @throws \Exception
     */
    public function __get(string $property)
    {
        return $this->container->get($property);
    }

    /**
     * Magic methods are passed to the service container.
     *
     * @since 1.0
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->container, $name], $arguments);
    }

    /**
     * Loads the service providers.
     *
     * @since 1.0
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadServiceProviders();
    }

    /**
     * Sets an array of service providers to load.
     *
     * @since 1.0
     *
     * @param  array  $classes
     *
     * @return void
     */
    public function withServiceProviders(array $classes): void
    {
        foreach ($classes as $class) {
            $this->addServiceProvider($class);
        }
    }

    /**
     * Adds a single service provider.
     *
     * @since 1.0
     *
     * @param  string  $class
     *
     * @return void
     * @throws InvalidServiceProviderException
     */
    public function addServiceProvider(string $class): void
    {
        $this->validateServiceProvider($class);
        $this->serviceProviders[] = $class;
    }

    /**
     * Validates the class as a service provider.
     *
     * @since 1.0
     *
     * @param  string  $class
     *
     * @return void
     * @throws InvalidServiceProviderException
     */
    private function validateServiceProvider(string $class): void
    {
        if (! is_subclass_of($class, ServiceProvider::class)) {
            throw new InvalidServiceProviderException(sprintf(
                '%s class must implement the %s interface.',
                $class,
                ServiceProvider::class
            ));
        }
    }

    /**
     * Registers and boots the service providers.
     *
     * @since 1.0
     *
     * @return void
     * @throws InvalidServiceProviderException
     */
    public function loadServiceProviders(): void
    {
        if ($this->serviceProvidersLoaded) {
            return;
        }

        $providers = [];
        foreach ($this->serviceProviders as $serviceProvider) {
            $this->validateServiceProvider($serviceProvider);

            /**
             * @var ServiceProvider $serviceProvider
             */
            $serviceProvider = new $serviceProvider();
            $serviceProvider->register();
            $providers[] = $serviceProvider;
        }

        foreach ($providers as $serviceProvider) {
            $serviceProvider->boot();
        }

        $this->serviceProvidersLoaded = true;
    }

}
