<?php

namespace Tualo\Office\ExtJSCompiler\Middlewares;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\IMiddleware;
use Tualo\Office\ExtJSCompiler\AppJson;
use Tualo\Office\ExtJSCompiler\Helper;

class Middleware implements IMiddleware
{
    public static function register()
    {
        TualoApplication::use('extjscmplr', function () {
            try {
                TualoApplication::javascript('extjscmplr_ext_before_load', './ui/ext_start.js', [], -10000);
                TualoApplication::javascript('extjscmplr_bootstrap', './ui/bootstrap.js', [], 1000000, [
                    'data-app' => AppJson::get()['id'],
                    'id' => 'microloader'
                ]);

                $requestedFile = 'bootstrap.js';
                $baseDir = Helper::getCachePath();

                if (!file_exists($baseDir . '/' . $requestedFile)) {
                    TualoApplication::set('message', 'Bootstrap file not found, please compile the ExtJS application first <a href="./compiler" target="_blank">here</a>');
                    TualoApplication::set('maintanceMode', 'on');
                }
            } catch (\Exception $e) {
                TualoApplication::set('maintanceMode', 'on');
                TualoApplication::addError($e->getMessage());
            }
        }, -100); // should be one of the last
    }
}
