<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use DOMDocument;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\PUG\CIDR;
use Tualo\Office\ExtJSCompiler\Helper;

class Read implements IRoute
{

    public static function register()
    {

        BasicRoute::add('/phpinfo', function ($matches) {
            if (App::configuration('compiler', 'phpinfo', false) !== true) {
                phpinfo();
                exit();
            }
            // 
            exit();
        }, ['get', 'post'], true);

        BasicRoute::add('/compiler_extract', function ($matches) {

            Helper::extract();
            App::contenttype('application/json');
            App::result('success', true);
        }, ['get', 'post'], true);


        BasicRoute::add('/compiler_files', function ($matches) {
            App::result('files', Helper::getFiles());
            App::result('success', true);
            App::contenttype('application/json');
        }, ['get', 'post'], true);


        BasicRoute::add('/compiler_copy', function ($matches) {

            App::result('compile', Helper::copy());
            App::result('success', true);
            App::contenttype('application/json');
        }, ['get', 'post'], true);


        $needsLogin = boolval(!App::configuration('compiler', 'allowedExtern', false));

        if ($needsLogin == true) {

            $keys =  json_decode(App::configuration('compiler', 'allowed_clientip_headers', "['HTTP_X_DDOSPROXY', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR']"), true);
            if (is_null($keys)) {
                $keys = ['HTTP_X_DDOSPROXY', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
            }
            if (CIDR::IPisWithinCIDR(
                CIDR::getIP($keys),
                explode(' ', App::configuration('compiler', 'allowed_cidrs', '127.0.0.1'))
            )) {
                $needsLogin = false;
            } else {
                $needsLogin = true;
            }
        }

        BasicRoute::add('/compiler', function ($matches) {
            App::contenttype('application/json');


            try {


                $client = Helper::getCurrentClient();

                $res = Helper::compile($client);
                App::result('compile', $res);

                if ($res['return_code'] != 0) {
                    foreach ($res['data'] as $row) {
                        if ($row['level'] == '[ERR]') {
                            App::result('msg', $row['note']);
                            break;
                        }
                    }
                    if ($res['return_code'] == 127) {
                        App::result('msg', 'compiler not found');
                    }
                    App::result('success', false);
                } else {

                    App::result('success', true);
                }
            } catch (\Exception $e) {
                App::result('msg', $e->getMessage());
                BasicRoute::$finished = true;
            }
        }, ['get', 'post'], $needsLogin);
    }
}
