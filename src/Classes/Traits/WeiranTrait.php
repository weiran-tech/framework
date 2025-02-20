<?php

declare(strict_types = 1);

namespace Weiran\Framework\Classes\Traits;

use Illuminate\Auth\AuthManager;
use Illuminate\Cache\TaggableStore;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Redis\RedisManager;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\View\Factory;
use Psr\Log\LoggerInterface;
use Weiran\Framework\Foundation\Application;
use Weiran\Framework\Parse\Ini;
use Weiran\Framework\Parse\Xml;
use Weiran\Framework\Parse\Yaml;
use Weiran\Framework\Translation\Translator;
use Weiran\Framework\Weiran\Weiran;

/**
 * WeiranTrait
 * @see app
 */
trait WeiranTrait
{

    /**
     * get auth
     * @return AuthManager
     */
    protected function pyAuth(): AuthManager
    {
        return weiran_container()->make('auth');
    }

    /**
     * get translator
     * @return Translator
     */
    protected function pyTranslator(): Translator
    {
        return weiran_container()->make('translator');
    }


    /**
     * Get configuration instance.
     * @return Repository
     */
    protected function pyConfig()
    {
        return weiran_container()->make('config');
    }


    /**
     * get db
     * @return DatabaseManager
     */
    protected function pyDb(): DatabaseManager
    {
        return weiran_container()->make('db');
    }

    /**
     * Get console instance.
     * @return Kernel
     */
    protected function pyConsole()
    {
        return weiran_container()->make(Kernel::class);
    }

    /**
     * Get IoC Container.
     * @return Container | Application
     */
    protected function pyContainer(): Container
    {
        return Container::getInstance();
    }

    /**
     * Get mailer instance.
     * @return Mailer
     */
    protected function pyMailer(): Mailer
    {
        return weiran_container()->make('mailer');
    }

    /**
     * Get session instance.
     * @return SessionManager|Store
     */
    protected function pySession()
    {
        return weiran_container()->make('session');
    }

    /**
     * get request
     * @return Request
     */
    protected function pyRequest(): Request
    {
        return weiran_container()->make('request');
    }


    /**
     * get redirector
     * @return Redirector
     */
    protected function pyRedirector(): Redirector
    {
        return weiran_container()->make('redirect');
    }

    /**
     * get validation
     * @return \Illuminate\Validation\Factory
     */
    protected function pyValidation(): \Illuminate\Validation\Factory
    {
        return weiran_container()->make('validator');
    }


    /**
     * get event
     * @return Dispatcher
     */
    protected function pyEvent(): Dispatcher
    {
        return weiran_container()->make('events');
    }


    /**
     * get logger
     * @return LoggerInterface
     */
    protected function pyLogger(): LoggerInterface
    {
        return weiran_container()->make('log');
    }


    /**
     * get response
     * @return ResponseFactory
     */
    protected function pyResponse()
    {
        return weiran_container()->make(ResponseFactory::class);
    }


    /**
     * get file
     * @return Filesystem
     */
    protected function pyFile()
    {
        return weiran_container()->make('files');
    }


    /**
     * get url
     * @return UrlGenerator
     */
    protected function pyUrl()
    {
        return weiran_container()->make('url');
    }


    /**
     * get cache
     * @param string $tag tag
     * @return mixed
     */
    protected function pyCache($tag = '')
    {
        $cache = weiran_container()->make('cache');
        if ($tag && $cache->getStore() instanceof TaggableStore) {
            return $cache->tags($tag);
        }

        return $cache;
    }

    /**
     * get redis
     * @return RedisManager
     */
    protected function pyRedis(): RedisManager
    {
        return weiran_container()->make('redis');
    }

    /**
     * get view
     * @return Factory
     */
    protected function pyView(): Factory
    {
        return weiran_container()->make('view');
    }

    /**
     * get weiran
     * @return Weiran
     */
    protected function pyWeiran(): Weiran
    {
        return weiran_container()->make('weiran');
    }


    /**
     * Yaml Parser
     * @return Yaml
     */
    protected function pyYaml(): Yaml
    {
        return weiran_container()->make('weiran.yaml');
    }
}

