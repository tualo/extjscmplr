<?php
namespace Tualo\Office\ExtJSCompiler;

class Helper {

    public static function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

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

    public static function cmp_tualo_file_sort($a,$b) {
        if ($a['prio']==$b['prio']) return 0;
        return ($a['prio']<$b['prio'])?-1:1;
    }
    
    public static function getOldFashioned() {
        try{
            $allfiles = [];
            $localindex=0;
            foreach (glob(dirname(__DIR__,4).'/cmp/*/compile.json') as $filename) {
                $list = json_decode(file_get_contents($filename),true);
                if (!is_array($list)) continue;
                $l = [];
                $min_prio='999999';
                foreach($list as $entry){
                    if (is_array($entry)){
                        $l[]=[ 
                                'file'=>dirname($filename).'/'.$entry[0],
                                'prio'=>sprintf('%06d',intval($entry[1])).sprintf('%06d',$localindex++)
                        ];
                    }else{
                        $l[] = [ 
                            'file'=>dirname($filename).'/'.$entry[0],
                            'prio'=>sprintf('%06d',99999).sprintf('%06d',$localindex++)
                        ];
                    }
                    $min_prio=min($min_prio,$l[count($l)-1]['prio']);
                }
                
                $allfiles[]=[
                    'toolkit'=>'classic',
                    'modul'=>basename((dirname($filename))),
                    'files'=>$l,
                    'prio'=>$min_prio
                ];
            }

            usort($allfiles,'Tualo\Office\ExtJSCompiler\Helper::cmp_tualo_file_sort');

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
            'src',
            'system'
        ]);
        if (!file_exists( $path )){ mkdir($path,0777,true); }
        self::delTree($path);
        //array_map('unlink', glob($path."/*"));

        foreach($files as $fileItem){
            if (isset($fileItem['toolkit']) && ($fileItem['toolkit']=='classic') ){
                if (!file_exists( $path.'/'.$fileItem['modul'] )){ mkdir($path.'/'.$fileItem['modul'],0777,true); }
                foreach($fileItem['files'] as $filelistitem){
                    if (file_exists($filelistitem['file']))
                    copy( $filelistitem['file'], $path.'/'.$fileItem['modul'].'/'.basename($filelistitem['file']) );
                }
            }
        }

        $path = implode('/',[
            $config['sencha_compiler_source'],
            'modern',
            'src',
            'system'
        ]);
        if (!file_exists( $path )){ mkdir($path,0777,true); }
        self::delTree($path);
        //array_map('unlink', glob($path."/*"));
        foreach($files as $fileItem){
            if (isset($fileItem['toolkit']) && ($fileItem['toolkit']=='modern') ){
                if (!file_exists( $path.'/'.$fileItem['modul'] )){ mkdir($path.'/'.$fileItem['modul'],0777,true); }
                foreach($fileItem['files'] as $file){
                    if (file_exists($file))
                    copy( $file, $path.'/'.$fileItem['modul'].'/'.basename($file) );
                }
            }
        }

        $path = implode('/',[
            $config['sencha_compiler_source'],
            'system'
        ]);
        if (!file_exists( $path )){ mkdir($path,0777,true); }
        self::delTree($path);
        //array_map('unlink', glob($path."/*"));
        foreach($files as $fileItem){
            if (isset($fileItem['toolkit']) && ($fileItem['toolkit']=='') ){
                if (!file_exists( $path.'/'.$fileItem['modul'] )){ mkdir($path.'/'.$fileItem['modul'],0777,true); }
                foreach($fileItem['files'] as $file){
                    if (file_exists($file))
                    copy( $file, $path.'/'.$fileItem['modul'].'/'.basename($file) );
                }
            }
        }

        chdir($config['sencha_compiler_source']);
        exec(implode(' ',$params),$result,$return_code);
        $data = [];
        $index = 0;
        foreach($result as $row){
            
            preg_match('/(?P<level>\[(\w+)\])\s(?P<note>.+)/', $row, $matches, PREG_OFFSET_CAPTURE);
            if (isset($matches['note'])&&isset($matches['level']))
            $data[] = [
                'index'=>$index++,
                'note'=>str_replace($config['sencha_compiler_source'],'.',$matches['note'][0]),
                'level'=>$matches['level'][0]
            ];
        }
        return [
            'return_code'=>$return_code,
            // 'result'=>($result),
            'data'=>($data)
        ];
    }
}