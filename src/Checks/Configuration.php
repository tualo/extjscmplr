<?php
namespace Tualo\Office\ExtJSCompiler\Checks;

use Tualo\Office\Basic\Middleware\Session;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Basic\TualoApplication as App;


class Configuration  extends PostCheck {
    
    public static function test(array $config){
        $clientdb = App::get('clientDB');
        if (is_null($clientdb)) return;

        if(($cmd = App::configuration('ext-compiler','sencha_compiler_command',false))==false){
            PostCheck::formatPrintLn(['red'],"\tsencha cmd not found");
            PostCheck::formatPrintLn(['blue'],"\tcall `./tm configuration --section ext-compiler --key sencha_compiler_command --value $(which sencha)`");
        }else{
            exec($cmd.'',$output,$return_var);
            if ($return_var!=0){
                PostCheck::formatPrintLn(['red'],"\tsencha cmd *$cmd* is not callable ($return_var), try `npm install -g sencha-cmd`");
            }else{
                PostCheck::formatPrintLn(['green'],"\tsencha version: ".$output[0]);
            }
        }
        if(($sdk = App::configuration('ext-compiler','sencha_compiler_sdk',false))==false){
            PostCheck::formatPrintLn(['red'],"\tsdk not defined");
        }else{
            if (file_exists($sdk)){
                PostCheck::formatPrintLn(['green'],"\tsdk found: ".$sdk);
            }else{
                PostCheck::formatPrintLn(['red'],"\tsdk not found: ".$sdk);
            }
        }
        
        

    }
}