<?php

function view($dir,array $vars = []){
    $viewBuilder = new \System\View\viewBuilder();
    $viewBuilder->run($dir);
    $viewVars   = $viewBuilder->vars;
    $content    = $viewBuilder->content;
    empty($viewVars)    ? : extract($viewVars);
    empty($vars)        ? : extract($vars);
    eval(" ?>" . html_entity_decode($content)); // for php code execute
}

function dd($var,$die = true){
//    echo "<pre></pre>";
    var_dump($var);
    if ($die){
        exit();
    }
}

function html($text){
    return html_entity_decode($text);
}

function old($name){
    if (isset($_SESSION["temporary_old"][$name])){
        return $_SESSION["temporary_old"][$name];
    }
    return null;
}

function flash($name,$message){
    if (empty($message)){
        if (isset($_SESSION["temporary_flash"][$name])){
            $temporary = $_SESSION["temporary_flash"][$name];
            unset($_SESSION["temporary_flash"][$name]);
            return $temporary;
        }else{
            return false;
        }
    }else{
        $_SESSION["flash"][$name] = $message;
    }
}

function flashExists($name){
    return isset($_SESSION["temporary_flash"][$name]) === true ? true : false;
}

function allFlashes($name){
    if (isset($_SESSION["temporary_flash"][$name])){
        $temporary = $_SESSION["temporary_flash"][$name];
        unset($_SESSION["temporary_flash"][$name]);
        return $temporary;
    }else{
        return false;
    }
}

function error($name, $message = null)
{
    if(empty($message))
    {
        if (isset($_SESSION["temporary_errorFlash"][$name])) {
            $temporary = $_SESSION["temporary_errorFlash"][$name];
            unset($_SESSION["temporary_errorFlash"][$name]);
            return $temporary;
        }
        else{
            return false;
        }
    }else{
        $_SESSION["errorFlash"][$name] = $message;
    }
}

function errorExists($name)
{
    return isset($_SESSION["temporary_errorFlash"][$name]) === true ? true : false;
}

function allErrors()
{
    if (isset($_SESSION["temporary_errorFlash"])) {
        $temporary = $_SESSION["temporary_errorFlash"];
        unset($_SESSION["temporary_errorFlash"]);
        return $temporary;
    }
    else{
        return false;
    }
}


function currentDomain(){
    $httpProtocol   = (isset($_SERVER["HTTPS"]) && $_SERVER['HTTPS'] === "on") ? "https://" : "http://";
    $currentUrl     = $_SERVER["HTTP_HOST"];
    return $httpProtocol . $currentUrl;
}

function redirect($url){
    $url = trim($url,"/ ");
    $url = strpos("z" .$url,currentDomain()) == true ? $url : currentDomain() . "/" . $url;
    header("Location: " . $url);
    exit();
}

function back(){
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    redirect($referer);
}

function asset($src){
    return currentDomain() . "/" . trim($src,"/ ");
}

function url($src){
    return currentDomain() . "/" . trim($src,"/ ");
}

function findRouteByName($name){
    global $gl_routes;
    $allRoutes  = array_merge($gl_routes["get"],$gl_routes["post"],$gl_routes["put"],$gl_routes["delete"]);
    $route      = null;
    foreach ($allRoutes as $item){
        if (!empty($item["name"]) && $item["name"] == $name){
            $route = $item["url"];
            break;
        }
    }
    return $route;
}


function route($name,array $params = []){
    $route = findRouteByName($name);
    if (empty($route)){
        throw new \System\Lib\Exp(__tr("route not found"),1013);
    }
    $params             = array_reverse($params);
    $routeParamsMatch   = [];

    preg_match_all("/{[^}.]*}/",$route,$routeParamsMatch);

    if (!empty($routeParamsMatch[0]) && count($routeParamsMatch[0]) > count($params)){
        throw new \System\Lib\Exp(__tr("route params not enough!"),1014);
    }

    if (!empty($routeParamsMatch[0])){
        foreach ($routeParamsMatch[0] as $key => $routeMatch) {
            $route = str_replace($routeMatch,array_pop($params),$route);
        }
    }

    return currentDomain() . "/" . trim($route," /");
}

function __tr($key){
    return $key;
}

function generateToken(){
    return bin2hex(openssl_random_pseudo_bytes(32));
}

function methodField(){
    $methodField = strtolower($_SERVER['REQUEST_METHOD']);
    if ($methodField == 'post'){
        if (isset($_POST['_method'])){
            if ($_POST['_method'] == 'put'){
                $methodField = "put";
            }elseif ($_POST['_method'] == 'delete'){
                $methodField = "delete";
            }
        }
    }

    return $methodField;
}

function array_dot($array,$return_array = [],$return_key = "")
{
    foreach ($array as $key => $value) {
        if (is_array($value)){
            $return_array = array_merge($return_array,$this->array_dot($value,$return_array,$return_key . $key . "."));
        }else{
            $return_array[$return_key . $key] = $value;
        }
    }

    return $return_array;
}

function currentUrl(){
    return currentDomain() . $_SERVER["REQUEST_URI"];
}

function clearStr($string,$type = 'string',$default = ""){
    if(is_array($string)){
        foreach ($string as $key => $val) $string[$key] = clear($val,$type);
        return $string;
    }else {
        $c = array('<'	,'>'	,"'"	,'"'	);
        $r = array('&lt;'	,'&gt;'	,"&#39;",'&quot;');
        if ($type == 'int') $string = intval($string) + 1 - 1;
        $string = urldecode($string);
        $string = trim(@str_replace($c,$r,$string));
        if ($type == 'int') {
            return (empty($string) && !is_int($string) && $default != 0)?false:intval($string);
        }
        return (empty($string) &&  $default != "" )?false:addslashes($string);
    }
}

function clear($value){
    return addslashes(htmlspecialchars(trim($value)));
}

function getServerIp(){
    if	(isset($_SERVER['SERVER_ADDR'])) $ip = $_SERVER['SERVER_ADDR'];
    else  $ip = 'none';
    return clearStr($ip);
}
function getIp(){
    if		(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) 	$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    elseif	(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    elseif	(isset($_SERVER['REMOTE_ADDR']))			$ip = $_SERVER['REMOTE_ADDR'];
    else  												$ip = 'none';
    return clearStr($ip);
}
function getUag(){
    if (isset($_ENV['HTTP_USER_AGENT'])) 				$uag = $_ENV['HTTP_USER_AGENT'];
    elseif (isset($_SERVER['HTTP_USER_AGENT'])) 		$uag = $_SERVER['HTTP_USER_AGENT'];
    else  $uag = 'none';
    return clearStr($uag);
}
function getRef(){
    if (isset($_ENV['HTTP_REFERER'])) 					$uag = $_ENV['HTTP_REFERER'];
    elseif (isset($_SERVER['HTTP_REFERER'])) 			$uag = $_SERVER['HTTP_REFERER'];
    else 												$uag = '';
    return clearStr(urldecode($uag));
}
function getUrl($clr = TRUE){
    if (empty($_SERVER['HTTP_HOST'])) return 'unknown!!!';
    $url = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'?'https://':'http://';
    $url .= $_SERVER['HTTP_HOST'];
    $url .= !empty($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'';
    return $clr? clearStr($url):$url;
}
function useSSL(){
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])){
        return $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
    }else if (!empty($_SERVER['REQUEST_SCHEME'])){
        return clearStr($_SERVER['REQUEST_SCHEME']);
    }else if (!empty($_SERVER['REQUEST_METHOD'])) {
        return $_SERVER['REQUEST_METHOD'];
    }else {
        return false;
    }
}

function jsonResponse($response,$message = "",$continue = false){
    if (is_array($response)){
        echo json_encode($response);
    }else{
        echo json_encode(["status" => intval($response),"message" => $message]);
    }
    if ($continue === false){
        exit();
    }
}

function convertTimeZone($number = -6){
    $UTC            = new DateTime("now", new DateTimeZone('UTC'));
    $UTC->format("Y-m-d H:i:s");
    $original       = new DateTime("now", new DateTimeZone('UTC'));
    $timezoneName   = timezone_name_from_abbr("", intval($number) *3600, false);
    $modified       = $original->setTimezone(new DateTimezone($timezoneName));
    $modified->format("Y-m-d");
    return ["UTC" => $UTC,"converted" => $modified];
}

function getLastInt($item) {
    preg_match_all('/([1-9]+)/',$item,$array);
    return substr(end($array[1]),-1);
}

function checkPurchase($receipt){
    $var    = getLastInt($receipt);
    return intval($var) % 2 ? 1 : 0;
}