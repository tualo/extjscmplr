<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\ExtJSCompiler\Helper;

class Ui implements IRoute{

    public static function readfile($filename, $identifier = "", $strict = false){
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
            :   null
        ;
        $client_last_modified =
            !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            ?   trim($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            :   null
        ;
        $client_accept_encoding =
            isset($_SERVER['HTTP_ACCEPT_ENCODING'])
            ?   $_SERVER['HTTP_ACCEPT_ENCODING']
            :   ''
        ;

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

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext=='css'){ header('Content-Type: text/css');}
        if ($ext=='js'){ header('Content-Type: application/javascript');}
        if ($ext=='html'){ header('Content-Type: text/html'); }

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


    public static function register(){
        BasicRoute::add('/(?P<path>.*)',function($matches){
            $client=Helper::getCurrentClient();
            $path = Helper::getBuildPath();
            $compiler_config = (App::get('configuration'))['ext-compiler'];
            if (!file_exists($path) || !is_dir($path)){
                Helper::compile($compiler_config, $client );
            }
            if (!file_exists($path) || !is_dir($path)){
                throw new \Exception("Version could not be build");
            }
            if (($matches['path']=='')||($matches['path']=='/')) return; //bsc should do that job // $matches['path']='index.html';
            if (!file_exists($path.'/'.$matches['path'])){
                // 
            }else{
                self::readfile($path.'/'.$matches['path']);
                exit();
            }
        },['get'],false);
    }
}
