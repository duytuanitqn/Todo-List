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
$route->get('/', 'Modules\TodoList\Controller\TodoListController@index');
$route->group('/todo-list', function(){
    $this->get('/index', 'Modules\TodoList\Controller\TodoListController@index');
    $this->get('/create', 'Modules\TodoList\Controller\TodoListController@create');
    $this->post('/store', 'Modules\TodoList\Controller\TodoListController@store');
});
$route->end(); 