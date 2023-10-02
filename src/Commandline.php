<?php
namespace Tualo\Office\Basic;
use Garden\Cli\Cli;
use Garden\Cli\Args;
use Tualo\Office\Basic\ICommandline;
use Tualo\Office\ExtJSCompiler\Helper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\PostCheck;

class Commandline implements ICommandline{

    public static function getCommandName():string { return 'compile';}

    public static function setup(Cli $cli){
        $cli->command(self::getCommandName())
            ->description('compile the extjs application')
            ->opt('client', 'only use this client', true, 'string');
    }
    public static function run(Args $args){

        if (isset((App::get('configuration'))['ext-compiler'])){
            $compiler_config = (App::get('configuration'))['ext-compiler'];
            try{
                $client=$args->getOpt('client','default');
                $res = Helper::compile($compiler_config,$client);
                if ($res['return_code']!=0){
                    foreach($res['data'] as $row){
                        if ($row['level']=='[ERR]'){
                            
                            PostCheck::formatPrintLn(['yellow'],"\ttry: export OPENSSL_CONF=/dev/null ");
                            PostCheck::formatPrintLn(['red'],"\t".$row['note']);
                            break;
                        }
                    }
                }else{
                    PostCheck::formatPrintLn(['green'],"\t compiled");
                }

            }catch(\Exception $e){
                echo $e->getMessage()."\n";
            }
        }
    }
}
