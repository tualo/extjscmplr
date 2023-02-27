<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\ExtJSCompiler\Helper;

class Read implements IRoute{

    public static function register(){
        BasicRoute::add('/compiler_files',function($matches){
            $compiler_config = (App::get('configuration'))['ext-compiler'];
            App::result('compile', Helper::getFiles($compiler_config ));
            App::contenttype('application/json');
        },['get','post'],true);

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
            App::contenttype('application/json');
        },['get','post'],true);



        BasicRoute::add('/app.js',function($matches){
            $source = dirname( (App::get('configuration'))['ext-compiler']['sencha_compiler_source'] );
            $_SESSION['']
            if (file_exists($source.'/'.$client.'/app.js')){ 
                readfile($source.'/'.$client.'/app.js'); exit(); 
            }else{
                readfile($source.'/default/app.js'); exit(); 
            }
        },['get'],true);

        BasicRoute::add('/classic.json',function($matches){
            /*$compiler_config = (App::get('configuration'))['ext-compiler'];
            App::result('compile', Helper::getFiles($compiler_config ));
            App::contenttype('application/json');
            */
        },['get'],true);
    }
}
