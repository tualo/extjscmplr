<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\ExtJSCompiler\Helper;

class Ui implements IRoute{

    public static function register(){
        BasicRoute::add('/ui/(?P<path>.*)',function($matches){
            $client=Helper::getCurrentClient();
            $path = Helper::getBuildPath();
            $compiler_config = (App::get('configuration'))['ext-compiler'];
            echo  $path.PHP_EOL;
            echo  $client.PHP_EOL;
            if (!file_exists($path) || !is_dir($path)){
                echo "compile needed".PHP_EOL;
                Helper::compile($compiler_config, $client );
            }
            print_r($matches);exit();
        },['get'],false);
    }
}
