<?php
namespace Tualo\Office\ExtJSCompiler;
use DOMDocument;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\ExtJSCompiler\FileHelper;
use Tualo\Office\ExtJSCompiler\AppJson;

class Helper {
    
    public static function getBuildPath(){
        $config = App::get('configuration');
        if (!isset($config['ext-compiler'])) throw new \Exception("ext-compiler section not defined");
        $compiler_config = $config['ext-compiler'];
        return implode('/',[
            App::get('basePath'),
            'ext-build',
            self::getCurrentClient(),
            'build',
            'production',
            'Tualo'
        ]);
    }

    public static function getCachePath(){
        $config = App::get('configuration');
        if (!isset($config['ext-compiler'])) throw new \Exception("ext-compiler section not defined");
        $compiler_config = $config['ext-compiler'];
        return implode('/',[
            App::get('basePath'),
            'ext-cache',
            self::getCurrentClient()
        ]);
    }

    public static function extract(){
        $doc = new DOMDocument();
        $doc->loadHTMLFile(Helper::getCachePath().'/index.html');
        $elements = $doc->getElementsByTagName('script');
        $index =0;
        if (!is_null($elements)) {
            foreach ($elements as $element) {
                if ($index==0){
                    file_put_contents(Helper::getCachePath().'/ext_start.js',$element->textContent);
                }else if ($index==1){
                    file_put_contents(Helper::getCachePath().'/bootstrap.js',$element->textContent);
                }
                $index++;
            }
        }
    }

    public static function getCurrentClient(){
        $client='default';
        if (
            isset($_SESSION['tualoapplication']) &&
            isset($_SESSION['tualoapplication']['loggedIn']) &&
            $_SESSION['tualoapplication']['loggedIn'] === true
        ){
            $client = $_SESSION['tualoapplication']['client'];
        }
        return $client;
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
        //$files=array_merge($files,self::getOldFashioned());
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
                                'subpath'=>dirname($entry[0]),
                                'prio'=>sprintf('%06d',intval($entry[1])).sprintf('%06d',$localindex++)
                        ];
                    }else{
                        $l[] = [ 
                            'file'=>dirname($filename).'/'.$entry,
                            'subpath'=>dirname($entry),
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

    public static function copySource($from,$to):array{
        $copiedFiles=[];
        //if ( file_exists( $to )) FileHelper::delTree($to);
        if (!file_exists( $to )) { mkdir($to,0777,true); }
        $files=[];
        FileHelper::listFiles($from,$files);
        //App::logger('compiler')->info('my message');
        foreach($files as $file){
            if($file['subpath']!='')$file['subpath']='/'.$file['subpath'];
            

            if(
                ( $file['subpath'] == '/ext') ||
                (strpos($file['subpath'],'/ext/')===0) 
            ){

            }else{
                if (!file_exists( $to.$file['subpath'] )){ mkdir($to.$file['subpath'],0777,true); }

                // copy( $file['file'],$to.$file['subpath'].'/'.basename($file['file'] ));

                $originalHash = ' ';
                $destinationHash = '';
                if (file_exists($file['file'])) $originalHash = md5_file($file['file']);
                if (file_exists($to.$file['subpath'].'/'.basename($file['file'] )))
                $destinationHash = md5_file($to.$file['subpath'].'/'.basename($file['file'] ));
                if ($originalHash == $destinationHash) {
                    
                }else{
                    copy( $file['file'],$to.$file['subpath'].'/'.basename($file['file'] ));
                    $copiedFiles[] = $file['subpath'].'/'.basename($file['file'] );
                }

                if (basename($file['file'] )=='app.js'){
                    file_put_contents(
                        $to.$file['subpath'].'/'.basename($file['file'] ),
                        str_replace(
                            "mainView: 'Tualo.view.main.Main'",
                            "mainView: 'Tualo.view.main.Main'",
                            file_get_contents($to.$file['subpath'].'/'.basename($file['file'] ))
                        )
                    );
                }
                
            }
        }
        return $copiedFiles;
    }

    public static function rglob(string $patterns, $flags = GLOB_NOSORT): array {
        $result = glob($patterns, $flags);
        foreach ($result as $item) {
            if (is_dir($item)) {
                array_push($result, ...self::rglob($item . '/*', $flags));
            }
        }
    
        return $result;
    }

    public static function copy($config):array {
        if (!isset($config['sencha_compiler_command'])) throw new \Exception("sencha_compiler_command not defined");
        if (!isset($config['sencha_compiler_sdk'])){ throw new \Exception("sencha_compiler_sdk not defined"); }
        if (!isset($config['sencha_compiler_source'])){
            $config['sencha_compiler_source'] = dirname(__DIR__,1).'/compiler_source/Tualo';
        }
        $copiedFiles = self::copySource( $config['sencha_compiler_source'], self::getBuildPath() );
        if (!file_exists( self::getBuildPath().'/ext' )){
            symlink($config['sencha_compiler_sdk'].'', self::getBuildPath().'/ext');
        }

        $append_modules=[];
        $files = self::getFiles();



        $oldFiles = self::rglob(self::getBuildPath().'/classic/*',GLOB_NOSORT);
        $oldFiles = array_merge($oldFiles,self::rglob(self::getBuildPath().'/modern/*',GLOB_NOSORT));
        $oldFiles = array_merge($oldFiles,self::rglob(self::getBuildPath().'/both/*',GLOB_NOSORT));


        $newFiles = [];

        $toolkits = ['classic','modern',''];
        foreach($toolkits as $toolkit){
            $path = implode('/',[
                self::getBuildPath(),
                (($toolkit=='')?'both':$toolkit),
                'src',
                'system'
            ]);
            if (!file_exists( $path )){ mkdir($path,0777,true); }
            
            // FileHelper::delTree($path);
            foreach($files as $fileItem){
                
                if (isset($fileItem['toolkit']) && ($fileItem['toolkit']==$toolkit) ){
                    if(!in_array($toolkit==''?'both':$toolkit,$append_modules)) $append_modules[] = $toolkit==''?'both':$toolkit;

                    if (!file_exists( $path.'/'.$fileItem['modul'] )){ mkdir($path.'/'.$fileItem['modul'],0777,true); }
                    foreach($fileItem['files'] as $filelistitem){
                        if (!isset($filelistitem['file'])){
                            print_r($fileItem);
                            exit();
                        }
                        if (file_exists($filelistitem['file'])){
                            if($filelistitem['subpath']!='')$filelistitem['subpath']='/'.$filelistitem['subpath'];
                            if (!file_exists( $path.'/'.$fileItem['modul'].'/'.$filelistitem['subpath']) ){ mkdir($path.'/'.$fileItem['modul'].$filelistitem['subpath'],0777,true); }

                            $originalHash = ' ';
                            $destinationHash = '';
                            if (file_exists($filelistitem['file'])) $originalHash = md5_file($filelistitem['file']);
                            if (file_exists($path.'/'.$fileItem['modul'].$filelistitem['subpath'].'/'.basename($filelistitem['file'])))
                            $destinationHash = md5_file(  $path.'/'.$fileItem['modul'].$filelistitem['subpath'].'/'.basename($filelistitem['file'])  );

                            $newFiles[] = $path.'/'.$fileItem['modul'].$filelistitem['subpath'].'/'.basename($filelistitem['file']);

                            if ($originalHash == $destinationHash) {
                                
                            }else{
                                copy( $filelistitem['file'], $path.'/'.$fileItem['modul'].$filelistitem['subpath'].'/'.basename($filelistitem['file']) );
                                $copiedFiles[] = $fileItem['modul'].$filelistitem['subpath'].'/'.basename($filelistitem['file']);
                            }

                            
                            
                        }
                    }
                }
            }
    

        }


        AppJson::append('classpath',$append_modules);
        file_put_contents(implode('/',[
            self::getBuildPath(),
            'app.json'
        ]),json_encode(AppJson::get(),JSON_PRETTY_PRINT));

        $diff = (array_diff($oldFiles,$newFiles));
        foreach($diff as $diffitem){
            if (!is_dir($diffitem)){
                unlink($diffitem);
            }
        }
        return [$copiedFiles,$append_modules];
    }

    public static function compile($config) {


        if (!isset($config['sencha_compiler_command'])) throw new \Exception("sencha_compiler_command not defined");
        if (!isset($config['sencha_compiler_sdk'])){ throw new \Exception("sencha_compiler_sdk not defined"); }
        if (!isset($config['sencha_compiler_source'])){
            $config['sencha_compiler_source'] = dirname(__DIR__,1).'/compiler_source/Tualo';
        }
        
        list($copiedfiles,$append_modules) = self::copy($config);


        chdir( self::getBuildPath() );
        # exec('export _JAVA_OPTIONS=-Xms2048m -Xmx8192m -Dapple.awt.UIElement=true');

        $params = [$config['sencha_compiler_command']];
        $params[] = 'build';
        if (isset($config['sencha_compiler_toolkit'])) $params[] = $config['sencha_compiler_toolkit'];
        exec(implode(' ',$params),$result,$return_code);
        $data = [];
        $index = 0;
        foreach($result as $row){
            preg_match('/(?P<level>\[(\w+)\])\s(?P<note>.+)/', $row, $matches, PREG_OFFSET_CAPTURE);
            if (isset($matches['note'])&&isset($matches['level']))
            $data[] = [
                'index'=>$index++,
                'note'=>str_replace(self::getBuildPath() ,'.',$matches['note'][0]),
                'level'=>$matches['level'][0]
            ];
        }

        if ($return_code==0){
            $res = Helper::copySource( Helper::getBuildPath().'/build/production/Tualo' , Helper::getCachePath() );
            self::extract();
        }
        return [
            'return_code'=>$return_code,
            'cmd'=>implode(' ',$params),
            'pwd'=>self::getBuildPath(),
            // 'result'=>($result),
            'data'=>($data)
        ];
    }
}