<?php

/*
  |--------------------------------------------------------------------------
  | Routes File
  |--------------------------------------------------------------------------
  |
  | Here is where you will register all of the routes in an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

use App\Http\Controllers\MachineController;
use App\Exceptions\CoherenceException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/run', function (Request $request) {
    $text = str_replace(chr(13), "", $request->input("text"));
    $status = 200;
    try {
        $contr = new MachineController();
        $contr->run_machine($text);
        $content = json_encode([
            'error' => false,
            'result' => $contr->getOutput()
        ]);
    } catch (CoherenceException $ex) {
        $content = json_encode([
            'error' => [
                'message' => $ex->getMessage(),
                'code' => $ex->getCode(),
                'line' => ($ex->getLine() + 1)
            ],
            'result' => false
        ]);
    }
    return (new Response($content, $status))->header('Content-Type', 'application/json');
});
/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | This route group applies the "web" middleware group to every route
  | it contains. The "web" middleware group is defined in your HTTP
  | kernel and includes session state, CSRF protection, and more.
  |
 */

Route::group(['middleware' => ['web']], function () {
    
});
