<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class Read implements IRoute{

    public static function register(){

        BasicRoute::add('/compiler',function($matches){

            $db = App::get('session')->getDB();
            $tablename = $matches['tablename'];
            $db->direct('SET SESSION group_concat_max_len = 4294967295;');
            try{
                App::result('data', __DIR__);
                App::result('success', true);
            }catch(\Exception $e){
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        },['get','post'],true);
    }
}
