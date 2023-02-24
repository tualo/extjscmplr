<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\ExtJSCompiler\Helper;

class Read implements IRoute{

    public static function register(){
        BasicRoute::add('/compiler',function($matches){
            if (isset((App::get('configuration'))['ext-compiler'])){
                $compiler_config = (App::get('configuration'))['ext-compiler'];
                try{
                    //App::result('compiler_config', $compiler_config);
                    App::result('compile', Helper::compile($compiler_config ));
                    App::result('success', true);
                }catch(\Exception $e){
                    App::result('msg', $e->getMessage());
                }
            }
            // $compiler_config = (App::get('configuration'))['ext-compiler'];
            // /Users/thomashoffmann/Documents/Projects/php/tualo/extjscmplr/compiler_source/Tualo/classic/src/view/main/List.js
            // $db = App::get('session')->getDB();
            // $db->direct('SET SESSION group_concat_max_len = 4294967295;');
            // App::contenttype('application/json');
        },['get','post'],true);
    }
}
