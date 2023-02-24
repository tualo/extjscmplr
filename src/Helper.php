<?php
namespace Tualo\Office\ExtJSCompiler;

class Helper {

    public static function getFiles(){
        $files=[];
        $classes = get_declared_classes();
        foreach($classes as $cls){
            $class = new \ReflectionClass($cls);
            if ( $class->implementsInterface('Tualo\Office\ExtJSCompiler\ICompiler') ) {
                $files=array_merge($files,$cls::getFiles());
            }
        }
        $files=array_merge($files,self::getOldFashioned());
        return $files;
    }

    public static function getOldFashioned() {
        try{
            $allfiles = [];
            foreach (glob(dirname(__DIR__,4).'/cmp/*/compile.json') as $filename) {
                $list = json_decode(file_get_contents($filename),true);
                if (!is_array($list)) continue;
                $l = [];
                foreach($list as $entry){
                    if (is_array($entry)){
                        $l[] = dirname($filename).'/'.$entry[0];
                    }else{
                        $l[] = dirname($filename).'/'.$entry;
                    }
                }
                $allfiles[]=[
                    'toolkit'=>'classic',
                    'modul'=>basename((dirname($filename))),
                    'files'=>$l
                ];
            }
        }catch(\Exception $e){
        }
        return $allfiles;
    }
}