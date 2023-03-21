<?php

namespace Tualo\Office\ExtJSCompiler\Middlewares;
use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\IMiddleware;

class Middleware implements IMiddleware{
    public static function register(){
        TualoApplication::use('extjscmplr',function(){
            try{
                TualoApplication::javascript('extjscmplr_ext_before_load', './cmplr/ext_before_load.js',[],-10000);
                TualoApplication::javascript('extjscmplr_bootstrap', './cmplr/bootstrap.js',[],1000000);
                

            }catch(\Exception $e){
                TualoApplication::set('maintanceMode','on');
                TualoApplication::addError($e->getMessage());
            }
        },-100); // should be one of the last
    }
}