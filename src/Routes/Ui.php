<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\ExtJSCompiler\Helper;

class Ui implements IRoute{

    public static function register(){
        BasicRoute::add('/ui/(?P<path>.*)',function($matches){
            print_r($matches);exit();
        },['get'],true);
    }
}
