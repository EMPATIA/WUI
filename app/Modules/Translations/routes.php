<?php

Route::group(array('module' => 'Translations', 'namespace' => 'App\Modules\Translations\Controllers', 'middleware' =>['web','privateAuthOne'],), function() {

    Route::get("translations/saveAllStrings", 'TranslationsController@saveAllStrings');
    Route::get("translations/getAllStrings", 'TranslationsController@getAllStrings');
    Route::get("translations/manageAllEmptyTranslations", ['as' => 'translations.manage_empty', 'uses' => 'TranslationsController@manageAllEmptyTranslations']);
    Route::post("translations/getGroups", 'TranslationsController@getGroups');
    Route::post("translations/getKeys", 'TranslationsController@getKeys');
    Route::post("translations/searchKey", 'TranslationsController@searchKey');
    Route::post("translations/getAllEmptyTranslations", 'TranslationsController@getAllEmptyTranslations');

    Route::post("translations/getAddTranslationBox", 'TranslationsController@getAddTranslationBox');
    Route::post("translations/getEditTranslationBox", 'TranslationsController@getEditTranslationBox');
    Route::post("translations/removeKeyTranslation", 'TranslationsController@removeKeyTranslation');
    Route::post("translations/saveKeyTranslation", 'TranslationsController@saveKeyTranslation');
    Route::post("translations/reloadTranslation", 'TranslationsController@reloadTranslation');

    Route::resource('translations', 'TranslationsController');
});
