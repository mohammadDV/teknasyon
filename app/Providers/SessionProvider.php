<?php


namespace App\Providers;

class SessionProvider extends Provider {

    public function boot()
    {
        session_start();
        if (isset($_SESSION['old'])) unset($_SESSION['temporary_old']);
        if (isset($_SESSION['old'])) {
            $_SESSION['temporary_old'] = $_SESSION["old"];
            unset($_SESSION["old"]);
        }

        $params             = [];
        $params             = !isset($_GET) ? $params : array_merge($params,$_GET);
        $params             = !isset($_POST) ? $params : array_merge($params,$_POST);
        $_SESSION["old"]    = $params;

        if (isset($_SESSION['flash'])) unset($_SESSION['temporary_flash']);
        if (isset($_SESSION['flash'])) {
            $_SESSION['temporary_flash'] = $_SESSION["flash"];
            unset($_SESSION["flash"]);
        }
    }
}