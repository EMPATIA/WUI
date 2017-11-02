<?php

namespace App\Modules\Translations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TranslatedString extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'translation',
        'string_id',
        'language_id',
    ];

    public function translatableStringAttribute() {
        $this->setAttribute("translatableString", $this->translatableString()->get());
    }
    public function translatableString() {
        return $this->belongsTo('App\Modules\Translations\Models\TranslatableString');
    }

    /*public function languageAttribute() {
        $this->setAttribute("language", $this->language()->first());
    }
    public function language(){
        return $this->belongsTo('App\Modules\Cms\Models\Language','language_id');
    }*/
}