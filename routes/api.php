<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:web'])->namespace('Hanoivip\Userinfo\Controllers')->group(function () {
// TODO: need protection
Route::any('/admin/pass/reset', 'AdminController@resetPassword');
Route::any('/admin/token', 'AdminController@generateToken');
Route::any('/admin/user', 'AdminController@userInfo');
});