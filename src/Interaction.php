<?php 

namespace Sasin91\LaravelInteractions;

use Illuminate\Support\Str;
use Sasin91\LaravelInteractions\Interactions\Interactable;

/**
 * Class Interaction
 * @package Sasin91\LaravelInteractions
 *
 * Inspired by Laravel Spark.
 * @credits {Authors of Laravel/Spark}
 */
class Interaction
{
    protected static $swappedInteractions = [];

    /**
     * @param $interaction
     * @param array $parameters
     * @return mixed
     */
    public static function call($interaction, array $parameters = [])
    {
        return static::interact($interaction, $parameters);
    }

    public static function interact($interaction, array $parameters = [])
    {
        // when a method is not defined by @,
        // default to handle
        if (! Str::contains($interaction, '@')) {
            $interaction = static::appendAtMethod($interaction);
        }

        list($class, $method) = explode('@', $interaction);

        $resolved = resolve($class);

        if (! $resolved instanceof Interactable) {
            throw new \InvalidArgumentException("{$class} is not a valid Interaction.");
        }

        if (isset(static::$swappedInteractions[$class])) {
            return self::callSwapped($class, $parameters, $resolved);
        }
        
        return $resolved->$method($parameters);
        //return call_user_func_array([$resolved, $method], $parameters);
    }

    /**
     * Swap given Interaction with a callback.
     *
     * @param $interaction
     * @param \Closure $callback
     */
    public static function swap($interaction, \Closure $callback)
    {
        static::$swappedInteractions[$interaction] = $callback;
    }

    /**
     * Call a swapped Interaction
     *
     * @param $interaction
     * @param array $parameters
     * @param Interactable $instance
     * @return mixed
     */
    public static function callSwapped($interaction, array $parameters, Interactable $instance)
    {
        $closure = static::$swappedInteractions[$interaction]->bindTo($instance, $interaction);

        return call_user_func_array($closure, static::resolveClosureDependencies($closure, $parameters));
    }

    /**
     * Resolve dependencies of give Closure.
     *
     * @param \Closure $closure
     * @param array $parameters
     * @return array
     */
    protected static function resolveClosureDependencies(\Closure $closure, array $parameters)
    {
        $dependencies = [];
        foreach ((new \ReflectionFunction($closure))->getParameters() as $parameter) {
            static::addDependencyForCallParameter(app(), $parameter, $parameters, $dependencies);
        }

        return array_merge($dependencies, $parameters);
    }

    /**
     * Get the dependency for the given call parameter.
     *
     * @credits Adam Wathan
     * @param  \Illuminate\Container\Container  $container
     * @param  \ReflectionParameter  $parameter
     * @param  array  $parameters
     * @param  array  $dependencies
     * @return mixed
     */
    protected static function addDependencyForCallParameter($container, $parameter,
                                                            array &$parameters, &$dependencies)
    {
        if (array_key_exists($parameter->name, $parameters)) {
            $dependencies[] = $parameters[$parameter->name];

            unset($parameters[$parameter->name]);
        } elseif ($parameter->getClass()) {
            $dependencies[] = $container->make($parameter->getClass()->name);
        } elseif ($parameter->isDefaultValueAvailable()) {
            $dependencies[] = $parameter->getDefaultValue();
        }
    }

    /**
     * @param string $interaction
     * @param string $method
     * @return string
     */
    public static function appendAtMethod(&$interaction, $method = 'handle')
    {
        return $interaction.'@'.$method;
    }
}
