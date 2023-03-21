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

            $doc = new DOMDocument();
            $doc->loadHTMLFile(Helper::getBuildPath().'/index.html');
            $elements = $doc->getElementsByTagName('script');
            $index =0;
            if (!is_null($elements)) {
                foreach ($elements as $element) {
                    if ($index==0){
                        file_put_contents(Helper::getBuildPath().'/ext_start.js',$element->textContent);
                    }else if ($index==1){
                        file_put_contents(Helper::getBuildPath().'/bootstrap.js',$element->textContent);
                    }
                    $index++;
                }
            }
            var_dump($elements );
            exit();

        },['get','post'],false);


        BasicRoute::add('/compiler_files',function($matches){
            $compiler_config = (App::get('configuration'))['ext-compiler'];
            App::result('compile', Helper::getFiles($compiler_config ));
            App::contenttype('application/json');
        },['get','post'],false);

        BasicRoute::add('/compiler',function($matches){
            if (isset((App::get('configuration'))['ext-compiler'])){
                $compiler_config = (App::get('configuration'))['ext-compiler'];
                try{
                    $client=Helper::getCurrentClient();
                    App::result('compile', Helper::compile($compiler_config, $client ));
                    App::result('success', true);
                }catch(\Exception $e){
                    App::result('msg', $e->getMessage());
                }
            }
            App::contenttype('application/json');
        },['get','post'],false);


    }
}
