<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Testimonial\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {
        Route::group(['prefix' => 'testimonials', 'as' => 'testimonial.'], function () {
            Route::resource('', 'TestimonialController')->parameters(['' => 'testimonial']);

            Route::delete('items/destroy', [
                'as' => 'deletes',
                'uses' => 'TestimonialController@deletes',
                'permission' => 'testimonial.destroy',
            ]);
        });
    });
});
