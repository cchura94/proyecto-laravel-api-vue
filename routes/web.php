<?php

use Illuminate\Support\Facades\Route;


/*
Route::get("/miurl", function(){
    return view("prueba");
});
*/

Route::get('/', function () {
    return view('index');
});

Route::get('/{any}', function () {
    return view('index');
})->where('any', '.*');
