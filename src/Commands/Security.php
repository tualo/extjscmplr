<?php

namespace Tualo\Office\ExtJSCompiler\Commands;

use Garden\Cli\Cli;
use Garden\Cli\Args;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Basic\Path;
use GuzzleHttp\Client;
use Tualo\Office\Security\BaseSecurityCommand as BaseSecurityCommand;

if (class_exists('Tualo\Office\Security\SecurityCommandline')) {
    class SecurityCommandline extends BaseSecurityCommand implements \Tualo\Office\Security\ISecurityCommandline
    {

        public static function getCommandName(): string
        {
            return 'compiler';
        }

        public static function getCommandDescription(): string
        {
            return 'perform a security check for compiler';
        }
        public static function security(Cli $cli): void
        {
            $cli->command(self::getCommandName())
                ->description(self::getCommandDescription())
                ->opt('client', 'only use this client', false, 'string');
        }
        public static function run(Args $args)
        {
            $clientName = $args->getOpt('client');
            if (is_null($clientName)) $clientName = '';

            PostCheck::formatPrintLn(['blue'], "TESING SECURITY FOR COMPILER");
            PostCheck::formatPrintLn(['blue'], "==========================================================");

            self::checkURIAccess('/composer.json') ? PostCheck::formatPrintLn(['red'], "root composer.json is accessible") : PostCheck::formatPrintLn(['green'], "root composer.json is not accessible");
            self::checkURIAccess('/composer.lock') ? PostCheck::formatPrintLn(['red'], "root composer.lock is accessible") : PostCheck::formatPrintLn(['green'], "root composer.lock is not accessible");
            self::checkURIAccess('/vendor/tualo/extcmplr/composer.json') ? PostCheck::formatPrintLn(['red'], "/vendor/tualo/extcmplr/composer.json is accessible") : PostCheck::formatPrintLn(['green'], "/vendor/tualo/extcmplr/composer.json is not accessible");

            ini_get('display_errors') ? PostCheck::formatPrintLn(['red'], 'Warning: display_errors is set to ON') : PostCheck::formatPrintLn(['green'], 'display_errors is set correctly');
            ini_get('error_reporting') | E_WARNING  | E_ALL | E_DEPRECATED ? PostCheck::formatPrintLn(['yellow'], 'Warning: error_reporting is set incorrectly ') : PostCheck::formatPrintLn(['green'], 'error_reporting is set correctly');
        }
    }
}
