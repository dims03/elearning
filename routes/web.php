<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('landing');

Route::get('/admin/access', function (Request $request) {
    $request->session()->put('admin_login_shortcut_unlocked', true);

    return redirect()->route('filament.admin.auth.login');
})->name('admin.shortcut');
