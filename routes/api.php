<?php
use System\Router\Api\Route;

Route::get("/execute","ApiController@execute","execute");
Route::get("/check/{token}","ApiController@check","check");
Route::post("/register","ApiController@register","register");
Route::post("/purchase","ApiController@purchase","purchase");
Route::post("/set","ApiController@set","set");
