<?php
/**
 * Hooks.php
 *
 * @package   wp-application
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace AshleyFae\App\Helpers;

use AshleyFae\App\App;

class Hooks
{

    /**
     * Adds a hook using `add_action()`.
     * This prevents the need to instantiate a class before adding it to hook.
     *
     * @param  string  $tag
     * @param  string  $class
     * @param  string  $method
     * @param  int  $priority
     * @param  int  $acceptedArgs
     */
    public static function addAction(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ) {
        if (! method_exists($class, $method)) {
            throw new \InvalidArgumentException(sprintf(
                'The method %s does not exist in %s.',
                $method,
                $class
            ));
        }

        add_action(
            $tag,
            static function () use ($tag, $class, $method) {
                call_user_func_array([App::getInstance()->make($class), $method], func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }

    public static function addFilter(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ) {
        if (! method_exists($class, $method)) {
            throw new \InvalidArgumentException(sprintf(
                'The method %s does not exist in %s.',
                $method,
                $class
            ));
        }

        add_filter(
            $tag,
            static function () use ($tag, $class, $method) {
                call_user_func_array([App::getInstance()->make($class), $method], func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }

}
