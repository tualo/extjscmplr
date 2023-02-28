<?php

namespace Tualo\Office\ExtJSCompiler;

class FileHelper {

    public static function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")&&(!is_link("$dir/$file"))) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
    
    public static function listFiles($path,&$files,$replacesubpath=''){
        if ($replacesubpath=='') $replacesubpath=$path.'/';
        if (file_exists($path)){
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if ( ($file!='.') && ($file!='..') ){
                        if (is_dir($path.'/'.$file)){
                            self::listFiles($path.'/'.$file,$files,$replacesubpath);
                        }else{
                            $files[]=[
                                'file'=>$path.'/'.$file,
                                'subpath'=>dirname(str_replace($replacesubpath,'',$path.'/'.$file)),
                                'prio'=>0
                            ];
                        }
                    }
                }
                closedir($handle);
            }
        }
    }
}