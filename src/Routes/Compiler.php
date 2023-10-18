<?php

namespace Tualo\Office\ExtJSCompiler\Routes;
use DOMDocument;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\ExtJSCompiler\Helper;

class Read implements IRoute{

    public static function register(){
        BasicRoute::add('/compiler_extract',function($matches){

            Helper::extract();
            App::contenttype('application/json');
            App::result('success', true);
        },['get','post'],false);


        BasicRoute::add('/compiler_files',function($matches){
            $compiler_config = (App::get('configuration'))['ext-compiler'];
            App::result('files', Helper::getFiles($compiler_config ));
            App::result('success', true );
            App::contenttype('application/json');
        },['get','post'],false);


        BasicRoute::add('/compiler_copy',function($matches){
            $compiler_config = (App::get('configuration'))['ext-compiler'];
            App::result('compile', Helper::copy( $compiler_config ));
            App::result('success', true );
            App::contenttype('application/json');
        },['get','post'],false);

        BasicRoute::add('/compiler',function($matches){
            App::contenttype('application/json');
            if (isset((App::get('configuration'))['ext-compiler'])){
                $compiler_config = (App::get('configuration'))['ext-compiler'];
                try{
                    $client=Helper::getCurrentClient();
                    $res = Helper::compile($compiler_config);
                    App::result('compile', $res);

                    if ($res['return_code']!=0){
                        foreach($res['data'] as $row){
                            if ($row['level']=='[ERR]'){
                                App::result('msg', $row['note']);
                                break;
                            }
                        }
                        if ($res['return_code']==127){
                            App::result('msg', 'compiler not found');
                        }
                        App::result('success', false);
                    }else{
                        
                        App::result('success', true);
                    }

                }catch(\Exception $e){
                    App::result('msg', $e->getMessage());
                    BasicRoute::$finished=true;
                }
            }
        },['get','post'],false);


    }
}
