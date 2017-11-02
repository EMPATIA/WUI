<?php

namespace App\ComModules;

use App\One\One;
use Exception;



class CM {
    public static function getMenu($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'menu',
            'api_attribute' => $id
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCM.errorRetrievingMenu"));

        }

        return $response->json();
    }

    public static function listContent($pageKeys){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'listContent',
            'params'    => ["page_keys" => $pageKeys ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCM.errorRetrievingContents"));
        }

        return $response->json()->data;
    }

    /**
     * Returns a list of all Content Type Types
     *
     * @return mixed
     * @throws Exception
     */
    public static function listAllContentTypeTypes(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'contentTypeTypes',
            'method' => 'list'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCM.errorRetrievingNewsTypes"));
        }

        return $response->json()->data;
    }
    /**
     * Returns a list of all Content Type Types by Entity
     *
     * @return mixed
     * @throws Exception
     */
    public static function listByEntityContentTypeTypes(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'contentTypeTypes',
            'method' => 'listByEntity'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCM.errorRetrievingContentTypes"));
        }

        return $response->json()->data;
    }

    /**
     * Returns a list of Content Type Types by Content Type
     *
     * @param $contentType
     * @return mixed
     * @throws Exception
     */
    public static function listContentTypeTypes($contentType){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'contentTypeTypes',
            'method' => 'listByType',
            'attribute' => $contentType
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCM.errorRetrievingContentTypeTypes"));
        }

        return $response->json()->data;
    }

    public static function getMenuTypeList()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'menutype',
            'method'    => 'list',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failedToGetMenuTypeList"));
        }
        return $response->json()->data;
    }


    /**
     * Returns a Content Type Type
     *
     * @param $contentTypeTypeKey
     * @return mixed
     * @throws Exception
     */
    public static function getContentTypeTypeByKey($contentTypeTypeKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'contentTypeTypes',
            'api_attribute' => $contentTypeTypeKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_type_type_by_key"));
        }
        return $response->json();

    }

    /**
     * Returns a Content Type Type with translations
     *
     * @param $contentTypeTypeKey
     * @return mixed
     * @throws Exception
     */
    public static function getContentTypeTypeWithTranslations($contentTypeTypeKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'contentTypeTypes',
            'api_attribute' => $contentTypeTypeKey,
            'method'    => 'edit',
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_type_type_with_translations"));
        }
        return $response->json();

    }

    /**
     * Deletes a content type type
     *
     * @param $contentTypeTypeKey
     * @return mixed
     * @throws Exception
     */
    public static function deleteContentTypeType($contentTypeTypeKey){

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'contentTypeTypes',
            'api_attribute' => $contentTypeTypeKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_delete_content_type_type"));
        }
        return $response;

    }

    /**
     *
     * Updates a content type type
     *
     * @param $request
     * @param $translations
     * @param $contentTypeTypeKey
     * @return mixed
     * @throws Exception
     */
    public static function updateContentTypeType($request, $translations, $contentTypeTypeKey){

        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'contentTypeTypes',
            'attribute' => $contentTypeTypeKey,
            'params' => [
                'content_type_id' => $request->content_types,
                'text_color' => $request->text_color ?? null,
                'code' => $request->code,
                'color' => empty($request->color) ? '' : $request->color,
                'file' => empty($request->file) ? '' : $request->file,
                'translations' => $translations,
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_content_type_type"));
        }
        return $response->json();

    }


    /**
     * Sets a new content type type
     *
     * @param $request
     * @param $translations
     * @return mixed
     * @throws Exception
     */
    public static function setContentTypeType($request, $translations) {

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'contentTypeTypes',
            'params' => [
                'content_type_id' => $request->content_types,
                'code' => $request->code,
                'color' => empty($request->color) ? '' : $request->color,
                'text_color' => empty($request->text_color) ? '' : $request->text_color,
                'file' => empty($request->file) ? '' : $request->file,
                'translations' => $translations,
            ]

        ]);

        if ($response->statusCode() != 201) {
            throw new Exception(trans("comModulesCM.failed_to_set_content_type_type"));
        }
        return $response->json();

    }

    /**
     * Returns a List with all content Types
     *
     * @return mixed
     * @throws Exception
     */
    public static function getAllContentTypes()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'contentType'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_all_content_types"));
        }
        return $response->json();
    }

    public static function getContentsByKeyWithType($dataNewsKeys, $contentTypeType)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'content',
            'method' => 'contentsByKeyWithType',
            'params' => [
                'content_keys' => $dataNewsKeys,
                'content_type_type' => $contentTypeType
            ]
        ]);
        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesCM.failed_to_get_contents_by_key_with_type"));
        }

        return $response->json()->data;

    }


    /* Section Types */
    public static function getSectionTypes()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'sectionType',
            'api_attribute' => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_Get_section_types"));
        }
        return $response->json();
    }
    public static function getSectionType($sectionTypeKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'sectionType',
            'api_attribute' => $sectionTypeKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_section_type"));
        }
        return $response->json();
    }
    public static function createSectionType($sectionType){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'sectionType',
            'params'    => $sectionType
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesCM.error_creating_section_type"));
        }
        return $response->json();
    }
    public static function updateSectionType($key, $updateData){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'sectionType',
            'api_attribute' => $key,
            'params'    => $updateData
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.error_updating_section_type"));
        }
        return $response->json();
    }
    public static function deleteSectionType($key){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'sectionType',
            "api_attribute" => $key,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.error_deleting_section_type"));
        }
        return $response->json();
    }

    /* Section Type Parameters */
    public static function getSectionTypeParameters()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'sectionTypeParameter',
            'api_attribute' => 'list'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_Get_section_type_parameters"));
        }
        return $response->json();
    }
    public static function getSectionTypeParameter($sectionTypeKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'sectionTypeParameter',
            'api_attribute' => $sectionTypeKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_section_type"));
        }
        return $response->json();
    }
    public static function createSectionTypeParameter($sectionType){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'sectionTypeParameter',
            'params'    => $sectionType
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesCM.error_creating_section_type"));
        }
        return $response->json();
    }
    public static function updateSectionTypeParameter($key, $sectionTypeParameter){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'sectionTypeParameter',
            "api_attribute" => $key,
            'params'    => $sectionTypeParameter
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.error_updating_section_type"));
        }
        return $response->json();
    }
    public static function deleteSectionTypeParameter($key){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'sectionTypeParameter',
            "api_attribute" => $key,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.error_deleting_section_type"));
        }
        return $response->json();
    }


    /* ------------------------------------- Content Management ------------------------------------- */
    public static function getNewContents($contentType = null, $siteKey = null)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'newContent',
            'api_attribute' => 'list',
            'params' => [
                "content_type_code" => $contentType,
                "site_key"     => $siteKey
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_contents"));
        }
        return $response->json()->data;
    }
    public static function getNewContent($contentKey,$version=null)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'newContent',
            'api_attribute' => $contentKey,
            'method'    => $version
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content"));
        }
        return $response->json();
    }
    public static function createNewContent($content){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'newContent',
            'params'    => $content
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesCM.error_creating_content"));
        }
        return $response->json();
    }
    public static function updateNewContent($contentKey, $content){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'newContent',
            'api_attribute' => $contentKey,
            'params'    => $content
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.error_updating_content"));
        }
        return $response->json();
    }
    public static function toggleNewContentActiveStatus($contentKey, $version, $newStatus){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'newContent',
            'api_attribute' => $contentKey,
            'method'    => $version,
            'attribute' => 'status',
            'params'    => [
                "newStatus" => $newStatus
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.error_updating_content_status"));
        }
        return $response->json();
    }
    public static function deleteNewContent($key){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'newContent',
            "api_attribute" => $key,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.error_deleting_section_type"));
        }
        return $response->json();
    }

    public static function getNewContentListForPublic($contentType, $page = 0, $contentsPerPage = 6) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'publicNewContent',
            'api_attribute' => $contentType,
            'params' => [
                'page' => $page,
                'contentsPerPage' => $contentsPerPage
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_list_for_public"));
        }
        return $response->json();
    }
    public static function getNewContentForPublic($contentType, $contentKey) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'publicNewContent',
            'api_attribute' => $contentType,
            'method'    => $contentKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_for_public"));
        }
        return $response->json();
    }
    public static function getNewContentForPreview($contentKey, $contentVersion) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'publicNewContentPreview',
            'api_attribute' => $contentKey,
            'method'    => $contentVersion

        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_for_public"));
        }
        return $response->json();
    }
    public static function getNewContentByCode($contentCode) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'publicNewContentCode',
            'params' => [
                "codes" => $contentCode
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_by_code"));
        }
        return $response->json();
    }
    public static function getLastOfType($contentType, $count) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'newContent',
            'api_attribute' => 'getLastOf',
            'method' => $contentType,
            'attribute'    => $count
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_last_of_type"));
        }
        return $response->json()->data;
    }

    /* ------------------------------------- Content (Pages/Events/News) ------------------------------------- */
    /**
     *
     *
     * @param $contentKey
     * @param null $showVersion
     * @return mixed
     * @throws Exception
     */
    public static function getContent($contentKey, $showVersion = null) {
            $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'edit',
            'api_attribute' => $contentKey,
            'params' => [
                'version' => $showVersion
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_content"));
        }
        return $response->json();
    }

    /* ------------------------------------- Dynamic BackOffice Menu Administration ------------------------------------- */
    public static function getBEMenuElementParameters($request) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'beMenuElementParameters',
            'api_attribute' => 'list',
            'params'    => [
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_be_menu_element_parameters"));
        }
        return $response->json();
    }
    public static function createBEMenuElementParameters($configuration){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'beMenuElementConfigurations',
            'params'    => $configuration
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesCM.failed_to_create_be_menu_element_parameters"));
        }
        return $response->json();
    }
    public static function getBEMenuElementParameter($key) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'beMenuElementParameters',
            'api_attribute' => $key
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_be_menu_element_parameter"));
        }
        return $response->json();
    }
    public static function updateBEMenuElementParameters($key, $configuration){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'beMenuElementParameters',
            'api_attribute' => $key,
            'params'    => $configuration
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.failed_to_update_be_menu_element_parameters"));
        }
        return $response->json();
    }
    public static function deleteBEMenuElementParameters($key){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'beMenuElementParameters',
            "api_attribute" => $key,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.failed_to_delete_be_menu_element_parameters"));

        }
        return $response->json();
    }



    public static function getBEMenuElements($request) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'beMenuElements',
            'api_attribute' => 'list',
            'params'    => [
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_be_menu_elements"));
        }
        return $response->json();
    }
    public static function createBEMenuElements($element){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'beMenuElements',
            'params'    => $element
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesCM.failed_to_create_be_menu_elements"));
        }
        return $response->json();
    }
    public static function getBEMenuElement($key) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'beMenuElements',
            'api_attribute' => $key
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_be_menu_element"));
        }
        return $response->json();
    }
    public static function updateBEMenuElements($key, $element){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'beMenuElements',
            'api_attribute' => $key,
            'params'    => $element
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.failed_to_update_be_menu_elements"));
        }
        return $response->json();
    }
    public static function deleteBEMenuElements($key){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'beMenuElements',
            "api_attribute" => $key,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.failed_to_delete_be_menu_elements"));
        }
        return $response->json();
    }

    public static function getBEMenuActions($request) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'actions',
            'api_attribute' => 'list',
            'params'    => [
                'tableData' => One::tableData($request),
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_be_menu_actions"));
        }
        return $response->json();
    }
    public static function createBEMenuActions($element){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'actions',
            'params'    => $element
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesCM.failed_to_create_be_menu_actions"));
        }
        return $response->json();
    }
    public static function getBEMenuAction($id) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'actions',
            'api_attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_be_menu_action"));
        }
        return $response->json();
    }
    public static function updateBEMenuActions($id, $action){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'actions',
            'api_attribute' => $id,
            'params'    => $action
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.failed_to_update_be_menu_actions"));
        }
        return $response->json();
    }
    public static function deleteBEMenuActions($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'actions',
            "api_attribute" => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.failed_to_delete_be_menu_actions"));
        }
        return $response->json();
    }

    /* ------------------------------------- Dynamic BackOffice Menu ------------------------------------- */

    public static function importDefaultMenu($userKey = null) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'beMenu',
            'api_attribute' => 'import',
            'params' => [
                'userKey' => $userKey
            ]
        ]);
        
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_import_default_menu"));
        }
        return $response->json();
    }
    public static function getEntityBEMenu($userKey = null) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'beMenu',
            'api_attribute' => 'list',
            'params' => [
                'userKey' => $userKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_be_entity_menu_elements"));
        }
        return $response->json();
    }
    public static function createEntityBEMenuElement($element, $userKey = null){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'beMenu',
            'params'    => $element
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesCM.failed_to_create_be_entity_menu_elements"));
        }
        return $response->json();
    }
    public static function getEntityBEMenuElement($key, $userKey = null) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'beMenu',
            'api_attribute' => $key,
            'params' => [
                'userKey' => $userKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_be_entity_menu_element"));
        }
        return $response->json();
    }
    public static function updateEntityBEMenuElements($key, $element){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'beMenu',
            'api_attribute' => $key,
            'params'    => $element
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.failed_to_update_be_entity_menu_elements"));
        }
        return $response->json();
    }
    public static function deleteEntityBEMenuElements($key, $userKey = null){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'beMenu',
            "api_attribute" => $key,
            'params' => [
                'userKey' => $userKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.failed_to_delete_be_entity_menu_elements"));
        }
        return $response->json();
    }
    public static function getEntityBEMenuRenderData() {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'beMenu',
            'api_attribute' => 'renderData'
        ]);

        if($response->statusCode() != 200){
            return false;
        }
        return $response->json();
    }
    public static function reorderEntityBEMenu($source, $destination, $rootOrdering, $ordering, $userKey = null) {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'beMenu',
            'method' => 'reorder',
            'attribute' => $source,
            'params'    => [
                'parent_key' => $destination, // actualiza o pai
                'positions' => (empty($destination) ? $rootOrdering : $ordering),
                'userKey' => $userKey
            ],
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_reorder_be_entity_menu_elements"));
        }
        return $response->json();
    }

    public static function listMenus($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'menu',
            'method'    => 'list',
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_list_menus"));
        }

        return $response->json()->data;
    }

    public static function getContentVersions($contentKey){
        $response = ONE::get([
            'component'         => 'empatia',
            'api'               => 'content',
            'api_attribute'     => $contentKey,
            'method'            => 'showVersions'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_versions"));
        }

        return $response->json();
    }

    public static function getLinkableContentType(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'contentType',
            'method'    => 'linkable'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_linkable_content_type"));
        }

        return $response->json();
    }

    public static function setContent($request, $contentTranslation){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'content',
            'params'    => [
                'type'       => $request->type,
                'start_date'    => (isset($request->start_date) ? $request->start_date : null),
                'end_date'      => (isset($request->end_date) ? $request->end_date : null),
                'publish_date'  => (isset($request->publish_date) ? $request->publish_date : null),
                'translations'  => $contentTranslation,
                'content_type_type' => (isset($request->content_type_type) ? $request->content_type_type : null),
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCM.failed_to_set_content"));
        }

        return $response->json();
    }

    public static function updateContent($contentKey, $request, $contentTranslation){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'content',
            'params'    => [
                'type'       => $request->type,
                'content_type_type'  => (isset($request->content_type_type) ? $request->content_type_type : null),
                'start_date'    => (isset($request->start_date) ? $request->start_date : null),
                'end_date'      => (isset($request->end_date) ? $request->end_date : null),
                'publish_date'  => (isset($request->publish_date) ? $request->publish_date : null),
                'translations'  => $contentTranslation
            ],
            'attribute' => $contentKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_content"));
        }

        return $response->json();
    }

    public static function activateVersion($contentKey, $newVersion){
        $response = ONE::put([
            'component'         => 'empatia',
            'api'               => 'content',
            'api_attribute'     => $contentKey,
            'method'            => 'version/'.$newVersion,
            'attribute'         => 'enable',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_activate_version"));
        }

        return $response->json();
    }

    public static function publishContent($contentKey){
        $response = ONE::put([
            'component' => 'empatia',
            'api'           => 'content',
            'api_attribute' => $contentKey,
            'method'        => 'publish',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_publish_content"));
        }

        return $response->json();
    }

    public static function unpublishContent($contentKey){
        $response = ONE::put([
            'component' => 'empatia',
            'api'           => 'content',
            'api_attribute' => $contentKey,
            'method'        => 'unpublish',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_unpublish_content"));
        }

        return $response->json();
    }

    public static function getContentByKey($contentKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'content',
            'attribute' => $contentKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content"));
        }

        return $response->json();
    }

    public static function deleteContent($contentKey){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'content',
            'attribute' => $contentKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_delete_content"));
        }

        return $response->json();
    }

    public static function getContentFiles($contentKey, $typeId){
        $response = ONE::get([
            'component' => 'empatia',
            'api'           => 'content',
            'api_attribute' => $contentKey,
            'method'        => 'files',
            'attribute' => $typeId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_files"));
        }

        return $response->json()->data;
    }

    public static function deleteContentFiles($request, $temp_files){
        $response = ONE::delete([
            'component'     => 'empatia',
            'api'           => 'content',
            'api_attribute' => $request->content_key,
            'method'        => 'file',
            'attribute'     => $temp_files[0]->file_id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_delete_content_files"));
        }

        return $response->json()->data;
    }

    public static function updateContentFiles($request){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'content',
            'api_attribute' =>  $request->content_key,
            'method'        => 'file',
            'params'    => [
                'file_id'       => $request->file_id,
                'name'          => $request->name,
                'description'   => $request->name,
                'position'      => 0,
                'type_id'       => $request->type_id
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCM.failed_to_update_content_files"));
        }

        return $response->json()->data;
    }


    public static function updateContentFile($contentKey, $params, $id){
        $response = ONE::put([
            'component'     => 'empatia',
            'api'           => 'content',
            'api_attribute' => $contentKey,
            'method'        => 'file',
            'attribute'     => $id,
            'params'        => $params
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_content_file"));
        }

        return $response->json();
    }

    public static function orderFile($contentKey, $typeId, $movement, $fileId){
        $response = ONE::put([
            'component'     => 'empatia',
            'api'           => 'content',
            'api_attribute' => $contentKey,
            'method'        => 'orderfile',
            'attribute'     => $fileId,
            'params'        => [
                'type_id'   => $typeId,
                'movement'  => $movement
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_order_file"));
        }

        return $response->json();
    }

    public static function deleteContentFile($contentKey, $fileId){
        $response = ONE::delete([
            'component'     => 'empatia',
            'api'           => 'content',
            'api_attribute' => $contentKey,
            'method'        => 'file',
            'attribute'     => $fileId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_delete_content_file"));
        }

        return $response->json();
    }

    public static function getMenuContent($id){
        $response = ONE::get([
            'component'     => 'cm',
            'api'           => 'menu',
            'method'        => 'content',
            'attribute'     =>  $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_menu_content"));
        }

        return $response->json();
    }

    public static function getSonsList($id){
        $response = ONE::get([
            'component' => 'cm',
            'api'       => 'menu',
            'method'    => 'sonsList',
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_menu_sons_list"));
        }

        return $response->json();
    }

    public static function getContentVersion($contentKey, $version){
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'content',
            'api_attribute' => $contentKey,
            'method'        => 'version',
            'attribute'     => $version
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_version"));
        }
        return $response->json()->data;
    }

    public static function getContentNewsList(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'newslist',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_new_list"));
        }
        return $response->json()->data;
    }

    public static function getContentNewsIds(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'newsids',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_new_ids"));
        }
        return $response->json()->data;
    }

    public static function getContentPresentNews(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'presentnews'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_present_news"));
        }
        return $response->json()->data;
    }

    public static function getContentLastNews(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'lastnews'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_last_news"));
        }
        return $response->json()->data;
    }

    public static function getContentEventIds(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'eventsids'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_events_ids"));
        }
        return $response->json()->data;
    }

    public static function getContentLastEvents(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'lastevents'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_content_last_events"));
        }
        return $response->json()->data;
    }

    public static function listContents($type){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'list',
            'attribute' => $type,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_list_contents"));
        }
        return $response->json();
    }

    public static function setMail($params){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'mail',
            'params'    => $params
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCM.failed_to_set_mail"));
        }
        return $response->json();
    }

    public static function getMail($mail_key){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'mail',
            'attribute' => $mail_key
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_mail"));
        }
        return $response->json();
    }

    public static function updateMail($params,$mail_key){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'mail',
            'attribute' => $mail_key,
            'params'    => $params
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_mail"));
        }
        return $response->json();
    }


    public static function update($access_id, $parent_id, $page_id, $type_id, $value, $private, $menuTranslation , $position, $menuKey ){

        $response = ONE::put([
            'component' => 'cm',
            'api'       => 'menu',
            'params'    => [
                'access_id' => $access_id,
                'parent_id' => $parent_id == "NONE" ? 0 : $parent_id,
                'page_id'   =>$page_id == "" ? 0 : $page_id,
                'type_id' => $type_id,
                'type' => ($private == 1 )? "private": "public",
                'value' => $value,
                'position'  => $position,
                'translations' => $menuTranslation
            ],
            'attribute' => $menuKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_mail"));
        }
        return $response->json();
    }

    public static function deleteMail($mail_key){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'mail',
            'attribute' => $mail_key,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_delete_mail"));
        }
        return $response->json();
    }

    public static function listMail(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'mail',
            'method'    => 'list',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_list_mail"));
        }
        return $response->json();
    }

    public static function setMenu($accessId, $parentId, $typeId, $value, $private, $translations){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'menu',
            'params'    => [
                'access_id' => $accessId,
                'parent_id' => $parentId == "NONE" ? 0 : $parentId,
                'type_id' => $typeId,
                'value' => $value,
                'type' => ($private == 1 )? "private": "public",
                'translations'=>$translations
            ],
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCM.failed_to_set_menu"));
        }
        return $response->json();
    }

    public static function editMenu($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'menu',
            'method'    => 'edit',
            'api_attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_edit_menu"));
        }
        return $response->json();
    }

    public static function getMenuParent($parentKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'menu',
            'attribute' => $parentKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_menu_parent"));
        }
        return $response->json();
    }

    public static function getMenuType($typeId){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'menutype',
            'attribute' => $typeId
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_menu_type"));
        }
        return $response->json();
    }

    public static function updateMenu($accessId, $parentId, $pageId, $typeId, $value, $private, $translations, $position, $menuKey){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'menu',
            'attribute' => $menuKey,
            'params'    => [
                'access_id' => $accessId,
                'parent_id' => $parentId == "NONE" ? 0 : $parentId,
                'page_id'   => $pageId == "" ? 0 : $pageId,
                'type_id' => $typeId,
                'type' => ($private == 1 )? "private": "public",
                'value' => $value,
                'position'  => $position,
                'translations' => $translations
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_menu"));
        }
        return $response->json();
    }

    public static function updateMenuReorder($destination, $rootOrdering, $ordering, $source){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'menu',
            'method'    => 'reorder',
            'attribute' => $source,
            'params'    => [
                'parent_key' => ($destination == "" ? "" : $destination), // actualiza o pai
                'positions' => ($destination == "" ? $rootOrdering : $ordering),
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_menu_reorder"));
        }
        return $response->json();
    }

    public static function deleteMenu($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'menu',
            'attribute' => $id,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_delete_menu"));
        }
        return $response->json();
    }

    public static function setPage($entityId){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'page',
            'params'    => [
                'entity_id' => $entityId
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCM.failed_to_delete_mail"));
        }
        return $response->json();
    }

    public static function setPageContent($pageId, $langId, $version, $enable, $title, $summary, $content){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'pagecontent',
            'params'    => [
                'page_id'     => $pageId,
                'language_id' => $langId,
                'version'     => $version,
                'enabled'     => $enable,
                'title'       => $title,
                'summary'     => $summary,
                'content'     => $content
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCM.failed_to_set_page_content"));
        }
        return $response->json();
    }

    public static function getPage($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'page',
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_page"));
        }
        return $response->json();
    }

    public static function listPageContent($id){
        $response = ONE::get([
            'component' => 'cm',
            'api'       => 'pagecontent/list',
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_list_page_content"));
        }
        return $response->json();
    }

    public static function getPageContent($id){
        $response = ONE::get([
            'component' => 'cm',
            'api'       => 'pagecontent/page/',
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_page_content"));
        }
        return $response->json();
    }

    public static function getPageContentWithVersion($id, $version){
        $response = ONE::get([
            'component' => 'cm',
            'api'       => 'pagecontent/page/',
            'attribute' => [$id, $version]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_page_content_with_version"));
        }
        return $response->json();
    }

    public static function enablePageContent($enable){
        $response = ONE::put([
            'component' => 'cm',
            'api'       => 'pagecontent/page/',
            'params'    => [
                'enabled' => $enable
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_enable_page_content"));
        }
        return $response->json();
    }

    public static function updatePage($entityId, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'page',
            'params'    => [
                'entity_id' => $entityId
            ],
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_page"));
        }
        return $response->json();
    }

    public static function updatePageContent($pageId, $langId, $version, $enable, $title, $summary, $content){
        $response = ONE::post([
            'component' => 'cm',
            'api'       => 'pagecontent/update',
            'params'    => [
                'page_id'     => $pageId,
                'language_id' => $langId,
                'version'     => $version,
                'enabled'      => $enable,
                'title'       => $title,
                'summary'     => $summary,
                'content'     => $content
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCM.failed_to_update_page_content"));
        }
        return $response->json();
    }

    public static function deletePage($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'page',
            'attribute' => $id,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_delete_page"));
        }
        return $response->json();
    }

    public static function listPages($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'page',
            'attribute' => $id,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_list_pages"));
        }
        return $response->json();
    }

    public static function getVariousContents($dataNews){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'content',
            'method' => 'contentsByKey',
            'params' => [
                'content_keys' => $dataNews
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_list_pages"));
        }
        return $response->json()->data;
    }

    public static function getEventsList(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'content',
            'method'    => 'eventslist'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_events_list"));
        }
        return $response->json()->data;
    }

    public static function setText($params){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'text',
            'params'    => $params
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesCM.failed_to_set_text"));
        }
        return $response->json();
    }

    public static function getText($textKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'text',
            'attribute' => $textKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_get_text"));
        }
        return $response->json();
    }

    public static function updateText($textKey, $params){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'text',
            'params'    => $params,
            'attribute'     => $textKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_update_text"));
        }
        return $response->json();
    }

    public static function deleteText($textKey){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'text',
            'attribute' => $textKey,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_delete_text"));
        }
        return $response->json();
    }

    public static function listText(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'text',
            'method'    => 'list',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesCM.failed_to_delete_text"));
        }
        return $response->json();
    }

    public static function listByAccessId($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'menu',
            'method'    => 'listByAccessId',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesCM.failed_to_list_by_access_id"));
        }

        return $response->json();
    }
}