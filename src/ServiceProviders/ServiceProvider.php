<?php
/**
 * ServiceProvider.php
 *
 * @package   wp-application
 * @copyright Copyright (c) 2022, Ashley Gibson
 * @license   MIT
 * @since     1.0
 */

namespace Ashleyfae\AppWP\ServiceProviders;

interface ServiceProvider
{

    /**
     * Registers the service provider within the application.
     *
     * @since 1.0
     *
     * @return void
     */
    public function register(): void;

    /**
     * Bootstraps the service after all of the services have been registered.
     * All dependencies will be available at this point.
     *
     * @since 1.0
     *
     * @return void
     */
    public function boot(): void;

}
