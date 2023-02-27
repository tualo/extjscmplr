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
                    App::result('compile', Helper::compile($compiler_config, $client ));
                    App::result('success', true);
                }catch(\Exception $e){
                    App::result('msg', $e->getMessage());
                }
            }
            App::contenttype('application/json');
        },['get','post'],true);



        BasicRoute::add('/app.js',function($matches){

            $compiler_config = (App::get('configuration'))['ext-compiler'];
            Helper::compile( $compiler_config );

            $source = dirname( (App::get('configuration'))['ext-compiler']['sencha_compiler_source'] );
            /*
            $_SESSION['tualoapplication']['loggedIn'] = true;
            $_SESSION['tualoapplication']['typ'] = $_SESSION['typ'];
            $_SESSION['tualoapplication']['username'] = $_SESSION['username'];
            $_SESSION['tualoapplication']['fullname'] = $_SESSION['fullname'];
            $_SESSION['tualoapplication']['client'] = (isset($_SESSION['dbname'])?$_SESSION['dbname']:$_SESSION['client']);
            $_SESSION['tualoapplication']['clients'] = $this->db->direct('SELECT macc_users_clients.client FROM macc_users_clients join view_macc_clients on macc_users_clients.client = view_macc_clients.id WHERE macc_users_clients.login = {username}',$_SESSION['tualoapplication']);
            */
            $client='default';
            if (
                isset($_SESSION['tualoapplication']) &&
                isset($_SESSION['tualoapplication']['loggedIn']) &&
                $_SESSION['tualoapplication']['loggedIn'] === true
            ){
                $client = $_SESSION['tualoapplication']['client'];
            }

            if (!file_exists($source.'/'.$client.'/app.js')){
                Helper::compile($compiler_config )
            }
            readfile($source.'/default/app.js'); exit(); 
            
        },['get'],true);

        BasicRoute::add('/classic.json',function($matches){
            /*$compiler_config = (App::get('configuration'))['ext-compiler'];
            App::result('compile', Helper::getFiles($compiler_config ));
            App::contenttype('application/json');
            */
        },['get'],true);
    }
}
