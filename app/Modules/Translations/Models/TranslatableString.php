<?php

namespace App\Modules\Translations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TranslatableString extends Model
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module',
        'group',
        'key',
    ];

    public function translationsAttribute() {
        $this->setAttribute("translations", $this->translations()->get());
    }
    public function translations() {
        return $this->hasMany('App\Modules\Translations\Models\TranslatedString','string_id');
    }
}