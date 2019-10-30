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
Route::get('/','TreeController@index')->name('index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/tree/{id}','TreeController@detail')->name('detail');
Route::get('/nodes/{tree_id}','TreeController@getchildren')->name('get_nodes');
Route::post('/nodes','TreeController@create_node')->name('create_node');
Route::post('/nodes/delete','TreeController@delete_node')->name('delete_node');
Route::post('/nodes/patch','TreeController@patch')->name('update_node');
Route::get('/mytrees','TreeController@mytrees')->name('my_trees');
Route::post('/tree','TreeController@add_tree')->name('add_tree');
