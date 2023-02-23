<?php

namespace Tualo\Office\ExtJSCompiler\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;

class Read implements IRoute{

    public static function register(){

        BasicRoute::add('/compiler',function($matches){

            $db = App::get('session')->getDB();
            $db->direct('SET SESSION group_concat_max_len = 4294967295;');
            try{
                App::result('data', dirname(__DIR__,5));

                $allfiles = [];
                foreach (glob(dirname(__DIR__,5).'/cmp/*/compile.json') as $filename) {
                    $list = json_decode(file_get_contents($filename),true);
                    $localindex = 0;
                    if (!is_array($list)) continue;
                    foreach($list as $entry){
                        if (is_array($entry)){
                            $allfiles[] = [
                                'filename'=>dirname($filename).'/'.$entry[0],
                                'priority'=>sprintf('%06d',intval($entry[1])).sprintf('%06d',$localindex)
                            ];
                        }else{
                            $allfiles[] = [
                                'filename'=>dirname($filename).'/'.$entry,
                                'priority'=>sprintf('%06d',99999).sprintf('%06d',$localindex)
                            ];
                        }
                        $localindex++;
                    }
                }
                App::result('compile', $allfiles);
                
                App::result('success', true);
            }catch(\Exception $e){
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        },['get','post'],true);
    }
}
