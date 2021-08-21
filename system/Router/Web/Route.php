<?php


namespace System\Router\Web;

use System\Lib\Exp;

class Route {
    public static function get($url,$executeMethod,$name = null){
        $executeMethod = explode("@",$executeMethod);
        if (empty($executeMethod[0]) || empty($executeMethod[1])){
            throw new Exp("error",1515);
        }
        $class  = $executeMethod[0];
        $method = $executeMethod[1];
        global $gl_routes;
        array_push($gl_routes['get'],array("url" => trim($url,"/"),"class" => $class,"method" => $method,"name" => $name));
    }

    public static function post($url,$executeMethod,$name = null){
        $executeMethod = explode("@",$executeMethod);
        if (empty($executeMethod[0]) || empty($executeMethod[1])){
            throw new Exp("error",1010);
        }
        $class  = $executeMethod[0];
        $method = $executeMethod[1];
        global $gl_routes;
        array_push($gl_routes['post'],array("url" => trim($url,"/"),"class" => $class,"method" => $method,"name" => $name));
    }

    public static function put($url,$executeMethod,$name = null){
        $executeMethod = explode("@",$executeMethod);
        if (empty($executeMethod[0]) || empty($executeMethod[1])){
            throw new Exp("error",1010);
        }
        $class  = $executeMethod[0];
        $method = $executeMethod[1];
        global $gl_routes;
        array_push($gl_routes['put'],array("url" => trim($url,"/"),"class" => $class,"method" => $method,"name" => $name));
    }

    public static function delete($url,$executeMethod,$name = null){
        $executeMethod = explode("@",$executeMethod);
        if (empty($executeMethod[0]) || empty($executeMethod[1])){
            throw new Exp("error",1010);
        }
        $class  = $executeMethod[0];
        $method = $executeMethod[1];
        global $gl_routes;
        array_push($gl_routes['delete'],array("url" => trim($url,"/"),"class" => $class,"method" => $method,"name" => $name));
    }
}