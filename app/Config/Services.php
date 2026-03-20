<?php

namespace Config;

use CodeIgniter\Cache\CacheFactory;
use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Cache\Exceptions\CacheException;
use CodeIgniter\Cache\Handlers\DummyHandler;
use CodeIgniter\Config\BaseService;
use Config\Cache;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /**
     * Cache service override.
     *
     * Some Windows/XAMPP setups can throw cache write errors early during boot
     * (ResponseCache is instantiated in the framework constructor). If the
     * configured cache handler is not writable, we fall back to DummyHandler
     * so the app can still run.
     */
    public static function cache(?Cache $config = null, bool $getShared = true): CacheInterface
    {
        if ($getShared) {
            return static::getSharedInstance('cache', $config);
        }

        $config ??= config(Cache::class);

        try {
            return CacheFactory::getHandler($config);
        } catch (CacheException $e) {
            return new DummyHandler();
        } catch (\Throwable $e) {
            return new DummyHandler();
        }
    }

    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */
}
