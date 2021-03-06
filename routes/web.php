<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::post('hangman', 'NewGameController@store')->name('new-game');

    Route::middleware('hasGameInProgress')->group(function () {
        Route::get('hangman', 'PlayGameController@index')->name('play');
        Route::post('guess/letter', 'GuessLetterController@store')->name('guess-letter');
        Route::post('guess/phrase', 'GuessPhraseController@store')->name('guess-phrase');
    });
});
