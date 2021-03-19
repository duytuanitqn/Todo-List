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
$route->get('/', 'Modules\TodoList\Controller\TodoListController@getCalendar');
$route->group('/todo-list', function(){
    $this->get('/index', 'Modules\TodoList\Controller\TodoListController@index');
    $this->get('/create', 'Modules\TodoList\Controller\TodoListController@create');
    $this->post('/store', 'Modules\TodoList\Controller\TodoListController@store');
    $this->get('/{id}:([1-9]{1}[0-9]*)/edit', 'Modules\TodoList\Controller\TodoListController@edit');
    $this->post('/{id}:([1-9]{1}[0-9]*)/update', 'Modules\TodoList\Controller\TodoListController@update');
    $this->get('/{id}:([1-9]{1}[0-9]*)/delete', 'Modules\TodoList\Controller\TodoListController@delete');
    $this->get('/canlendar/event', 'Modules\TodoList\Controller\TodoListController@getEventCalendar');
});
$route->end(); 