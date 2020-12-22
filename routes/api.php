<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => config('knowledge-base.authorization')], function () {
    Route::get('kb/update-cache', 'KnowledgeBaseAPIController@getUpdateCache');
});

Route::get('kb/article/{category_slug}/{article_slug}', 'KnowledgeBaseAPIController@getArticle');
Route::get('kb/search/{term}', 'KnowledgeBaseAPIController@getSearch');
Route::get('kb/category/{slug}', 'KnowledgeBaseAPIController@getCategory');
Route::get('kb/{category_slug?}', 'KnowledgeBaseAPIController@getIndex');
