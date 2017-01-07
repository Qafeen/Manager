<?php
namespace Qafeen\Manager\Traits;

use Exception;

/**
 * Helper trait
 *
 * @package Qafeen\Manager
 * @author Mohammed Mudasir <hello@mudasir.me>
 */
trait Helper
{
    /**
     * Instantiate a class and use it like chain responsibility.
     *
     * @return static
     * @throws Exception
     */
    public static function instance()
    {
        switch (func_num_args()) {
            case func_num_args() == 0:
                return new static;
            case func_num_args() == 1:
                return new static(func_get_arg(0));
            case func_num_args() == 2:
                return new static(func_get_arg(0), func_get_arg(1));
            case func_num_args() == 3:
                return new static(func_get_arg(0), func_get_arg(1), func_get_arg(2));
            case func_num_args() == 4:
                return new static(func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3));
            case func_num_args() == 5:
                return new static(
                    func_get_arg(0), func_get_arg(1), func_get_arg(2), func_get_arg(3), func_get_arg(4)
                );
            default:
                throw new Exception('Unable to instantiate class with given arguments');
        }
    }
}
