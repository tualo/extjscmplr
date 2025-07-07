<?php

namespace Tualo\Office\ExtJSCompiler\Commands;

use Garden\Cli\Cli;
use Garden\Cli\Args;
use Tualo\Office\Basic\PostCheck;

use GuzzleHttp\Client;

if (class_exists('Tualo\Office\Security\SecurityCommandline')) {
    class SecurityCommandline implements \Tualo\Office\Security\ISecurityCommandline
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

            /*
            // check statuscode for compiler using curl
            // query only header options
            $url = 'https://oldenburg.stimmzettel.online'; // . urlencode($clientName);

            try {
                $client = new Client(
                    [
                        'base_uri' =>  $url,
                        'timeout'  => 1.0,
                    ]
                );
                $response = $client->get('/wm/composer.json', []);
                $code = $response->getStatusCode(); // 200

                if ($code == 200) {
                    PostCheck::formatPrintLn(['green'], "Compiler is running and accessible at " . $url);
                } else {
                    PostCheck::formatPrintLn(['red'], "Compiler is not accessible at " . $url . " (HTTP Status Code: " . $code . ")");
                }
            } catch (\Exception $e) {
                PostCheck::formatPrintLn(['red'], "Compiler is not accessible at " . $url . " (Exception: " . $e->getMessage() . ")");
            }*/
            ini_get('display_errors') ? PostCheck::formatPrintLn(['red'], 'Warning: display_errors is set to ON') : PostCheck::formatPrintLn(['green'], 'display_errors is set correctly');
            // ini_get('error_reporting') & E_ALL ? PostCheck::formatPrintLn(['red'], 'Warning: error_reporting is set to E_ALL') : PostCheck::formatPrintLn(['green'], 'error_reporting is set correctly');
            ini_get('error_reporting') | E_WARNING  | E_ALL | E_DEPRECATED ? PostCheck::formatPrintLn(['yellow'], 'Warning: error_reporting is set incorrectly ') : PostCheck::formatPrintLn(['green'], 'error_reporting is set correctly');
        }
    }
}
