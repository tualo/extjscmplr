<?php
namespace Tualo\Office\Basic;
use Garden\Cli\Cli;
use Garden\Cli\Args;
use Tualo\Office\Basic\ICommandline;

class Commandline implements ICommandline{

    public static function getCommandName():string { return 'compile';}

    public static function setup(Cli $cli){
        $cli->command(self::getCommandName())
            ->description('compile the extjs application')
            ->opt('client', 'only use this client', false, 'string');
    }
    public static function run(Args $args){
        echo $args->getOpt('client','default').": HERE I AM\n";
    }
}
