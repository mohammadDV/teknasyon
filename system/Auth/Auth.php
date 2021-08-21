<?php

namespace System\Auth;
use System\Database\DBConnection\Connection;

use App\User;
use System\Session\Session;

class  Auth {

    private $redirectTo     = "/login";
    private $sessionName    = "user";

    private function userMethod(){
        if (!Session::get($this->sessionName)){
            return redirect($this->redirectTo);
        }

        $user = User::find(Session::get($this->sessionName));
        if (empty($user)){
            Session::remove($this->sessionName);
            return redirect($this->redirectTo);
        }else{
            return $user;
        }
    }

    private function checkMethod(){
        if (!Session::get($this->sessionName)){
            return redirect($this->redirectTo);
        }

        $user = User::find(Session::get($this->sessionName));
        if (empty($user)){
            Session::remove($this->sessionName);
            return redirect($this->redirectTo);
        }else{
            return true;
        }
    }

    private function checkLoginMethod()
    {
        if (!Session::get($this->sessionName)){
            return false;
        }

        $user = User::find(Session::get($this->sessionName));
        if (empty($user)){
            return false;
        }else{
            return true;
        }
    }

    private function loginByIdMethod($id){
        $user = User::find($id);
        if (empty($user)){
            error('login','not found email');
            return false;
        }else{
            Session::set($this->sessionName,$user->id);
            return true;
        }
    }

    private function loginByEmailMethod($email,$password){
        $user = User::where('email',$email)->first();
        if (empty($user)){
            error('login','not found email');
            return false;
        }

        if (password_verify($password,$user->password)){
            Session::set($this->sessionName,$user->id);
            return true;
        }else{
            error("login","wrong password");
            return false;
        }
    }

    private function logoutMethod(){
        Session::remove($this->sessionName);
    }

    public function __call($name, $arguments)
    {
        return $this->methodCaller($name,$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = new self();
        return $instance->methodCaller($name,$arguments);
    }

    private function methodCaller($method,$args)
    {
        $suffix = 'Method';
        $methodName = $method . $suffix;
        return call_user_func_array([$this,$methodName],$args);
    }
}