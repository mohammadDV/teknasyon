<?php

namespace System\Router;
use System\Config\Config;
use System\Lib\Exp;
use ReflectionMethod;
class Routing {
    private $current_route;
    private $method_field;
    private $routes;
    private $values         = [];

    public function __construct()
    {
        $this->current_route    = explode("/",Config::get('app.CURRENT_ROUTE'));
        $this->method_field    = $this->method_field();
        global $gl_routes;
        $this->routes           = $gl_routes;
    }

    public function run()
    {
        $match = $this->match();
        if (empty($match)){
            $this->notfound();
        }
        $classPath  = str_replace("\\","/",$match["class"]);
        $path       = Config::get('app.BASE_DIR') . "/app/Http/Controllers/" . $classPath . ".php";
        if (!file_exists($path)){
            throw new Exp("not found class");
        }

        $class  = "\App\Http\Controllers\\" . $match["class"];
        $object = new $class;
        if (method_exists($object,$match["method"])){
            $reflection = new ReflectionMethod($class,$match["method"]);
            $paramCount = $reflection->getNumberOfParameters();

            if ($paramCount <= count($this->values)){
                call_user_func_array([$object,$match["method"]],$this->values);
            }else{
                throw new Exp("not equal variables");
            }

        }else{
            throw new Exp("not found action");
        }
    }

    public function match()
    {
        $reserveRoute = !empty($this->routes[$this->method_field]) ? $this->routes[$this->method_field] : [];
        if (!empty($reserveRoute)){
            foreach ($reserveRoute as $item) {
                if ($this->compare($item["url"]) == true){
                    return $item;
                }else{
                    $this->values = [];
                }
            }
        }

        return [];
    }

    public function compare($reservedURL)
    {
        if (trim($reservedURL,"/") === ""){
            return trim($this->current_route[0],"/") === '' ? true : false;
        }

        $reserveArray = explode("/",$reservedURL);
        if (count($reserveArray) != count($this->current_route)){
            return false;
        }

        foreach ($this->current_route as $key => $current_route_element) {
            $reserveElement = $reserveArray[$key];
            if (substr($reserveElement,0,1) == "{" && substr($reserveElement,-1) == "}"){
                $this->values[] = $current_route_element;
            }elseif ($reserveElement != $current_route_element){
                return false;
            }
        }
        return true;
    }

    public function notfound()
    {
        http_response_code(404);
        include __DIR__ . DIRECTORY_SEPARATOR . "View" .DIRECTORY_SEPARATOR . "404.php";
        exit();
    }
    
    private function method_field(){
        $method_field  = strtolower(clearStr($_SERVER["REQUEST_METHOD"]));
        $_method        = isset($_POST["_method"]) ? clearStr($_POST["_method"]) : "";

        if ($method_field === "post"){
            if (!empty($_method)){
                if ($_method === "put"){
                    $method_field = "put";
                }elseif($_method === "delete"){
                    $method_field = "delete";
                }
            }
        }
        return $method_field;
    }
    
    
}
