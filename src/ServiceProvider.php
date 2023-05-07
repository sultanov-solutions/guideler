<?php

namespace Guideler\Tools;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerHelpers();
    }

    private function registerHelpers()
    {
        require_once __DIR__ . '/Helpers/table.php';
    }
}
