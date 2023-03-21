<?php

namespace Tualo\Office\MonacoEditor\Middlewares;
use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\IMiddleware;

class Middleware implements IMiddleware{
    public static function register(){
        TualoApplication::use('extjscmplr',function(){
            try{
                TualoApplication::javascript('extjscmplr', './cmplr/ext_before_load.js',[],-10000);
                TualoApplication::javascript('extjscmplr', './cmplr/bootstrap.js',[],-10000);
                

            }catch(\Exception $e){
                TualoApplication::set('maintanceMode','on');
                TualoApplication::addError($e->getMessage());
            }
        },-100); // should be one of the last
    }
}