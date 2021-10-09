<?php
/**
 * App.php
 *
 * @package   wp-application
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\App;

use AshleyFae\App\Container\Container;
use AshleyFae\App\ServiceProviders\ServiceProvider;

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

    public function boot(): void
    {
        add_action('plugins_loaded', [$this, 'loadServiceProviders'], 200);
    }

    public function addServiceProviders(array $classes): void
    {
        foreach ($classes as $class) {
            $this->addServiceProvider($class);
        }
    }

    public function addServiceProvider(string $class): void
    {
        $this->validateServiceProvider($class);
        $this->serviceProviders[] = $class;
    }

    private function validateServiceProvider(string $class): void
    {
        if (! is_subclass_of($class, ServiceProvider::class)) {
            throw new \InvalidArgumentException(sprintf(
                '%s class must implement the %s interface.',
                $class,
                ServiceProvider::class
            ));
        }
    }

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
