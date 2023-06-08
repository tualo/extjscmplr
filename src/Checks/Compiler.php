<?php

namespace Tualo\Office\ExtJSCompiler\Checks;

use Tualo\Office\Basic\Middleware\Session;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Basic\TualoApplication as App;

use Tualo\Office\ExtJSCompiler\Helper;

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


        $client=Helper::getCurrentClient();
        $path = Helper::getCachePath();
        
        /*
        echo "$client \n";
        echo "$path \n";
        */
        if (!file_exists($path) || !is_dir($path)){

            self::formatPrintLn(['yellow'],"\tshould compiled first");
            $prompt = [
                "\t".'do you want to compile the "'.$client.'" now? [y|n|c] '
            ];
            while(in_array($line = readline(implode("\n",$prompt)),['yes','y','n','no','c'])){
                if ($line=='c') exit();
                if ($line=='y'){
                    self::formatPrintLn(['blue'],"\t compiling");
                    if (is_null(App::get('session'))){
                        self::formatPrintLn(['red'],"\t not possible, no database");
                    }else{
                        $res = Helper::compile($config['ext-compiler'], $client );
                        if ($res['return_code']!=0){
                            foreach($res['data'] as $row){
                                if ($row['level']=='[ERR]'){
                                    
                                    self::formatPrintLn(['yellow'],"\ttry: export OPENSSL_CONF=/dev/null ");
                                    self::formatPrintLn(['red'],"\t".$row['note']);
                                    break;
                                }
                            }
                        }else{
                            self::formatPrintLn(['green'],"\t compiled");
                        }
                    }
                    break;
                }
                if ($line=='n'){
                    self::formatPrintLn(['yellow'],"\tthe compiler will start at the first time visiting the service");
                    break;
                }
            }
        }
        
    }
}