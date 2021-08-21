<?php

namespace System\Application;

use System\Config\Config;
use System\Router\Routing;

class Application {
    public function __construct()
    {
        $this->loadProviders();
        $this->loadHelpers();
        $this->registerRoute();
        $this->routing();
    }

    private function loadProviders()
    {
//        $appConfigs = require dirname(dirname(__DIR__)) . '/config/app.php';
//        $providers  = $appConfigs['providers'];

        $providers = Config::get('app.providers');

        foreach ($providers as $provider){
            $providerObj = new $provider();
            $providerObj->boot();
        }
    }

    private function loadHelpers()
    {
        require_once dirname(__DIR__) . '/Helpers/helper.php';
        if (file_exists(dirname(dirname(__DIR__)) . '/app/Http/Helpers.php')){
            require_once dirname(dirname(__DIR__)) . '/app/Http/Helpers.php';
        }
    }

    private function registerRoute()
    {
        global $gl_routes;
        $gl_routes = [
            "get"       =>  [],
            "post"      =>  [],
            "put"       =>  [],
            "delete"    =>  [],
        ];

        require_once(dirname(dirname(__DIR__)) . "/routes/web.php");
        require_once(dirname(dirname(__DIR__)) . "/routes/api.php");
    }

    private function routing()
    {
        $routing = new Routing();
        $routing->run();
    }
}
