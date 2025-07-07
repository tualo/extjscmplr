<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\Basic\RouteSecurityHelper;
use Tualo\Office\ExtJSCompiler\Helper;

class Ui implements IRoute
{

    public static function readfile($filename, $identifier = "", $strict = false)
    {
        // check: are we still allowed to send headers?
        if (headers_sent()) {
            exit(404);
        }

        $stat = stat($filename);
        $timestamp = $stat['mtime'];

        // get: header values from client request
        $client_etag =
            !empty($_SERVER['HTTP_IF_NONE_MATCH'])
            ?   trim($_SERVER['HTTP_IF_NONE_MATCH'])
            :   null;
        $client_last_modified =
            !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            ?   trim($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            :   null;
        $client_accept_encoding =
            isset($_SERVER['HTTP_ACCEPT_ENCODING'])
            ?   $_SERVER['HTTP_ACCEPT_ENCODING']
            :   '';

        /**
         * Notes
         *
         * HTTP requires that the ETags for different responses associated with the 
         * same URI are different (this is the case in compressed vs. non-compressed
         * results) to help caches and other receivers disambiguate them.
         *
         * Further we cannot trust the client to always enclose the ETag in normal
         * quotation marks (") so we create a "raw" server sided ETag and only
         * compare if our ETag is found in the ETag sent from the client
         */

        // calculate: current/new header values
        $server_last_modified = gmdate('D, d M Y H:i:s', $timestamp) . ' GMT';
        $server_etag_raw = md5($timestamp . $client_accept_encoding . $identifier);
        $server_etag = '"' . $server_etag_raw . '"';

        // calculate: do client and server tags match?
        $matching_last_modified = $client_last_modified == $server_last_modified;
        $matching_etag = $client_etag && strpos($client_etag, $server_etag_raw) !== false;

        // set: new headers for cache recognition
        header('Last-Modified: ' . $server_last_modified);
        header('ETag: ' . $server_etag);
        header('Cache-Control: public');

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == 'css') {
            header('Content-Type: text/css');
        }
        if ($ext == 'js') {
            header('Content-Type: application/javascript');
        }
        if ($ext == 'html') {
            header('Content-Type: text/html');
        }

        if (
            ($client_last_modified && $client_etag) || $strict
            ?   $matching_last_modified && $matching_etag
            :   $matching_last_modified || $matching_etag
        ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
            exit(304);
        }

        readfile($filename);
    }


    public static function register()
    {

        BasicRoute::add('/', function ($matches) {
            http_response_code(200);
            if (($matches['path'] == '') || ($matches['path'] == '/')) return; //bsc should do that job // $matches['path']='index.html';
        }, ['get'], false);


        BasicRoute::add('/classic.json', function ($matches) {
            RouteSecurityHelper::serveSecureStaticFile(
                '/classic.json',
                Helper::getCachePath(),
                ['js', 'css', 'map', 'json', 'ttf', 'woff', 'woff2'],
                [
                    'js' => 'application/javascript',
                    'css' => 'text/css',
                    'map' => 'application/json',
                    'json' => 'application/json',
                    'ttf' => 'font/ttf',
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2'
                ]
            );
        }, ['get'], false, [
            'errorOnUnexpected' => true,
            'errorOnInvalid' => true,
            'fields' => [
                '_dc' => [
                    'required' => false,
                    'type' => 'int',
                ]
            ]
        ]);


        BasicRoute::add('/framework.js', function ($matches) {
            RouteSecurityHelper::serveSecureStaticFile(
                '/framework.js',
                Helper::getCachePath(),
                ['js', 'css', 'map', 'json', 'ttf', 'woff', 'woff2'],
                [
                    'js' => 'application/javascript',
                    'css' => 'text/css',
                    'map' => 'application/json',
                    'json' => 'application/json',
                    'ttf' => 'font/ttf',
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2'
                ]
            );
        }, ['get'], false, [
            'errorOnUnexpected' => true,
            'errorOnInvalid' => true,
            'fields' => [
                '_dc' => [
                    'required' => false,
                    'type' => 'int',
                ]
            ]
        ]);


        BasicRoute::add('/(?P<sub>(classic|resources))/(?P<path>.+)', function ($matches) {
            RouteSecurityHelper::serveSecureStaticFile(
                $matches['sub'] . '/' . $matches['path'],
                Helper::getCachePath(),
                ['js', 'css', 'map', 'json', 'ttf', 'woff', 'woff2'],
                [
                    'js' => 'application/javascript',
                    'css' => 'text/css',
                    'map' => 'application/json',
                    'json' => 'application/json',
                    'ttf' => 'font/ttf',
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2'
                ]
            );
        }, ['get'], false, [
            'errorOnUnexpected' => true,
            'errorOnInvalid' => true,
            'fields' => [
                '_dc' => [
                    'required' => false,
                    'type' => 'int',
                ]
            ]
        ]);

        BasicRoute::add('/ui/(?P<path>.+)', function ($matches) {

            RouteSecurityHelper::serveSecureStaticFile(
                $matches['path'],
                Helper::getCachePath(),
                ['js', 'css', 'map', 'json', 'ttf', 'woff', 'woff2'],
                [
                    'js' => 'application/javascript',
                    'css' => 'text/css',
                    'map' => 'application/json',
                    'json' => 'application/json',
                    'ttf' => 'font/ttf',
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2'
                ]
            );
        });
    }
}
