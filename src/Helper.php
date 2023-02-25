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


    public static function compile($config) {
        if (!isset($config['sencha_compiler_command'])) throw new \Exception("sencha_compiler_command not defined");
        if (!isset($config['sencha_compiler_source'])) throw new \Exception("sencha_compiler_source not defined");

        $params = [$config['sencha_compiler_command']];
        $params[] = 'build';
        if (isset($config['sencha_compiler_toolkit'])) $params[] = $config['sencha_compiler_toolkit'];

        $files = self::getFiles();
        $path = implode('/',[
            $config['sencha_compiler_source'],
            'classic',
            'system'
        ]);
        if (!file_exists( $path )){ mkdir($path,0777,true); }
        //array_map('unlink', glob($path."/*"));

        foreach($files as $fileItem){
            if (isset($fileItem['toolkit']) && ($fileItem['toolkit']=='classic') ){
                if (!file_exists( $path.'/'.$fileItem['modul'] )){ mkdir($path.'/'.$fileItem['modul'],0777,true); }
                foreach($fileItem['files'] as $file){
                    if (file_exists($file))
                    copy( $file, $path.'/'.$fileItem['modul'].basename($file) );
                }
            }
        }

        $path = implode('/',[
            $config['sencha_compiler_source'],
            'modern',
            'system'
        ]);
        if (!file_exists( $path )){ mkdir($path,0777,true); }
        //array_map('unlink', glob($path."/*"));
        foreach($files as $fileItem){
            if (isset($fileItem['toolkit']) && ($fileItem['toolkit']=='modern') ){
                if (!file_exists( $path.'/'.$fileItem['modul'] )){ mkdir($path.'/'.$fileItem['modul'],0777,true); }
                foreach($fileItem['files'] as $file){
                    if (file_exists($file))
                    copy( $file, $path.'/'.$fileItem['modul'].basename($file) );
                }
            }
        }

        $path = implode('/',[
            $config['sencha_compiler_source'],
            'system'
        ]);
        if (!file_exists( $path )){ mkdir($path,0777,true); }
        //array_map('unlink', glob($path."/*"));
        foreach($files as $fileItem){
            if (isset($fileItem['toolkit']) && ($fileItem['toolkit']=='') ){
                if (!file_exists( $path.'/'.$fileItem['modul'] )){ mkdir($path.'/'.$fileItem['modul'],0777,true); }
                foreach($fileItem['files'] as $file){
                    if (file_exists($file))
                    copy( $file, $path.'/'.$fileItem['modul'].basename($file) );
                }
            }
        }

        chdir($config['sencha_compiler_source']);
        exec(implode(' ',$params),$result,$return_code);
        return [
            'return_code'=>$return_code,
            'result'=>($result)
        ];
    }
}