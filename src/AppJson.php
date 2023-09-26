<?php

namespace Tualo\Office\ExtJSCompiler;


class AppJson {

    public static function get($key=''){
        if ($key=='') return self::$data;
        if (isset(self::$data[$key])) return self::$data[$key];
        return null;
    }
    public static function set($key,$value):void{
        self::$data[$key]=$value;
        return;
    }

    public static function appendElement($key,$value):void{
        self::$data[$key][]=$value;
    }

    public static function append($key,$value):void{
        if (isset(self::$data[$key])){
            /*if (is_array(self::$data[$key])){ 
                self::$data[$key][]=$value;
            }else{
             */   
                self::$data[$key]+=$value;
            //}
        }
        return;
    }

    private static $data = [
        "name"=> "Tualo",
        "namespace"=> "Tualo",
        "version"=> "1.0.0.0",
        "framework"=> "ext",
        "requires"=> [
            "font-awesome",
            "ux",
            "charts",
            "ext-locale"
        ],
        "locale" => "de",
        "indexHtmlPath"=> "index.html",
        "classpath"=> [
            "app",
            "$"."{toolkit.name}/src"
        ],
        "overrides"=> [
            "overrides",
            "$"."{toolkit.name}/overrides"
        ],
        "fashion"=> [
            "missingParameters"=> "error",
            "inliner"=> [
                "enable"=> false
            ]
        ],
        "sass"=> [
            "namespace"=> "Tualo",
            "etc"=> [
                "sass/etc/all.scss",
                "$"."{toolkit.name}/sass/etc/all.scss"
            ],
            "var"=> [
                "sass/var/all.scss",
                "sass/var",
                "$"."{toolkit.name}/sass/var/all.scss",
                "$"."{toolkit.name}/sass/var"
            ],
            "src"=> [
                "sass/src",
                "$"."{toolkit.name}/sass/src"
            ]
        ],
        "js"=> [
            [
                "path"=> "app.js",
                "bundle"=> true
            ]
        ],
        "classic"=> [
            "js"=> [
                // Remove this entry to individually load sources from the framework.
                [
                    "path"=> "$"."{framework.dir}/build/ext-all-rtl-debug.js"
                ]
            ]
        ],
        "modern"=> [
            "js"=> [
                // Remove this entry to individually load sources from the framework.
                [
                    "path"=> "$"."{framework.dir}/build/ext-modern-all-debug.js"
                ]
            ]
        ],
        "css"=> [
            [
                "path"=> "$"."{build.out.css.path}",
                "bundle"=> true,
                "exclude"=> ["fashion"]
            ]
        ],
        "loader"=> [
            "cache"=> true,
            "cacheParam"=> "_dc"
        ],
        "production"=> [
            "output"=> [
                "appCache"=> [
                    "enable"=> true,
                    "path"=> "cache.appcache"
                ]
            ],
            "loader"=> [
                "cache"=> "$"."{build.timestamp}"
            ],
            "cache"=> [
                "enable"=> true
            ],
            "compressor"=> [
                "type"=> "yui"
            ]
        ],
        "testing"=> [
        ],
        "development"=> [
            "watch"=> [
                "delay"=> 250
            ]
        ],
        "bootstrap"=> [
            "base"=> "$"."{app.dir}",
            "manifest"=> "$"."{build.id}.json",
            "microloader"=> "bootstrap.js",
            "css"=> "bootstrap.css"
        ],
        "output"=> [
            "base"=> "$"."{workspace.build.dir}/$"."{build.environment}/$"."{app.name}",
            "page"=> "index.html",
            "microloader"=> "bootstrap.js",
            "manifest"=> "$"."{build.id}.json",
            "js"=> "$"."{build.id}/app.js",
            "appCache"=> [
                "enable"=> true
            ],
            "resources"=> [
                "path"=> "$"."{build.id}/resources",
                "shared"=> "resources"
            ]
        ],
        "cache"=> [
            "enable"=> true,
            "deltas"=> "$"."{build.id}/deltas"
        ],
        "appCache"=> [
            "cache"=> [
                "index.html"
            ],
            "network"=> [
                "*"
            ],
            "fallback"=> []
        ],
        "resources"=> [
            [
                "path"=> "resources",
                "output"=> "shared"
            ],
            [
                "path"=> "$"."{toolkit.name}/resources"
            ],
            [
                "path"=> "$"."{build.id}/resources"
            ]
        ],
        "archivePath"=> "archive/$"."{build.id}",
        "builds"=> [
            "classic"=> [
                "toolkit"=> "classic",
                "theme"=> "theme-triton",
                "sass"=> [
                    "generated"=> [
                        "var"=> "classic/sass/save.scss",
                        "src"=> "classic/sass/save"
                    ]
                ]
            ],
    
            "modern"=> [
                "toolkit"=> "modern",
                "theme"=> "theme-material",
                "sass"=> [
                    "generated"=> [
                        "var"=> "modern/sass/save.scss",
                        "src"=> "modern/sass/save"
                    ]
                ]
            ]
        ],
        "ignore"=> [
            "(^|/)CVS(/?$|/.*?$)"
        ],
        "id"=> "3990dd8d-734c-4c0a-b00d-5b2b262fca3f"    
            
    ];

}