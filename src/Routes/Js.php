<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\ExtJSCompiler\Helper;

class Js implements IRoute{
    public static function register(){

        BasicRoute::add('/cmplr/(?P<file>[\/.\w\d\-]+)',function($matches){
            if (file_exists(Helper::getBuildPath().'/'.$matches['file'].'')){
                $path_parts = pathinfo(Helper::getBuildPath().'/'.$matches['file'].'');
                if ($path_parts['extension']=='js')   App::contenttype('application/javascript');
                if ($path_parts['extension']=='css')   App::contenttype('text/css');
                App::etagFile(Helper::getBuildPath().'/'.$matches['file'].'');
            }else{
                App::body("// hm, something is wrong ".$matches['file']);
            }
        },array('get','post'),false);
    }
}