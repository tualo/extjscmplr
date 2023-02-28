<?php
namespace Tualo\Office\ExtJSCompiler;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\ExtJSCompiler\ICompiler;
use Tualo\Office\ExtJSCompiler\FileHelper;

class CompilerHelper {
    
    public static function getFiles($dir,$modul,$prio){
        $files = [];
        $l = [];
        FileHelper::listFiles($dir."/js/modern",$l);
        $files[] = [
            'prio'=>$prio,
            'toolkit'=>'modern',
            'modul'=>$modul,
            'files'=>$l
        ];  

        $l = [];
        FileHelper::listFiles($dir."/js/classic",$l);
        $files[] = [
            'prio'=>$prio,
            'toolkit'=>'classic',
            'modul'=>$modul,
            'files'=>$l
        ];  
        
        $l = [];
        FileHelper::listFiles($dir."/js/both",$l);
        $files[] = [
            'prio'=>$prio,
            'toolkit'=>'',
            'modul'=>$modul,
            'files'=>$l
        ];
        return $files;
    }
}