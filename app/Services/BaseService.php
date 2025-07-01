<?php

namespace App\Services;

class BaseService
{
    protected static array $instances = [];

    /**
     * 返回static
     * @return static
     */
    public static function getInstance(): static
    {
        $class = static::class;
        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = new static();
        }
        return static::$instances[$class];
    }

    private function __clone(): void
    {
        // TODO: Implement __clone() method.
    }

    public function __construct()
    {
    }
}
