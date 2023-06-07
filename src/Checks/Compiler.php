<?php

namespace Tualo\Office\ExtJSCompiler\Checks;

use Tualo\Office\Basic\Middleware\Session;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Basic\TualoApplication as App;


class Compiler extends PostCheck {
    public static function test(array $config){
        $config = App::get('configuration');

        if (!isset($config['ext-compiler'])){
            self::formatPrintLn(['yellow'],"\text-compiler section not defined");
        }else{
            self::formatPrintLn(['green'],"\text-compiler section defined");
        }

        if (!isset($config['ext-compiler']['sencha_compiler_command'])){
            self::formatPrintLn(['yellow'],"\tsencha_compiler_command not defined");
        }else{
            self::formatPrintLn(['green'],"\tsencha_compiler_command defined");
        }

        if (!isset($config['ext-compiler']['sencha_compiler_sdk'])){
            self::formatPrintLn(['yellow'],"\tsencha_compiler_sdk not defined");
        }else{
            self::formatPrintLn(['green'],"\tsencha_compiler_sdk defined");
            if (!file_exists($config['ext-compiler']['sencha_compiler_sdk'])&&!is_dir($config['ext-compiler']['sencha_compiler_sdk'])){
                self::formatPrintLn(['red'],"\tsencha_compiler_sdk not accessible");
            }
        }
        
    }
}