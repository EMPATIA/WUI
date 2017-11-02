<?php namespace App\Modules\Translations\Controllers;

use App\ComModules\Orchestrator;
use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Modules\Translations\Models\TranslatableString;
use App\Modules\Translations\Models\TranslatedString;
use App\One\One;
use Exception;
use File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Session;

class TranslationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {

        $modules = [];
        $title = trans('Translations::translation.title');
        $translationsModules = TranslatableString::select('module')->distinct()->orderBy('module')->get();
        foreach ($translationsModules as $translationsModule) {
            if($request->has('layout')){
                if ($translationsModule->module == "*") {
                    $translationsModule->module = trans('Translations::translation.structure');
                    $modules[] = $translationsModule->module;
                }

            }else{
                if ($translationsModule->module == "*") {
                    $translationsModule->module = trans('Translations::translation.structure');
                }
                $modules[] = $translationsModule->module;
            }
        }

        $restrition = $request->layout ?? null;

        return view("Translations::index", compact('title', 'modules', 'restrition'));
    }

    public function manageAllEmptyTranslations()
    {
        $title = trans('Translations::translation.manage_empty');
        $translationsModules = TranslatableString::select('module')->distinct()->orderBy('module')->get();
        foreach ($translationsModules as $translationsModule) {
            if ($translationsModule->module == "*") {
                $translationsModule->module = trans('Translations::translation.structure');
            }
            $modules[] = $translationsModule->module;
        }
        $translationsGroups = TranslatableString::select('group')->distinct()->orderBy('group')->get();
        $groups = [];
        foreach ($translationsGroups as $translationsGroups) {

            $groups[] = $translationsGroups->group;
        }
        return view("Translations::manageEmpty", compact('title', 'modules', 'groups'));
    }

    public function getAllEmptyTranslations(Request $request)
    {
        if ($request->input('module')) {
            if ($request->input('module') == trans('Translations::translation.structure')) {
                $module = '*';
            } else {
                $module = $request->input('module');
            }
        }
        if ($request->input('group')) {
            $group = $request->input('group');
        }


        if (isset($module) && isset($group)) {
            $translationsKeys = TranslatableString::whereModule($module)->whereGroup($group)->orderBy('module')->orderBy('group')->get();
        } elseif (isset($module)) {
            $translationsKeys = TranslatableString::whereModule($module)->orderBy('module')->orderBy('group')->get();
        } elseif (isset($group)) {
            $translationsKeys = TranslatableString::whereGroup($group)->orderBy('module')->orderBy('group')->get();
        } else {
            $translationsKeys = TranslatableString::orderBy('module')->orderBy('group')->get();
        }
        $languages = Orchestrator::getAllLanguages();
        foreach ($translationsKeys as $value => $key) {
            $asEmpty = false;
            if ($key->module == '*') {
                $key->module = trans('Translations::translation.structure');
            }
            foreach ($languages as $language) {

                $translation = TranslatedString::whereStringId($key->id)->whereLanguageId($language->id)->first();
                if ($translation) {
                    if (strlen($translation->translation) > 30) {
                        $key[$language->code] = substr($translation->translation, 0, 30) . ' <small>[...]</small>';
                    } else {
                        $key[$language->code] = $translation->translation;
                    }

                } else {
                    $key[$language->code] = '';
                    $asEmpty = true;
                }
            }
            if (!$asEmpty) {
                $translationsKeys->forget($value);
            }
        }
        return TranslationsController::buildEmptyKeysTable($translationsKeys);
    }

    public function getGroups(Request $request)
    {
        if ($request->input('module') == trans('Translations::translation.structure')) {
            $module = '*';
        } else {
            $module = $request->input('module');
        }

        if (!empty($request->group['term'])) {
            $group = $request->group['term'];
        }

        if(isset($request->restrition) and !empty($request->restrition)){
            $group = $request->restrition;
        }

        if (isset($group)) {
            $translationsGroups = TranslatableString::select('group')->distinct()->whereModule($module)->where('group', 'LIKE', "%$group%")->orderBy('group')->get();
        } else {
            $translationsGroups = TranslatableString::select('group')->distinct()->whereModule($module)->orderBy('group')->get();
        }

        $groups = [];
        foreach ($translationsGroups as $translationsGroups) {

            $groups[] = $translationsGroups->group;
        }

        return $groups;

    }

    public function getKeys(Request $request)
    {
        if ($request->input('module') == trans('Translations::translation.structure')) {
            $module = '*';
        } else {
            $module = $request->input('module');
        }
        if (count($request->input('states')) > 0) {
            $withStates = true;
            foreach ($request->input('states') as $state) {
                if ($state == 1) {
                    $getEmpty = true;
                }
                if ($state == 0) {
                    $getSaved = true;
                }
            }
        }
        $translationsKeys = TranslatableString::whereModule($module)->whereGroup($request->input('group'))->orderBy('key')->get();
        $languages = Orchestrator::getAllLanguages();
        foreach ($translationsKeys as $value => $key) {
            $asEmpty = false;
            foreach ($languages as $language) {

                $translation = TranslatedString::whereStringId($key->id)->whereLanguageId($language->id)->first();
                if ($translation) {
                    if (strlen($translation->translation) > 30) {
                        $key[$language->code] = substr($translation->translation, 0, 30) . ' <small>[...]</small>';
                    } else {
                        $key[$language->code] = $translation->translation;
                    }

                } else {
                    $key[$language->code] = '';
                    $asEmpty = true;
                }
            }

            if (isset($withStates)) {
                if (isset($getEmpty) && isset($getSaved)) {
                    continue;
                } elseif (isset($getEmpty)) {
                    if (!$asEmpty) {
                        $translationsKeys->forget($value);
                    }
                } else {
                    if ($asEmpty) {
                        $translationsKeys->forget($value);
                    }
                }
            }
        }
        return TranslationsController::buildKeysTable($translationsKeys, $request->input('module'), $request->input('group'));
    }

    /**
     * this function builds the translations keys
     * table according to a selected module
     * and a selected group
     * @param $keys
     * @param $module
     * @param $group
     * @return string
     */
    public static function buildKeysTable($keys, $module, $group)
    {
        $html = '';
        $languages = Orchestrator::getAllLanguages();
        if ($keys) {

            // Hide show controlls
            $html .= "<div class='margin-top-20 margin-bottom-20'><b class='hide_show_languages'>".trans('Translations::translation.hide_show_languages').":</b> ";
            foreach ($languages as $language) {
                $html .= "<label class=\"toggle-vis-label\"  title=\"$language->name\" ><input id=\"toggle-vis-$language->code\"  value=\"$language->code\" class=\"toggle-vis\" type=\"checkbox\" checked onclick='javascript:hideShowLanguages(this);' > ". $language->code . '</label>';
            }
            $html .= "</div>";

            $html .= '<table class="table translations-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<td class="text-center" colspan="' . (count($languages) + 1) . '">';
            $html .= $module . '-' . $group;
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . trans("Translations::translation.key") . '</td>';
            foreach ($languages as $language) {
                $html .= '<td class="translations-table-'.$language->code.'" title="'.$language->name.'">' . $language->code . '</td>';
            }
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            if (count($keys) > 0) {
                foreach ($keys as $key) {
                    $html .= '<tr>';
                    $html .= '<td>' . $key->key . '</td>';
                    foreach ($languages as $language) {
                        if ($key[$language->code] == '') {
                            $html .= '<td class="empty-translation cursor translations-table-'.$language->code.'" title="'.$language->name.'"><span class="float-xs-left" title="' . trans("Translations::translation.add") . '">' . trans("Translations::translation.empty") . '<input type="hidden" id="key_id" value="' . $key->id . '"><input type="hidden" id="language_id" value="' . $language->id . '"></span></td>';
                        } else {
                            $html .= '<td class="edit-translation cursor translations-table-'.$language->code.'" title="'.$language->name.'"><span class="float-xs-left" title="' . trans("Translations::translation.edit") . '">' . $key[$language->code] . '</i></span><input type="hidden" id="key_id" value="' . $key->id . '"><input type="hidden" id="language_id" value="' . $language->id . '"></span><span class="remove-translation cursor" title="' . trans("Translations::translation.delete") . '"><i class="fa fa-trash"></td>';
                        }

                    }
                    $html .= '</tr>';
                }
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }

        return $html;
    }

    public static function buildEmptyKeysTable($keys)
    {
        $html = '';
        $languages = Orchestrator::getAllLanguages();
        if ($keys) {

            // Hide show controlls
            $html .= "<div class='margin-top-20 margin-bottom-20'><b class='hide_show_languages'>".trans('Translations::translation.hide_show_languages').":</b> ";
            foreach ($languages as $language) {
                $html .= "<label class=\"toggle-vis-label\"  title=\"$language->name\" ><input id=\"toggle-vis-$language->code\"  value=\"$language->code\" class=\"toggle-vis\" type=\"checkbox\" checked onclick='javascript:hideShowLanguages(this);' > ". $language->code . '</label>';
            }
            $html .= "</div>";

            $html .= '<table class="table translations-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<td>' . trans("Translations::translation.key") . '</td>';
            foreach ($languages as $language) {
                $html .= '<td>' . $language->code . '</td>';
            }
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            if (count($keys) > 0) {
                foreach ($keys as $key) {
                    if ($key->module == '*') {
                        $key->module = trans('Translations::translation.structure');
                    }
                    $html .= '<tr>';
                    $html .= '<td>' . $key->module . ' > ' . $key->group . ' > ' . $key->key . '</td>';
                    foreach ($languages as $language) {
                        if ($key[$language->code] == '') {
                            $html .= '<td class="empty-translation cursor"><span class="float-xs-left" title="' . trans("Translations::translation.add") . '">' . trans("Translations::translation.empty") . '<input type="hidden" id="key_id" value="' . $key->id . '"><input type="hidden" id="language_id" value="' . $language->id . '"></span></td>';
                        } else {
                            $html .= '<td class="edit-translation cursor"><span class="float-xs-left" title="' . trans("Translations::translation.edit") . '">' . $key[$language->code] . '</i></span><input type="hidden" id="key_id" value="' . $key->id . '"><input type="hidden" id="language_id" value="' . $language->id . '"></span><span class="remove-translation cursor" title="' . trans("Translations::translation.delete") . '"><i class="fa fa-trash"></td>';
                        }

                    }
                    $html .= '</tr>';
                }
            }
            $html .= '</tbody>';
            $html .= '</table>';
        }

        return $html;
    }

    public static function reloadTranslationHtml($translation, $keyId, $languageId)
    {
        if ($translation == '') {
            $html = '<span class="float-xs-left" title="' . trans("Translations::translation.add") . '">' . trans("Translations::translation.empty") . '<input type="hidden" id="key_id" value="' . $keyId . '"><input type="hidden" id="language_id" value="' . $languageId . '"></span></td>';
        } else {
            $html = '<span class="float-xs-left" title="' . trans("Translations::translation.edit") . '">' . $translation . '</i></span><input type="hidden" id="key_id" value="' . $keyId . '"><input type="hidden" id="language_id" value="' . $languageId . '"></span><span class="remove-translation cursor" title="' . trans("Translations::translation.delete") . '"><i class="fa fa-trash"></i>';
        }

        return $html;
    }

    public static function buildAddKeyTranslationBox($keyId, $languageId)
    {
        $html = '';
        $html .= '<input type="text" class="form-control" id="translation" placeholder="' . trans("Translations::translation.enter_translation") . '">';
        $html .= '<input type="hidden" id="key_id" value="' . $keyId . '">';
        $html .= '<input type="hidden" id="language_id" value="' . $languageId . '">';
        $html .= '<i class="cancel-translation-new fa fa-times cursor" title="' . trans("Translations::translation.cancel") . '"></i><i class="save-translation fa fa-save cursor" title="' . trans("Translations::translation.save") . '"></i>';
        return $html;
    }

    public static function getEditTranslationBoxHtml($keyId, $languageId, $translation)
    {
        $html = '';
        $html .= '<input type="text" class="form-control" id="translation" value="' . e($translation) . '">';
        $html .= '<input type="hidden" id="key_id" value="' . $keyId . '">';
        $html .= '<input type="hidden" id="language_id" value="' . $languageId . '">';
        $html .= '<i class="cancel-translation fa fa-times cursor" title="' . trans("Translations::translation.cancel") . '"></i><i class="save-translation fa fa-save cursor" title="' . trans("Translations::translation.save") . '"></i>';
        return $html;
    }

    public function getAddTranslationBox(Request $request)
    {
        $keyId = $request->input('key_id');
        $languageId = $request->input('language_id');

        return TranslationsController::buildAddKeyTranslationBox($keyId, $languageId);
    }

    public function getEditTranslationBox(Request $request)
    {
        $keyId = $request->input('key_id');
        $languageId = $request->input('language_id');
        $translation = TranslatedString::whereStringId($keyId)->whereLanguageId($languageId)->first();
        if (!$translation) {
            $translation = '';
        } else {
            $translation = $translation->translation;
        }
        return TranslationsController::getEditTranslationBoxHtml($keyId, $languageId, $translation);
    }

    public function removeKeyTranslation(Request $request)
    {
        $keyId = $request->input('key_id');
        $languageId = $request->input('language_id');
        $translation = TranslatedString::whereStringId($keyId)->whereLanguageId($languageId)->first();
        $translation->delete();

        return $this->reloadTranslation($request);


    }

    public function searchKey(Request $request)
    {
        $keys = [];
        $translations = TranslatedString::select('string_id')->distinct()->where('translation', 'LIKE', "%$request->term%")->get();

        $languages = Orchestrator::getAllLanguages();
        if (count($translations) > 0) {
            foreach ($translations as $translation) {
                $keys[] = TranslatableString::find($translation->string_id);
            }
        }
        if (count($keys) > 0) {
            foreach ($keys as $key) {
                foreach ($languages as $language) {
                    $translation = TranslatedString::whereStringId($key->id)->whereLanguageId($language->id)->first();
                    if ($translation) {
                        if (strlen($translation->translation) > 30) {
                            $key[$language->code] = substr($translation->translation, 0, 30) . ' <small>[...]</small>';
                        } else {
                            $key[$language->code] = $translation->translation;
                        }

                    } else {
                        $key[$language->code] = '';
                        $asEmpty = true;
                    }
                }
            }
        }


        return TranslationsController::buildEmptyKeysTable($keys);
    }

    public function reloadTranslation(Request $request)
    {
        $keyId = $request->input('key_id');
        $languageId = $request->input('language_id');
        $translatedString = TranslatedString::whereStringId($keyId)->whereLanguageId($languageId)->first();
        if (!$translatedString) {
            $translation = '';
        } else {
            if (strlen($translatedString->translation) > 30) {
                $translation = substr($translatedString->translation, 0, 30) . ' <small>[...]</small>';
            } else {
                $translation = $translatedString->translation;
            }
        }
        return TranslationsController::reloadTranslationHtml($translation, $keyId, $languageId);
    }

    public function saveKeyTranslation(Request $request)
    {
        $keyId = $request->input('key_id');
        $languageId = $request->input('language_id');
        $newTranslation = $request->input('translation');

        if ($request->input('translation') != '') {
            $translation = TranslatedString::whereStringId($keyId)->whereLanguageId($languageId)->first();
            if ($translation) {
                $translation->translation = $newTranslation;
                $translation->save();
            } else {
                TranslatedString::create(
                    [
                        'translation' => $newTranslation,
                        'string_id'   => $keyId,
                        'language_id' => $languageId
                    ]
                );
            }
        }

        return $this->reloadTranslation($request);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


    public function getAllStrings()
    {
        try {
            $count = array(
                0 => 0, /* Translatable Strings Count */
                1 => 0, /* Translated Strings Count */
            );

            $translatableStrings = array();
            $functions = array('trans', 'trans_choice', 'Lang::get', 'Lang::choice', 'Lang::trans', 'Lang::transChoice', '@lang', '@choice');
            $pattern =
                "[^\w|]" .                                             // Must not have an alphanum or _ or > before real method
                "(" . implode('|', $functions) . ")" .                  // Must start with one of the functions
                "\(" .                                                  // Match opening parenthese
                "[\'\"]" .                                              // Match " or '
                "(([a-zA-Z0-9_-]+::)?[a-zA-Z0-9_-]+([.][^\1\$)]+)+)" .  // Search for strings
                "[\'\"]" .                                              // Closing quote
                "[\),]";                                                // Close parentheses or new parameter

            $finder = new Finder();
            $finder->in(base_path())->exclude('storage')->name('*.php')->name('*.twig')->files();

            $files = [];
            foreach ($finder as $file) {
                $files[] = $file;
                if (preg_match_all("/$pattern/siU", $file->getContents(), $matches)) {
                    foreach ($matches[2] as $key) {
                        $translatableStrings[] = $key;
                    }
                }
            }
            // Remove duplicates
            $translatableStrings = array_unique($translatableStrings);

            $languages = Orchestrator::getAllLanguages();
            foreach ($translatableStrings as $key => $value) {
                /* If not Module, Add * to Start */
                if (!str_contains($value, "::"))
                    $value = "*::" . $value;

                $temp = preg_split("/::|[.]/", $value);
                $current = array(
                    "module" => $temp[0],
                    "group"  => $temp[1],
                    "key"    => "",
                );

                /* This is where the magic happens when we're talking about of arrays inside arrays */
                for ($i = 2; $i < count($temp); $i++) {
                    $current["key"] .= $temp[$i] . ".";
                }
                $current["key"] = rtrim($current["key"], '.');

                /* This is needed because, for MySQL "teste"="TESTE" */
                $existing = TranslatableString::whereRaw("BINARY `module`= ?", $current["module"])->whereRaw("BINARY `group`= ?", $current["group"])->whereRaw("BINARY `key`= ?", $current["key"])->get();
                if (count($existing) == 1)
                    $current["id"] = $existing->first()->id;
                else
                    $current["id"] = TranslatableString::create($current)->id;

                $count[0]++;
                foreach ($languages as $language) {
                    $langKey = $current["module"] . "::" . $current["group"] . "." . $current["key"];

                    if (\Lang::hasForLocale($langKey, $language->code)) {
                        $count[1]++;
                        $existingTranslation = TranslatedString::whereStringId($current["id"])->whereLanguageId($language->id)->first();
                        $newTranslationString = \Lang::get($langKey, array(), $language->code);

                        if (!is_array($newTranslationString)) {
                            if (count($existingTranslation) == 0 && is_null($existingTranslation)) {
                                TranslatedString::create(array(
                                    "translation" => $newTranslationString,
                                    "string_id" => $current["id"],
                                    "language_id" => $language->id,
                                ));
                            } else if (count($existingTranslation) == 1 && $existingTranslation->translation != $newTranslationString) {
                                $existingTranslation->translation = $newTranslationString;
                                $existingTranslation->save();
                            }
                        }
                    }
                }

            }

            return $response = trans("Translations::translation.translatable") . ':' . $count[0] . ' ' . trans("Translations::translation.translated") . ':' . $count[1];
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => 1], 500);
        } catch (Exception $e) {
            return response()->json(["error" => 2], 500);
        }
    }


    public function saveAllStrings()
    {
        // This is the good guy who makes the magic about the Arrays in translations Keys /
        function getArrayedTranslation($originalKey, $value, $iteration = 0)
        {
            if (substr_count($originalKey, ".") > 0) {
                $keys = explode(".", $originalKey);
                $value = getArrayedTranslation(implode(".", array_slice($keys, 1)), $value, $iteration + 1);

                if ($iteration == 0)
                    return ["key" => $keys[0], "value" => $value];
                else
                    return [$keys[0] => $value];
            } else {
                if ($iteration == 0)
                    return ["key" => $originalKey, "value" => $value];
                else
                    return [$originalKey => $value];
            }
        }

        try {
            $count = 0; /* Saved Translations count*/
            $anyFileFailed = false;

            $toSave = [];
            /* Anatomy of ToSave
             * > Module::Group / Namespace::File
             * >> LanguageCode
             * >>> Translation Key, which can contain N subkeys
             */
            $languages = Orchestrator::getAllLanguages();

            foreach ($languages as $language){
                $availableLanguages[$language->id] = $language->code;
            }

            $translatableStrings = TranslatableString::all();
            foreach ($translatableStrings as $translatableString) {
                foreach ($translatableString->translations()->get() as $translation) {
                    $new = getArrayedTranslation($translatableString->key, $translation->translation);


                    $toSave[$translatableString->module . "::" . $translatableString->group][$availableLanguages[$translation->language_id]][$new["key"]] = $new["value"];
                    $count++;
                }
            }

            // Save to Files /
            foreach ($toSave as $path => $language) {
                foreach ($language as $languageCode => $translations) {
                    $temp = preg_split("/::|[.]/", $path);
                    if (!str_contains($path, "*"))
                        $realPath = app_path() . "/Modules/" . $temp[0] . "/Translations/" . $languageCode;
                    else
                        $realPath = app()->langPath() . "/" . $languageCode;

                    if (!File::exists($realPath))
                        File::makeDirectory($realPath);
                    $output = "<?php return " . preg_replace('/\r|\n/', "",var_export($translations, true)) . ";";
                    if (File::put($realPath  . "/" . $temp[1] . ".php", $output) === false)
                        $anyFileFailed = true;
                }
            }

            if ($anyFileFailed) {
                $response = trans("Translations::translation.file_creation_failed");
                Session::flash('error', $response);

            } else {
                $response = trans("Translations::translation.saved") . ': ' . $count;
                Session::flash('message', $response);
            }
            return action('\App\Modules\Translations\Controllers\TranslationsController@index');
        } catch (ModelNotFoundException $e) {
            return $response = $e->getMessage();
        } catch (Exception $e) {
            return $response = $e->getMessage();
        }
    }
}
