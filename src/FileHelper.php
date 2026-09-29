<?php

namespace Tualo\Office\ExtJSCompiler;

class FileHelper {

    public static function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")&&(!is_link("$dir/$file"))) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return @rmdir($dir);
    }
    
    /**
     * @param array $skip Verzeichnisse relativ zu $path, die nicht durchlaufen werden (z. B. ['ext'])
     */
    public static function listFiles($path,&$files,$replacesubpath='',array $skip=[]){
        if ($replacesubpath=='') $replacesubpath=$path.'/';
        if (file_exists($path)){
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if ( ($file!='.') && ($file!='..') ){
                        if (is_dir($path.'/'.$file)){
                            if (in_array(str_replace($replacesubpath,'',$path.'/'.$file),$skip,true)) continue;
                            self::listFiles($path.'/'.$file,$files,$replacesubpath,$skip);
                        }else{
                            $dirname = dirname(str_replace($replacesubpath,'',$path.'/'.$file));
                            if ($dirname=='.') $dirname='';
                            $files[]=[
                                'file'=>$path.'/'.$file,
                                'subpath'=>$dirname,
                                'prio'=>0
                            ];
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    /**
     * Kopiert nur, wenn Größe oder Änderungszeit abweichen; übernimmt die mtime der Quelle.
     *
     * @return bool true, wenn kopiert wurde
     */
    public static function syncFile(string $src, string $dst): bool
    {
        clearstatcache(true, $dst);
        $srcTime = filemtime($src);
        if (is_file($dst) && filesize($src) === filesize($dst) && $srcTime === filemtime($dst)) {
            return false;
        }
        copy($src, $dst);
        touch($dst, $srcTime);
        return true;
    }
}