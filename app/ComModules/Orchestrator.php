<?php

namespace App\ComModules;

use App\Http\Requests\ParameterTypesRequest;
use App\Http\Requests\SiteConfGroupRequest;
use App\Http\Requests\SiteConfRequest;
use App\Http\Requests\SiteSiteConfigRequest;
use App\One\One;
use Illuminate\Http\Request;
use Exception;

class Orchestrator {

    public static function getPageListByType($type, $value = null, $getAll = null){
        // Ask orchestrator for Page List from an entity
        if($value) {
            $response = ONE::get([
                'component' => 'empatia',
                'api' => 'page',
                'method' => 'listByType',
                'attribute' => $type,
                'params' => [
                    'value' => $value,
                    'get_all' => $getAll
                ]
            ]);
        }else{
            $response = ONE::get([
                'component' => 'empatia',
                'api' => 'page',
                'method' => 'listByType',
                'attribute' => $type,
                'params' => [
                    'get_all' => $getAll
                ]
            ]);
        }


        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingContentList"));
        }
        return $response->json()->data;
    }


    public static function getAllCbs($cbTypeCode = null){
        if(empty($cbTypeCode)){

            $response = ONE::get([
                'component' => 'empatia',
                'api' => 'entityCb',
                'method' => 'list',
            ]);
        }else{
            $response = ONE::get([
                'component' => 'empatia',
                'api' => 'entityCb',
                'method' => 'list',
                'params' => [
                    "code" => $cbTypeCode
                ]
            ]);
        }
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingAllCbsList"));
        }
        return $response->json()->data;
    }

    /**
     * @param $cbKey
     * @return mixed
     * @throws Exception
     */
    public static function getCbTypesByCbKey($cbKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cbTypes',
            'method' => 'cb',
            'attribute' => $cbKey

        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingGetCbTypesByCbKey"));
        }
        return $response->json();
    }

    public static function getTypesByCbKeys($cbKeys){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'cbTypes',
            'method' => 'cb',
            'params' => [
                "cbKeys" => $cbKeys
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingGetCbTypesByCbKey"));
        }
        return $response->json();
    }


    public static function getCbTypes($api){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'entityCb',
            'method' => 'list',
            'params' => [
                'code' => $api
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingCbTypeList"));
        }
        return $response->json()->data;
    }

    public static function getAllManagers(){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'user',
            'method' => 'list',
            'params' => [
                'role' => 'manager'
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetAllManagers"));
        }
        return $response->json()->data;
    }

    /**
     * Gets a list of Entity Managers without Auth
     *
     * @return mixed
     * @throws Exception
     */
    public static function getAllManagersNoAuthNeeded(){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'user',
            'method' => 'managersList',
            'params' => [
                'role' => 'manager'
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetAllManagers"));
        }
        return $response->json()->data;
    }

    public static function getAllUsers(){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'user',
            'method' => 'list',
            'params' => [
                'role' => 'user'
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetAllUsers"));
        }
        return $response->json()->data;
    }


    /**
     * @param $role
     * @return mixed
     * @throws Exception
     */
    public static function getUsers($role){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'user',
            'method' => 'list',
            'params' => [
                'role' => $role
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetAllUsers"));
        }
        return $response->json()->data;
    }

    public static function getUserEmail($userKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'user',
            'method' => 'userEmail',
            'params' => [
                'userKey' => $userKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetUserEmail"));
        }
        return $response->json();
    }

    /**
     * @param $entityGroupKey
     * @return mixed
     * @throws Exception
     */
    public static function getUsersByEntityGroupKey($entityGroupKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'entityGroup',
            'api_attribute'  => $entityGroupKey,
            'method' => 'listUsers'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetAllUsers"));
        }
        return $response->json();
    }



    public static function getAllLoginStatus(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'statusLogin',
            'method'    => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetAllLoginStatus"));
        }
        return $response->json()->data;
    }

    public static function getUsersByRole($request, $role)
    {
        $tableData = ONE::tableData($request);

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'user',
            'method' => 'list',
            'params' => [
                'tableData' => $tableData,
                'role' => $role
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetUsersByRole"));
        }
        return $response->json();
    }

    public static function getUsersWithStatus($status){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'user',
            'method'    => 'listWithStatus',
            'attribute'  => $status
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetUsersWithStatus"));
        }
        return $response->json()->data;
    }

    public static function getLanguageList(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'lang',
            'method' => 'list',
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetLanguageList"));
        }
        return $response->json()->data;
    }

    public static function getLanguages($entityKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'lang',
            'method' => 'languages',
            'params' => [
                'entity_key' => $entityKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetLanguages"));
        }
        return $response->json()->data;
    }

    public static function getAllLanguages(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'lang',
            'method' => 'listAll',
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetAllLanguages"));
        }
        return $response->json()->data;
    }

    public static function getCountryList(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'country',
            'method'    => 'list'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetCountryList"));
        }
        return $response->json()->data;
    }

    public static function getTimeZoneList(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'tz',
            'method'    => 'list'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetTimeZoneList"));
        }
        return $response->json()->data;
    }

    public static function getCurrencyList(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'currency',
            'method'    => 'list'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetCurrencyList"));
        }
        return $response->json()->data;
    }


    public static function getRole($roleKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'role',
            'attribute' => $roleKey
        ]);


        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetRole"));
        }
        return $response->json();
    }


    public static function getSite($siteKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'           => 'site',
            'attribute'        => $siteKey,
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetEntitySite"));
        }
        return $response->json();
    }


    public static function getSiteAdditionalLinks($siteKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'site',
            'method'    => 'additionalUrls',
            'api_attribute' => $siteKey,
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetSiteAdditionalUrls"));
        }
        return $response->json();
    }

    public static function getSiteAdditionalLinkById($additionalLinkId)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'site',
            'method'    => 'getAdditionalUrl',
            'api_attribute' => $additionalLinkId,
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetSiteAdditionalUrl"));
        }
        return $response->json();
    }

    public static function storeSiteAdditionalLink($site_id,$link)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'site',
            'method'    => 'storeSiteAdditionalLink',
            'params' => [
                'site_id' => $site_id,
                'link' => $link
            ]
        ]);

        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesOrchestrator.failedToStoreAdditionalLink"));
        }

        return $response->json();
    }

    public static function updateSiteAdditionalLink($url_id,$link)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'site',
            'method'    => 'updateSiteAdditionalLink',
            'params' => [
                'url_id' => $url_id,
                'link' => $link
            ]
        ]);
        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesOrchestrator.failedToUpdateAdditionalLink"));
        }

        return $response->json();
    }



    public static function getSiteById($siteId)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'site',
            'method'    => 'getSiteById',
            'api_attribute' => $siteId,
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetSiteById"));
        }
        return $response->json();
    }




    public static function getSiteEntity($url, $returnResponseCodeOnFailure = false)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'           => 'site',
            'method'        => 'entity',
            'params'        => ["url" => $url], // $_SERVER[HTTP_HOST]
        ]);
        if($response->statusCode() != 200){
            if ($returnResponseCodeOnFailure)
                throw new Exception($response->statusCode());
            else
                throw new Exception(trans("comModulesOrchestrator.errorGetEntitySite"));
        }
        return $response->json();
    }


    public static function getEntity($entityKey) {

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'entity',
            'attribute' => $entityKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetEntity"));
        }
        return $response->json();
    }

    /**
     * @param $entityKey
     * @return mixed
     * @throws Exception
     */
    public static function getPublicEntity($entityKey) {

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'       => 'publicEntityForNotify',
            'api_attribute' => $entityKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetEntity"));
        }
        return $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getSiteList() {
        // Getting the list of available sites
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'site',
            'method'    => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetSiteList"));
        }
        return $response->json()->data;
    }

    /**
     * @param $groupTypeKey
     * @return mixed
     * @throws Exception
     */
    public static function getGroupTypeByKey($groupTypeKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'groupType',
            'attribute'    => $groupTypeKey
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingGroupTypeByKey"));
        }
        return $response->json();
    }

    public static function getEntityGroupByKey($entityGroupKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'entityGroup',
            'attribute'    => $entityGroupKey
        ]);


        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingEntityGroupTypeByKey"));
        }
        return $response->json();
    }



    /**
     * @return mixed
     * @throws Exception
     */
    public static function getGroupTypes(){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'groupType',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingGroupTypes"));
        }
        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getEntityGroups(){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'entityGroup',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingEntityGroups"));
        }
        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getEntityGroupsByGroupTypeKey($groupTypeKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'entityGroup',
            'method'    => 'listByType',
            'attribute' => $groupTypeKey
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingEntityGroupsByGroupTypeKey"));
        }
        return $response->json()->data;
    }

    /**
     * @param $code
     * @return mixed
     * @throws Exception
     */
    public static function setEntityGroup($name, $designation, $groupTypeKey, $parentEntityGroupKey){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'entityGroup',
            'params' => [
                'name' => $name,
                'designation' => $designation,
                'group_type_key' => $groupTypeKey,
                'entity_group_key' => $parentEntityGroupKey,
            ]
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewGroupType"));
        }
        return $response->json();
    }

    /**
     * @param $code
     * @return mixed
     * @throws Exception
     */
    public static function setGroupType($groupTypeRequest){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'groupType',
            'params' => $groupTypeRequest->all()
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewGroupType"));
        }
        return $response->json();
    }

    public static function setNewCbTemplate($name, $api,$cbKey, $startDate = '01-02-2016', $endDate = '01-02-2016'){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'entityCbTemplate',
            'params' => [
                'name' => $name,
                'code' => $api,
                'cb_key' => $cbKey,
                'start_date' => $startDate,
                'end_date' =>  $endDate
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesOrchestrator.error_setting_new_cb_template_in_orchestrator"));
        }
        return $response;
    }

    public static function setNewCb($api,$cbKey, $startDate = '01-02-2016', $endDate = '01-02-2016'){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'entityCb',
            'params' => [
                'code' => $api,
                'cb_key' => $cbKey,
                'start_date' => $startDate,
                'end_date' =>  $endDate
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewCbInOrchestrator"));
        }
        return $response;

    }

    public static function setNewCategories($options){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'category',
            'method' => 'createCategories',
            'params' => [
                'categories' => $options
            ]
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewCategoriesInOrchestrator"));
        }
        return $response->json();

    }

    public static function setCategoriesIdeas($cbId,$options){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'idea',
            'method' => 'categories',
            'api_attribute' => $cbId,
            'params' => [
                'categories' => $options
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewCategoriesInCb"));
        }
    }

    public static function setNewRole($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'role',
            'params'    => $request->all()
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewRole"));
        }
        return $response->json();

    }


    public static function setNewEntitySite($entityKey ,$data)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'site',
            'params'    => $data
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewEntitySite"));
        }
        return $response->json();
    }


    public static function setUserRoles($userKey, $roles)
    {

        //TODO: remove
        $role = $roles[0];

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'user',
            'api_attribute' => $userKey,
            'method' => 'updateEntityUserRole',
            'params' => [
                'role_key' => $role
            ]
        ]);


        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewEntityRole"));
        }
        return $response->json()->roles;


    }


    public static function getUserRoles($userKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'user',
            'api_attribute' => $userKey,
        ]);


        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewUserEntityRole"));
        }
        return $response->json()->roles;


    }

    public static function updateGroupType($request,$groupTypeKey)
    {

        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'groupType',
            'params'    => $request->all(),
            'attribute' => $groupTypeKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingGroupType"));
        }
        return $response->json();
    }

    public static function updateEntityGroup($name, $designation, $groupTypeKey, $parentEntityGroupKey,$entityGroupKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'entityGroup',
            'params' => [
                'name' => $name,
                'designation' => $designation,
                'group_type_key' => $groupTypeKey,
                'entity_group_key' => $parentEntityGroupKey,
            ],
            'attribute' => $entityGroupKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingEntityGroup"));
        }
        return $response->json();
    }

    /**
     * @param $entityGroupKey
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function addEntityGroupUser($entityGroupKey, $userKey)
    {

        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'entityGroup',
            'api_attribute' => $entityGroupKey,
            'method' => 'user',
            'attribute' => $userKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingEntityGroupUser"));
        }
        return $response->json();
    }

    /**
     * @param $entityGroupKey
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function removeEntityGroupUser($entityGroupKey, $userKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'entityGroup',
            'api_attribute' => $entityGroupKey,
            'method' => 'removeUser',
            'attribute' => $userKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRemoveEntityGroupUser"));
        }
        return $response->json();
    }


    public static function updateEntityGroupsOrder($source, $destination, $ordering, $rootOrdering)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'entityGroup',
            'method'    => 'reorder',
            'params'    => [
                'parent_group_key' => ($destination == "" ? null : $destination), // updates parent
                'positions' => ($destination == "" ? $rootOrdering : $ordering),
            ],
            'attribute' => $source
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingEntityGroupsOrder"));
        }
        return $response->json();
    }


    public static function updateRole($request,$roleKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'role',
            'params'    => $request->all(),
            'attribute' => $roleKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingRole"));
        }
        return $response->json();
    }


    public static function updateEntitySite($request,$siteKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'site',
            'attribute' => $siteKey,
            'params'    => $request->all()
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingSite"));
        }
        return $response->json();
    }



    public static function deleteCb($api,$cbKey){
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'entityCb',
            'attribute' => $cbKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorDeletingCBinCBs"));
        }
    }


    public static function deleteRole($roleKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'role',
            'attribute' => $roleKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingRole"));
        }
    }


    /**
     * @param $siteKey
     * @throws Exception
     */
    public static function deleteEntitySite($siteKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'site',
            'attribute' => $siteKey,
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingEntitySite"));
        }
    }

    /**
     * @param $groupTypeKey
     * @throws Exception
     * @internal param $siteKey
     */
    public static function deleteGroupType($groupTypeKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'groupType',
            'attribute' => $groupTypeKey,
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingGroupType"));
        }
    }

    /**
     * @param $siteKey
     * @throws Exception
     */
    public static function deleteEntityGroup($entityGroupKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'entityGroup',
            'attribute' => $entityGroupKey,
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingGroupType"));
        }
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public static function getKioskTypes() {
        // Get Kiosk Type List
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'kiosktype',
            'method'    => 'list',
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingKioskTypeList"));
        }

        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getIdeas() {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'idea',
            'method'    => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingListIdeas"));
        }

        return  $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getEntities() {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'list'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingListEntities"));
        }
        return $response->json()->data;
    }


    /**
     * @param $key
     * @return mixed
     */
    public static function getKiosk($key) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'attribute' => $key
        ]);
        return $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getParametersTypes(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'orchParameterTypes',
            'method' => 'list'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingParametersTypes"));
        }
        return $response->json()->data;
    }


    /**
     * @param null $entityKey
     * @return mixed
     * @throws Exception
     */
    public static function getRolesList($entityKey = null)
    {
        if(!isset($entityKey)){
            $entityKey = ONE::getEntityKey();
        }

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'role',
            'method'    => 'list',
            'params' => [
                'entity_key' => $entityKey
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingRolesList"));
        }
        return $response->json()->data;

    }


    /**
     * @return mixed
     * @throws Exception
     */
    public static function getLayouts()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'layout',
            'method' => 'list'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingLayouts"));
        }
        return $response->json()->data;

    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function setNewLayout($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'layout',
            'params'    => $request->all()
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorSettingNewLayout"));
        }
        return $response->json();

    }

    /**
     * @param $layoutKey
     * @return mixed
     * @throws Exception
     */
    public static function getLayout($layoutKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'layout',
            'attribute' => $layoutKey
        ]);


        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingLayout"));
        }
        return $response->json();

    }

    /**
     * @param $request
     * @param $layoutKey
     * @return mixed
     * @throws Exception
     */
    public static function updateLayout($request, $layoutKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'layout',
            'params'    => $request->all(),
            'attribute' => $layoutKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingLayout"));
        }
        return $response->json();

    }

    /**
     * @param $layoutKey
     * @throws Exception
     */
    public static function deleteLayout($layoutKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'layout',
            'attribute' => $layoutKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingLayout"));
        }
    }

    /**
     * @param $entityKey
     * @param $layoutKey
     * @throws Exception
     */
    public static function deleteEntityLayout($entityKey, $layoutKey)
    {

        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'entity',
            'api_attribute' => $entityKey,
            'method' => 'Layout',
            'attribute' => $layoutKey ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorDeletingLayoutFromEntity"));
        }

    }

    /**
     * @param $entityKey
     * @param $layoutKey
     * @return mixed
     * @throws Exception
     */
    public static function setLayoutEntity($entityKey, $layoutKey = null, $layoutReference = null)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'addLayout',
            'params'    => [
                'layout_key' => $layoutKey,
                'layout_reference' => $layoutReference,
                'entity_key' => $entityKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingLayoutToEntity"));
        }
        return $response->json();

    }

    /**
     * @return null
     */
    public static function getEmpatiaStream()
    {
        $stream = ['title' => 'International Congress'];
        $result = json_encode($stream,true);
        return null;

    }


    /**
     * @return mixed
     * @throws Exception
     */
    public static function getParametersTemplatesKeys(){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'cbParameterTemplate',
            'method' => 'list',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingCbParameters"));
        }
        return $response->json()->data;
    }

    /**
     * @param $parameter_template_key
     * @return mixed
     * @throws Exception
     */
    public static function setParameterTemplate($parameter_template_key)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'cbParameterTemplate',
            'params'    => [
                'parameter_template_key' => $parameter_template_key
            ]
        ]);

        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesOrchestrator.errorSettingParameterTemplate"));
        }
        return $response->json();
    }

    /**
     * @param $key
     * @throws Exception
     */
    public static function deleteParameterTemplate($key)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'cbParameterTemplate',
            'attribute' => $key
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingParameterTemplate"));
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getHomePageTypes()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageType',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingHomepageTypesList"));
        }

        return $response->json()->data;
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public static function getGroupHomePageTypes()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageType',
            'method'    => 'groupsList'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingGroupsHomepageTypesList"));
        }

        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getHomePageGroupTypes($home_page_type_key)
    {

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'homePageType',
            'method'    => 'groupTypesList',
            'params' => [
                'home_page_type_key' => $home_page_type_key
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingHomepageTypesList"));
        }

        return $response->json()->data;
    }
    /**
     * @param $homePageTypeKey
     * @return mixed
     * @throws Exception
     */
    public static function getHomePageType($homePageTypeKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageType',
            'attribute' => $homePageTypeKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingHomepageTypes"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function setNewHomePageType($request){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'homePageType',
            'params'    => $request->all()
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesOrchestrator.errorSettingHomepageTypes"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $homePageTypeKey
     * @return mixed
     * @throws Exception
     */
    public static function updateHomePageType($request, $homePageTypeKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'homePageType',
            'params'    => $request->all(),
            'attribute' => $homePageTypeKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorSettingHomepageType"));
        }
        return $response->json();
    }

    /**
     * @param $homePageTypeKey
     * @throws Exception
     */
    public static function deleteHomePageType($homePageTypeKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'homePageType',
            'attribute' => $homePageTypeKey,
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingHomepageType"));
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getHomePagerurations()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingHomepageConfigurationsList"));

        }
        return $response->json()->data;
    }

    /**
     * @param $homePageConfigurationKey
     * @return mixed
     * @throws Exception
     */
    public static function getHomePageConfiguration($homePageConfigurationKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'attribute' => $homePageConfigurationKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingHomepageConfigurations"));
        }
        return $response->json();
    }

    public static function getHomePageConfigurationEdit($homePageConfigurationKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'homePageConfiguration',
            'method' => 'edit',
            'api_attribute' => $homePageConfigurationKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingHomepageConfiguration"));
        }
        return $response->json();

    }

    /**
     * @param $request
     * @param $translations
     * @return mixed
     * @throws Exception
     */
    public static function SetHomePageConfiguration($request, $translations)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'params'    => [
                'site_key' => $request->siteKey,
                'home_page_type_key' => $request->home_page_type_key,
                'translations' => $translations
            ]
        ]);

        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesOrchestrator.errorSettingHomepageConfiguration"));
        }
        return $response->json();
    }

    /**
     * @param $siteKey
     * @return mixed
     * @throws Exception
     */
    public static function getSiteHomePageConfiguration($siteKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'method'    => 'sitePages',
            'attribute' => $siteKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingHomepageSiteConfiguration"));
        }
        return $response->json()->data;
    }

    /**
     * @param $homePageConfigurationKey
     * @throws Exception
     */
    public static function deleteHomePageConfiguration($homePageConfigurationKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'attribute' => $homePageConfigurationKey,
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingHomepageConfiguration"));
        }
    }

    /**
     * @param $request
     * @param $homePageConfigurationKey
     * @param $translation
     * @return mixed
     * @throws Exception
     */
    public static function updateHomePageConfiguration($request, $homePageConfigurationKey, $translation)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'homePageConfiguration',
            'api_attribute' => $homePageConfigurationKey,
            'params' => [
                'home_page_type_key' => $request->home_page_type_key,
                'translations' => $translation
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingHomepageConfiguration"));
        }
        return $response->json();
    }

    /**
     * @param $userKey
     * @param $entityKey
     * @return mixed
     * @throws Exception
     */
    public static function storeUser($userKey, $entityKey){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'user',
            'params'    => [
                'user_key'      => $userKey,
                'entity_key'    => $entityKey
            ]
        ]);

        if($response->statusCode() != 201) {
            throw new Exception(trans("comModulesOrchestrator.errorStoreUserOrchestractor"));
        }
        return $response->json();
    }

    /**
     * @param null $homePageTypeKey
     * @return mixed
     * @throws Exception
     */
    public static function getHomePageTypeParents($homePageTypeKey = null)
    {
        if(!$homePageTypeKey){
            $response = ONE::post([
                'component' => 'empatia',
                'api'       => 'homePageType',
                'method'    => 'parentsList',
            ]);
        }else{
            $response = ONE::post([
                'component' => 'empatia',
                'api'       => 'homePageType',
                'method'    => 'parentsList',
                'params'    => [
                    'home_page_type_key' => $homePageTypeKey
                ]
            ]);
        }
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetHomePageTypeParents"));
        }
        return $response->json()->data;
    }

    public static function setHomePageConfigurationGroup($groupName,$homePageConfigurations)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'method'    => 'storeConfigurations',
            'params'    => [
                'group_name'               => $groupName,
                'home_page_configurations' => $homePageConfigurations
            ]
        ]);
        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesOrchestrator.failedToSetMultiHomePageConfigurations"));
        }

        return $response->json();
    }

    public static function getSiteHomePageConfigurationGroups($siteKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'method'    => 'listGroups',
            'attribute' => $siteKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetSiteHomePageConfigurationGroups"));
        }
        return $response->json()->data;
    }

    public static function getHomePageConfigurationGroup($groupConfigurationKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'method'    => 'showGroup',
            'attribute' => $groupConfigurationKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetHomePageConfigurationGroup"));
        }
        return $response->json();
    }

    public static function getHomePageConfigurationGroupEdit($groupConfigurationKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'method'    => 'editGroup',
            'attribute' => $groupConfigurationKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetHomePageConfigurationGroup"));
        }
        return $response->json();
    }

    public static function updateHomePageConfigurationGroup($groupKey, $homePageConfigurations)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'homePageConfiguration',
            'attribute' => $groupKey,
            'method' => 'updateGroup',
            'params' => [
                'home_page_configurations' => $homePageConfigurations
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.failedToUpdateHomePageConfigurationGroup"));
        }
        return $response->json();
    }

    public static function deleteHomePageConfigurationGroup($groupKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'method'    => 'destroyGroup',
            'attribute' => $groupKey
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToDeleteHomePageConfigurationGroup"));
        }
    }

    public static function getSiteHomePageConfigurations()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'homePageConfiguration',
            'method'    => 'siteConfigurations'
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetSiteHomePageConfigurations"));
        }
        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getEntityAuthMethods($entityKey = "")
    {
        if(!empty($entityKey)){
            $data =[
                'component' => 'empatia',
                'api'       => 'authmethod',
                'method' => 'listEntityAuthMethods',
                'params' => [
                    'entity_key' => $entityKey
                ]
            ];
        } else {
            $data = [
                'component' => 'empatia',
                'api'       => 'authmethod',
                'method' => 'listEntityAuthMethods'
            ];
        }
        $response = ONE::get($data);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.FailedToGetListOfAuthenticationMethods"));
        }
        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getAvailableEntityAuthMethods($entityKey = "")
    {
        if(!empty($entityKey)){
            $data =[
                'component' => 'empatia',
                'api'       => 'authmethod',
                'method' => 'listAvailableAuthMethods',
                'params' => [
                    'entity_key' => $entityKey
                ]
            ];
        } else {
            $data = [
                'component' => 'empatia',
                'api'       => 'authmethod',
                'method' => 'listAvailableAuthMethods'
            ];
        }


        $response = ONE::get($data);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.FailedToGetListOfAuthenticationMethods"));
        }
        return $response->json()->data;
    }

    /**
     * @param $entityKey
     * @param $authMethodKey
     * @throws Exception
     */
    public static function deleteEntityAuthMethod($entityKey, $authMethodKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'entity',
            'api_attribute' => $entityKey,
            'method' => 'AuthMethod',
            'attribute' => $authMethodKey ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.FailedToDeleteAuthenticationMethod"));
        }
    }

    public static function setEntityAuthMethod($entityKey, $authMethodKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'addAuthMethod',
            'params'    => [
                'authMethod_key' => $authMethodKey,
                'entity_key' => $entityKey
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.FailedToSetAuthenticationMethod"));
        }
        return $response;
    }


    public static function getCbTypesList()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'cbTypes',
            'method'    => 'list',

        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.FailedToGetCBTypes"));
        }
        return $response->json()->data;
    }

    public static function getAllEntityCbTemplatesList()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'entityCbTemplate',
            'method'    => 'list',

        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.failed_to_get_entity_cb_template"));
        }
        return $response->json()->data;
    }

    /**
     * @return array
     */
    public static function getRolesModuleAPI()
    {

        return array("cb" => ["forum", "ideas", "proposal"],
            "cm" => ["cm1", "cm2", "cm3", "cm4"]);

    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getModulesList()
    {

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'module',
            'method'    => 'list',

        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.FailedToGetModules"));
        }
        return $response->json();
    }

    /**
     * @param $entityKey
     * @return mixed
     * @throws Exception
     */
    public static function getActiveEntityModules($entityKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'entityModule',
            'attribute' => $entityKey
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetEntityModulesActive"));
        }
        return $response->json()->data;
    }

    /**
     * @param $entityKey
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public static function updateEntityModules($entityKey, $data)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'entityModule',
            'params'    => [
                'entity_key' => $entityKey,
                'modules' => $data
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.failedToUpdateEntityModules"));
        }
        return $response->json();
    }

    public static function setModuleTypeForCurrentEntity($moduleKey, $moduleTypeKey) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'           => 'entityModule',
            'api_attribute' => 'setModuleTypeForCurrentEntity',
            'params'     => [
                "moduleKey"     => $moduleKey,
                "moduleTypeKey" => $moduleTypeKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.failed_to_set_module_type_for_current_entity"));
        }
        return $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function setKiosk($data) {

        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'params'    => $data
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesOrchestrator.errorSetNewKiosk"));
        }

        return $response->json();
    }

    /**
     * @param $data
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function updateKiosk($data, $key)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'params'    => $data,
            'attribute' => $key
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdateKiosk"));
        }
        return $response->json();

    }


    /**
     * @param $key
     * @throws Exception
     */
    public static function deleteKiosk($key)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'attribute' => $key,
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorDeleteKiosk"));
        }
    }


    /**
     * @return mixed
     * @throws Exception
     */

    public static function getKioskList()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'method'    => 'list'
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetKioskList"));

        }
        return $response->json()->data;
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public static function getEntityRegisterParameters()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'parameters'
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetEntityRegisterParameters"));

        }
        return $response->json()->data;
    }

    public static function getUserAuthValidate()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'orchAuth',
            'method'    => 'validate'
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetUserAuthValidate"));

        }
        return $response->json();

    }

    public static function updateUserStatus($status, $userKey = null)
    {
        if($userKey){
            $response = ONE::post([
                'component' => 'empatia',
                'api'       => 'user',
                'method'    => 'updateStatus',
                'params'    => [
                    'status'    => $status,
                    'user_key'  => $userKey
                ]
            ]);
        }else{
            $response = ONE::post([
                'component' => 'empatia',
                'api'       => 'user',
                'method'    => 'updateStatus',
                'params'    => [
                    'status'    => $status,
                ]
            ]);
        }
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorUpdateUserStatus"));
        }

        return $response->json();

    }

    public static function deleteUser($userKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'user',
            'api_attribute'    => $userKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorDeletingUser"));
        }
    }

    public static function getVoteConfigParameterTypes($configCode)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'orchParameterTypes',
            'method' => 'getParameterTypesVoteConfig',
            'attribute' => $configCode
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetVoteConfigParameterTypes"));
        }

        return $response->json()->data;
    }

    public static function getParameterUserTypesList($parameterTypeCodes)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'parameterUserType',
            'method' => 'getParameterUserTypesList',
            'params' => [
                'parameterTypeCodes' => $parameterTypeCodes
            ]
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetParameterUserTypesList"));
        }

        return $response->json()->data;
    }

    /* ParameterUserTypes HTTP REQUESTs */
    public static function getParameterUserTypes(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'           => 'parameterUserType',
            'method'        => 'list',
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingParameterUserTypes"));
        }

        return $response->json()->data;
    }


    public static function getParameterUserType($parameterUserType) {

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'parameterUserType',
            'attribute' => $parameterUserType
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingParameterUserType"));
        }
        return $response->json();
    }


    public static function getEditParameterUserType($parameterUserType) {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'parameterUserType',
            'api_attribute' => $parameterUserType,
            'method' => 'edit'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingParameterUserTypeEdit"));
        }
        return $response->json();
    }

    public static function deleteParameterUserType($parameterUserType)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'parameterUserType',
            'attribute' => $parameterUserType
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingRole"));
        }
    }

    public static function createParameterUserType($parameterTypeCode,$parameterCode,$mandatory,$unique,$contentTranslation,$optionsWithTranslation){

        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'parameterUserType',
            'params' => [
                'parameter_type_code' => $parameterTypeCode,
                'parameter_code' => $parameterCode,
                'mandatory' => $mandatory,
                'parameter_unique' => $unique,
                'translations' => $contentTranslation,
                'parameter_user_options' => $optionsWithTranslation
            ]
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorCreatingParameterUserType"));
        }
        return $response->json();
    }

    public static function updateParameterUserType($parameterUserTypeKey,$parameterCode,$parameterTypeCode,$mandatory,$unique,$contentTranslation,$optionsWithTranslation){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'parameterUserType',
            'attribute' => $parameterUserTypeKey,
            'params' => [
                'parameter_type_code' => $parameterTypeCode,
                'parameter_code' => $parameterCode,
                'mandatory' => $mandatory,
                'parameter_unique' => $unique,
                'translations' => $contentTranslation,
                'parameter_user_options' => $optionsWithTranslation
            ]
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingParameterUserType"));
        }
        return $response->json();

    }


    /**
     * @return mixed
     * @throws Exception
     */
    public static function getParametersType($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'orchParameterTypes',
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingParametersType"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $parameterID
     * @return mixed
     * @throws Exception
     */
    public static function updateParameterTypes($request, $parameterID)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'orchParameterTypes',
            'params'    => $request->all(),
            'attribute' => $parameterID
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingParameterType"));
        }
        return $response->json();

    }

    /**
     * @param $parameterID
     * @throws Exception
     */
    public static function deleteParameterType($parameterID)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'orchParameterTypes',
            'attribute' => $parameterID
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingParameterType"));
        }
    }

    /**
     * @param ParameterTypesRequest $request
     * @return mixed
     * @throws Exception
     */
    public static function setParameterType(ParameterTypesRequest $request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'orchParameterTypes',
            'params'    => $request->all(),
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingParameterType"));
        }
        return $response->json();

    }

    /**
     * @param $siteKey
     * @return mixed
     * @throws Exception
     */

    public static function getSiteConfGroups($siteKey, $public = false){

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'siteConfGroup',
            'method'    => 'list',
            'params'    => [
                "siteKey" => $siteKey ,
                "public" => $public
            ],
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingSiteConfGroups"));
        }
        return $response->json()->data;
    }

    /**
     * @param id
     * @return mixed
     * @throws Exception
     */
    public static function getSiteConfGroup($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'siteConfGroup',
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingSiteConfGroup"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $siteConfigGroupID
     * @return mixed
     * @throws Exception
     */
    public static function updateSiteConfGroup($request, $siteConfigGroupID, $translations)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'siteConfGroup',
            'params'    => [
                "request" => $request->all(),
                "translations" => $translations,
            ],
            'attribute' => $siteConfigGroupID
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingSiteConfGroup"));
        }
        return $response->json();

    }

    /**
     * @param request
     * @return mixed
     * @throws Exception
     */
    public static function setSiteConfGroup(SiteConfGroupRequest $request,$translations)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'siteConfGroup',
            'params'    => [
                "request" => $request->all(),
                "translations" => $translations,
            ],
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingSiteConfGroup"));
        }
        return $response->json();

    }

    /**
     * @param $siteConfGroupId
     * @throws Exception
     */
    public static function deleteSiteConfGroup($siteConfGroupId)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'siteConfGroup',
            'attribute' => $siteConfGroupId
        ]);
        if($response->statusCode()!= 200) {

            throw new Exception(trans("comModulesOrchestrator.errorDeletingSiteConfGroup"));
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getSiteConfs() {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'SiteConf',
            'method'    => 'list'
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingSiteConfs"));
        }
        return $response->json()->data;
    }

    /**
     * @param id
     * @return mixed
     * @throws Exception
     */
    public static function getSiteConf($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'SiteConf',
            'attribute' => $id
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingSiteConf"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $siteConfigID
     * @return mixed
     * @throws Exception
     */
    public static function updateSiteConf($request, $siteConfigID, $translations)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'SiteConf',
            'params'    => [
                "request" => $request->all(),
                "translations" => $translations,
            ],
            'attribute' => $siteConfigID
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingSiteConf"));
        }
        return $response->json();

    }

    /**
     * @param request
     * @return mixed
     * @throws Exception
     */
    public static function setSiteConf(SiteConfRequest $request, $translations)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'SiteConf',
            'params'    => [
                "request" => $request->all(),
                "translations" => $translations,
            ],
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingSiteConf"));
        }
        return $response->json();

    }

    /**
     * @param $siteConfId
     * @throws Exception
     */
    public static function deleteSiteConf($siteConfId)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'SiteConf',
            'attribute' => $siteConfId
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingSiteConf"));
        }
    }

    public static function getSiteConfEdit($id)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'SiteConf',
            'method'    => 'edit',
            'api_attribute' => $id
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesCB.errorRetrievingSiteConfigEdit"));
        }
        return $response->json();
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public static function getSiteSiteConfigs(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'SiteSiteConfs',
            'method'    => 'list',
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingSiteSiteConfs"));
        }

        return $response->json()->data;
    }

    public static function getSiteConfValues(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'siteConfValues',
            'method'    => 'list',
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingSiteConfValues"));
        }

        return $response->json()->data;
    }

    /**
     * @param id
     * @return mixed
     * @throws Exception
     */
    public static function getSiteSiteConfig($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'SiteSiteConfs',
            'attribute' => $id
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingSiteConf"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $siteConfigID
     * @return mixed
     * @throws Exception
     */
    public static function updateSiteSiteConfig(SiteSiteConfigRequest $request, $siteConfigID)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'SiteSiteConfs',
            'params'    => $request->all(),
            'attribute' => $siteConfigID
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingSiteConf"));
        }
        return $response->json();

    }

    /**
     * @param request
     * @return mixed
     * @throws Exception
     */
    public static function setSiteSiteConfig(SiteSiteConfigRequest $request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'SiteSiteConfs',
            'params'    => $request->all(),
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingSiteConf"));
        }
        return $response->json();

    }

    public static function setSiteConfValues(SiteSiteConfigRequest $request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'SiteSiteConfs',
            'params'    => $request->all(),
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorAddingSiteConf"));
        }
        return $response->json();

    }

    /**
     * @param $siteConfGroupId
     * @throws Exception
     */
    public static function deleteSiteSiteConfig($siteSiteConfigId)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'SiteSiteConfs',
            'attribute' => $siteSiteConfigId
        ]);
        if($response->statusCode()!= 200) {

            throw new Exception(trans("comModulesOrchestrator.errorDeletingSiteConfGroup"));
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getSiteUseTerm(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'site',
            'method'    => 'ethics',
            'attribute' => 'use_terms'
        ]);

        if($response->statusCode() != 200){
            return null;
        }
        return $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getSitePrivacyPolicy(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'site',
            'method'    => 'ethics',
            'attribute' => 'privacy_policy'
        ]);

        if($response->statusCode() != 200){
            return null;
        }
        return $response->json();
    }

    /* END ParameterUserTypes HTTP REQUESTs */

    public static function getUserByKey($userKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'user',
            'attribute' => $userKey
        ]);

        if($response->statusCode() != 200){
            return null;
        }
        return $response->json();
    }

    /**
     * @param $entityKey
     * @param $languageId
     * @throws Exception
     */
    public static function deleteLanguage($entityKey, $languageId)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'entity',
            'api_attribute' => $entityKey,
            'method' => 'Language',
            'attribute' => $languageId
        ]);

        if ($response->statusCode() == 409) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingDefaultLanguage"));
        }

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingLanguage"));
        }
    }

    /**
     * @return null
     */
    public static function getTotalUsersRegistered()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'entity',
            'method' => 'totalUsers'
        ]);
        if($response->statusCode() != 200){
            return null;
        }
        return $response->json();
    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function setNewModule($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'module',
            'params' => $request->all()
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorCreatingModule"));
        }
        return $response->json();

    }

    /**
     * @param $key
     * @throws Exception
     */
    public static function getModule($key)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'module',
            'attribute' => $key
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetModule"));
        }
        return $response->json();

    }

    /**
     * @param $request
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function updateModule($request, $key)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'module',
            'attribute' => $key,
            'params'    => $request->all()

        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdateModule"));
        }
        return $response->json();

    }

    /**
     * @param $key
     * @throws Exception
     */
    public static function deleteModule($key)
    {

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'module',
            'attribute' => $key
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingModule"));
        }

    }

    /**
     * @param $moduleKey
     * @return mixed
     * @throws Exception
     */
    public static function getModuleTypesList($moduleKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'moduleType',
            'method'    => 'list',
            'params' => [
                'module_key' => $moduleKey
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.failedToGetModuleTypes"));
        }
        return $response->json()->data;

    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function setNewModuleType($request,$module_key)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'moduleType',
            'params' => [
                'module_key' => $module_key,
                'module_types' => [$request->all()]
            ]
        ]);
        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorCreatingModuleType"));
        }
        return $response->json();

    }

    /**
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function getModuleType($key)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'moduleType',
            'attribute' => $key
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetModuleType"));
        }
        return $response->json();


    }

    /**
     * @param $request
     * @param $key
     * @return mixed
     * @throws Exception
     */
    public static function updateModuleType($request, $key)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'moduleType',
            'attribute' => $key,
            'params'    => $request->all()

        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdateModuleType"));
        }
        return $response->json();

    }

    /**
     * @param $key
     * @throws Exception
     */
    public static function deleteModuleType($key)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'moduleType',
            'attribute' => $key
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingModuleType"));
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getEntityModules()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'entityModule',
            'method'    => 'moduleCode'
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.failedToGetEntityModules"));
        }
        return $response->json()->data;
    }

    /**
     * @param $requestUser
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function updateUser($entityKey, $userKey, $role)
    {
        if(isset($entityKey)){
            $response = ONE::put([
                'component' => 'empatia',
                'api'       => 'user',
                'api_attribute' => $userKey,
                'params' => [
                    'role'          => $role,
                    'entity_key'    => $entityKey
                ]
            ]);
        }else{
            $response = ONE::put([
                'component' => 'empatia',
                'api'       => 'user',
                'api_attribute' => $userKey,
                'params'    => [
                    'role'      => $role
                ]
            ]);
        }
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorUpdateUser"));
        }

        return $response->json();

    }


    //    ------------------------- BEGIN LOGIN LEVEL REQUESTS -------------------------
    /**
     * @return mixed
     * @throws Exception
     */
    public static function getLoginLevels($siteKey = null){
        if(!empty($siteKey)){
            $response = ONE::get([
                'component' => 'empatia',
                'api' => 'level',
                'method'    => 'list',
                'params'    => ['site_key' => $siteKey]
            ]);
        }else{
            $response = ONE::get([
                'component' => 'empatia',
                'api' => 'level',
                'method'    => 'list'
            ]);
        }


        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingLoginLevels"));
        }
        return $response->json()->data;
    }

    /**
     * @param $loginLevelKey
     * @return mixed
     * @throws Exception
     */
    public static function getLoginLevel($loginLevelKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'level',
            'attribute' => $loginLevelKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingLoginLevel"));
        }
        return $response->json();
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getSiteLoginLevels()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'level',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingSiteLoginLevels"));
        }
        return $response->json()->data;
    }

    /**
     * @param $request
     * @param $loginLevelKey
     * @return mixed
     * @throws Exception
     */
    public static function updateLoginLevel($request, $loginLevelKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'level',
            'attribute' => $loginLevelKey,
            'params'    => $request->all()
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdateLoginLevel"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function setLoginLevel($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'level',
            'params' => $request->all()
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.errorCreatingLoginLevel"));
        }
        return $response->json();
    }

    /**
     * @param $loginLevelKey
     * @throws Exception
     */
    public static function deleteLoginLevel($loginLevelKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'level',
            'attribute' => $loginLevelKey
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingLoginLevel"));
        }
    }

    /**
     * @param $userKey
     * @param $levelPosition
     * @return mixed
     * @throws Exception
     */
    public static function setUserLevel($userKey, $levelPosition){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'user',
            'api_attribute' => $userKey,
            'method'    => 'level',
            'attribute' => $levelPosition
        ]);


        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorSetingNewUserLevel"));
        }
        return $response->json();
    }

    /**
     * @param $loginLevelKey
     * @return mixed
     * @throws Exception
     */
    public static function getLoginLevelParameters($loginLevelKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'level',
            'api_attribute' => $loginLevelKey,
            'method'    => 'levelParameters'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.errorGettingLoginLevelParameters"));
        }
        return $response->json()->data;
    }

    /**
     * @param $parameterUserTypeKey
     * @param $levelParameterKey
     * @return mixed
     * @throws Exception
     */
    public static function updateLoginLevelParameters($parameterUserTypeKey, $levelParameterKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'level',
            'api_attribute' => $levelParameterKey,
            'method'    => 'updateParameters',
            'params' => [
                'parameter_user_type_key' => $parameterUserTypeKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingLevelParameters"));
        }
        return $response->json();
    }

    /**
     * @param $loginLevels
     * @return mixed
     * @throws Exception
     */
    public static function updateLoginLevelPositions($loginLevels)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'level',
            'method'    => 'reorder',
            'params' => [
                'login_levels' => $loginLevels
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingLoginLevelPositions"));
        }
        return $response->json();
    }
    //    ------------------------- END LOGIN LEVEL REQUESTS -------------------------


    //    ------------------------- BEGIN ENTITY LOGIN LEVEL REQUESTS -------------------------
    /**
     * @param null $entityKey
     * @return mixed
     * @throws Exception
     */
    public static function getAllEntityLoginLevels($entityKey = null){

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'loginLevel',
            'method'    => 'list',
            'params'    => [
                'entity_key' => $entityKey
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.error_getting_entity_login_levels"));
        }
        return $response->json()->data;
    }

    /**
     * @param $entityKey
     * @param $loginLevelKey
     * @return mixed
     * @throws Exception
     */
    public static function getEntityLoginLevel($loginLevelKey,$entityKey = null)
    {
        $response = ONE::get([

            'component' => 'empatia',
            'api' => 'loginLevel',
            'attribute' => $loginLevelKey,
            'params' => [
                'entity_key' => $entityKey
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.error_getting_login_level"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @param $loginLevelKey
     * @return mixed
     * @throws Exception
     */
    public static function updateEntityLoginLevel($request, $loginLevelKey)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'loginLevel',
            'attribute' => $loginLevelKey,
            'params'    => $request->all()
        ]);

        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_entity_login_level"));
        }
        return $response->json();
    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function setEntityLoginLevel($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'loginLevel',
            'params' => $request->all()
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_creating_login_level"));
        }
        return $response->json();
    }

    /**
     * @param $loginLevelKey
     * @throws Exception
     */
    public static function deleteEntityLoginLevel($loginLevelKey)
    {

        $response = ONE::delete([
            'component' => 'empatia',
            'api' => 'loginLevel',
            'attribute' => $loginLevelKey
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.error_deleting_login_level"));
        }
    }

    /**
     * @param $loginLevelKey
     * @return mixed
     * @throws Exception
     */
    public static function getEntityLoginLevelParameters($loginLevelKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'loginLevel',
            'api_attribute' => $loginLevelKey,
            'method'    => 'getLoginLevelParameters'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.error_getting_login_level_parameters"));
        }
        return $response->json()->data;
    }

    /**
     * @param $parameterUserTypeKey
     * @param $levelParameterKey
     * @return mixed
     * @throws Exception
     */
    public static function updateEntityLoginLevelParameters($parameterUserTypeKey, $levelParameterKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'loginLevel',
            'api_attribute' => $levelParameterKey,
            'method'    => 'updateLoginLevelParameters',
            'params' => [
                'parameter_user_type_key' => $parameterUserTypeKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_updating_login_level_parameters"));
        }
        return $response->json();
    }


    /** Check and Update User Login Level
     * @param $userKey
     * @return array
     */
    public static function checkAndUpdateUserLoginLevel($userKey)
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'user',
            'method'        => 'autoCheckLoginLevel',
            'params'        => [
                'user_key'      => $userKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            return [];
        }
        return $response->json()->data;
    }

    /** Check and Update User Login Level
     * @throws Exception
     */
    public static function autoUpdateEntityUsersLoginLevels()
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'user',
            'method'        => 'autoUpdateEntityUsersLoginLevels',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorAutoUpdateEntityUsersLoginLevels"));
        }
        return $response->json();
    }

    public static function autoUpdateUserLoginLevels()
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'user',
            'method'        => 'updateUserLoginLevels',
        ]);


        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorAutoUpdateUserLoginLevels"));
        }
        return $response->json();

    }


    /**
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function getUserLoginLevels($userKey)
    {
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'user',
            'method'        => 'loginLevels',
            'api_attribute' => $userKey
        ]);

        if($response->statusCode()!= 200) {
            return [];
        }
        return $response->json()->data;
    }


    public static function getManualLoginLevelUsers()
    {
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'loginLevel',
            'method'        => 'manualListUsers',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_manual_login_level_users"));
        }
        return $response->json()->data;
    }


    public static function updateManualLoginLevelUser($userKey,$loginLevelKey)
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'user',
            'api_attribute' => $userKey,
            'method'        => 'manualCheckLoginLevel',
            'params' => [
                'login_level_key' => $loginLevelKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_manual_login_level_users"));
        }
        return $response->json()->data;
    }



    public static function UserLoginLevels($userKey, $cbKey, $code)
    {
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'user',
            'api_attribute' => $userKey,
            'method'        => 'userLoginLevels',
            'params' => [
                'cbKey' => $cbKey,
                'codeConfigPermission'=>$code
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_user_login_levels"));
        }
        return $response->json()->data;
    }

    /**
     * @param $loginLevelKey
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function manualGrantLoginLevel($loginLevelKey, $userKey)
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'user',
            'api_attribute' => $userKey,
            'method'        => 'manualGrantLoginLevel',
            'params' => [
                'login_level_key' => $loginLevelKey,
                'user_key' => $userKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_manually_granting_login_level"));
        }
        return $response->json();
    }

    /**
     * @param $loginLevelKey
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function manualRemoveLoginLevel($loginLevelKey, $userKey)
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'user',
            'api_attribute' => $userKey,
            'method'        => 'manualRemoveLoginLevel',
            'params' => [
                'login_level_key' => $loginLevelKey,
                'user_key' => $userKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_manually_remove_login_level"));
        }
        return $response->json();
    }


    //    ------------------------- END ENTITY LOGIN LEVEL REQUESTS -------------------------
    /**
     * @param $auth_method_key
     * @return mixed
     * @throws Exception
     */
    public static function getAuthMethod($auth_method_key)
    {

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'authmethod',
            'attribute' => $auth_method_key
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_auth_method"));
        }
        return $response->json();

    }

    /**
     * @param $request
     * @param $auth_method_key
     * @return mixed
     * @throws Exception
     */
    public static function updateAuthMethod($request, $auth_method_key)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'authmethod',
            'params'    => $request->all(),
            'attribute' => $auth_method_key
        ]);

        if($response->statusCode()!= 200)
        {
            throw new Exception(trans("comModulesOrchestrator.error_updating_auth_method"));

        }
        return $response->json();

    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function setAuthMethod($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'authmethod',
            'params'    => $request->all()
        ]);

        if($response->statusCode()!= 201)
        {
            throw new Exception(trans("comModulesOrchestrator.error_creating_auth_method"));
        }
        return $response->json();

    }

    /**
     * @param $auth_method_key
     * @throws Exception
     */
    public static function deleteAuthMethod($auth_method_key)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'authmethod',
            'attribute' => $auth_method_key,
        ]);
        if($response->statusCode() != 200)
        {
            throw new Exception(trans("comModulesOrchestrator.error_deleting_auth_method"));
        }
    }

    public static function getAuthMethodsList()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'           => 'authmethod',
            'method'        => 'list',
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.error_getting_auth_methods_list"));
        }
        return $response->json()->data;
    }


    public static function getPermissionsList($value)
    {

        $response = ONE::get([
            'component' => 'empatia',
            'api'           => 'entityPermissions',
            'method'        => 'list',
            'params'        => $value
        ]);
        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesOrchestrator.error_getting_entity_permissions_list"));
        }
        return $response->json()->data;
    }

    public static function setPermissions($value)
    {

        $response = ONE::post([
            'component' => 'empatia',
            'api'           => 'entityPermissions',
            'params'        => $value
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesOrchestrator.error_setting_entity_permissions"));
        }
        return $response->json()->data;
    }


    public static function migrateUserToEntity($userKey) {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'user',
            'method'    => "migrateUserToEntity",
            'params'    => [
                'user_key'  => $userKey,
            ]
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.failed_to_migrate_user_to_entity"));
        }

        return $response->json()->user;
    }

    /**
     * Update the User Level
     *
     * @param $userKey
     * @param $userParameters
     * @return mixed
     * @throws Exception
     */
    public static function updateUserLevel($userKey, $userParameters)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'           => 'user',
            'api_attribute' => $userKey,
            'method'        => 'updateLevel',
            'params'        => [
                'user_parameters' => $userParameters
            ]
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingUserLevel"));
        }
        return $response->json();
    }

    /**
     * Deletes the User Level
     *
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function deleteUserLevel($userKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'           => 'user',
            'api_attribute' => $userKey,
            'method'        => 'updateLevel'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingUserLevel"));
        }
        return $response->json();
    }

    /**
     * @param $user
     * @return mixed
     * @throws Exception
     */
    public static function checkAndUpdateUserLevel($user)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'           => 'user',
            'method'        => 'checkAndUpdateLevel',
            'params'        => [
                'user_key'      => $user->user_key,
                'user_level'    => $user->user_level,
                'user_confirmed'    => $user->confirmed,
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorCheckingAndUpdatingUserLevel"));
        }
        return $response->json();
    }

    /**
     * @param $userKey
     * @return mixed
     * @throws Exception
     */
    public static function getUserLevel($userKey, $confirmed = 1)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'           => 'user',
            'api_attribute' => $userKey,
            'method'        => 'getUserLevel',
            'params'        => [
                'user_confirmed' => $confirmed
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorGettingUserLevel"));
        }
        return $response->json();

    }



    public static function getSiteEthics($siteKey, $code,$version)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'           => 'site',
            'api_attribute'     => $siteKey,
            'method' => 'siteEthic',
            'params' => [
                'site_ethic_type_code' => $code,
                'version' => $version
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.error_get_entity_site_ethics"));
        }
        return $response->json()->data;
    }

    public static function setSiteEthic($siteKey, $typeCode, $contentTranslation)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'           => 'siteEthic',
            'params' => [
                'site_key' => $siteKey,
                'site_ethic_type_code' => $typeCode,
                'translations' =>$contentTranslation
            ]
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesOrchestrator.error_set_entity_site_ethics"));
        }
        return $response->json();


    }

    public static function activateSiteEthicVersion($siteEthicKey, $version)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'           => 'siteEthic',
            'api_attribute'     => $siteEthicKey,
            'method' => 'activateVersion',
            'params' => [
                'version' =>$version
            ]
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.error_activate_entity_site_ethics"));
        }
        return $response->json();

    }

    public static function getSiteEthicByKey($ethicKey, $version)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'           => 'siteEthic',
            'attribute' => $ethicKey,
            'params' => [
                'version' => $version
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.error_get_site_ethic_by_key"));
        }
        return $response->json();

    }

    public static function updateSiteEthic($siteKey,$ethicKey, $contentTranslation)
    {
        $response = ONE::put([
            'component' => 'empatia',
            'api'           => 'siteEthic',
            'attribute' => $ethicKey,
            'params' => [
                'site_key' => $siteKey,
                'translations' => $contentTranslation
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.error_update_site_ethic"));
        }
        return $response->json();
    }

    public static function deleteSiteEthic($siteEthicKey)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'siteEthic',
            'attribute' => $siteEthicKey
        ]);
        if($response->statusCode() != 200)
        {
            throw new Exception(trans("comModulesOrchestrator.error_deleting_site_ethic"));
        }
    }



    /** -----------------------------------------------------------
     *  {BEGIN} Methods to deal with the entity registration values
     * ------------------------------------------------------------
     */

    /**
     * @param $entityKey
     * @param $type
     * @return
     * @throws Exception
     */
    public static function getEntityRegistrationValues($request, $entityKey,$type) {

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'getEntityRegistrationValues',
            'params' => [
                'entity_key' => $entityKey,
                'type' => $type,
                'tableData' => One::tableData($request),
            ]
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetEntityRegistrationValues"));
        }
        return $response->json();
    }

    /**
     * @param $importedData
     * @param $type
     * @param $entityKey
     * @return mixed
     * @throws Exception
     */
    public static function importRegistrationFields($importedData, $type, $entityKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'entity',
            'method' => 'importRegistrationFields',
            'params' => [
                'values' => $importedData,
                'type' => $type,
                'entity_key' => $entityKey
            ]
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.errorEntityImportRegistrationFields"));
        }
        return $response->json();


    }

    /**
     * @param $valueId
     * @param $type
     * @param $entityKey
     * @throws Exception
     * @internal param $siteKey
     */
    public static function deleteEntityRegistrationValue($valueId,$type,$entityKey)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => 'deleteRegistrationValues',
            'params' => [
                'value_id' => $valueId,
                'type' => $type,
                'entity_key' => $entityKey
            ]
        ]);
        if($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.errorDeletingVatNumber"));
        }
    }
    /** ----------------------------------------------------------
     *  {END} Methods to deal with the entity registration values
     * -----------------------------------------------------------
     */

    /**
     * DEPRECATED. use siteUsersToModerate instead
     *
     * @return mixed
     * @throws Exception
     */
    public static function getUsersToModerate(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'level',
            'method'    => 'usersToModerate'
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetUsersToModerate"));
        }
        return $response->json()->data;
    }

    /**
     * @param Request $request
     * @param $siteKey
     * @return mixed
     * @throws Exception
     */
    public static function siteUsersToModerate(Request $request, $siteKey){

        $tableData = ONE::tableData($request);

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'level',
            'method'    => 'siteUsersToModerate',
            'params' => [
                'table_data' => $tableData,
                'site_key' => $siteKey,
            ],
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorGetSiteUsersToModerate"));
        }
        return $response->json()->data;
    }

    public static function setUsersToModerate($userKey, $siteKey){

        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'user',
            'api_attribute' => $userKey,
            'method'    => 'site',
            'attribute' => $siteKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorSetUsersToModerate"));
        }
        return $response->json();
    }

    public static function SmsUpdateLevel()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'user',
            'method'    => 'smsUpdateLevel',
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorUpdatingSmsLevel"));
        }
        return $response->json();
    }


    /**
     * Returns the number of registered users for entity
     * @return integer
     * @throws Exception
     */
    public static function getPublicUsersCount(){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'user',
            'method' => 'count',
        ]);
        if($response->statusCode() != 200){
            //throw new Exception(trans("comModulesOrchestrator.errorGetPublicUsersCount"));
            return "--";
        }
        return $response->json()->data ?? "--";
    }


    /**
     * checks if the vat number is valid
     * @param $vatNumberToValidate
     * @param $name
     * @param $surname
     * @return
     * @throws Exception
     */
    public static function validateVatNumber($vatNumberToValidate,$name,$surname)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'validateVatNumber',
            'params' => [
                'vat_number' => $vatNumberToValidate,
                'name' => $name,
                'surname' => $surname
            ],
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorValidatingVatNumber"));
        }
        return $response->json();

    }

    /**
     * checks if the domain name is valid
     * @param $domainNameToValidate
     * @return
     * @throws Exception
     */
    public static function validateDomainName($domainNameToValidate)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'validateDomainName',
            'params' => [
                'domain_name' => $domainNameToValidate
            ],
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorValidatingDomainName"));
        }
        return $response->json();

    }


    /** -----------------------------------------------------------
     *  {BEGIN} Methods to deal with messages from user to entity
     *  and vice versa
     * ------------------------------------------------------------
     * @param Request $request
     * @param array $tags
     * @return
     * @throws Exception
     */

    public static function sendMessage($request,$tags = [])
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'message',
            'params' => [
                'message'   => $request->message,
                'to'        => $request->to ?? null,
                'topic_key' => $request->topic_key ?? null,
                'tags'      => $tags,
            ],
        ]);
        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesOrchestrator.errorSendingMessage"));
        }
        return $response->json();

    }

    public static function getMessages()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'message',
            'method'    => 'list'
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingMessage"));
        }
        return $response->json();

    }

    public static function getMessagesFromUser($userKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'message',
            'method' => 'messagesFrom',
            'attribute' => $userKey
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingMessageFromUser"));
        }
        return $response->json();

    }

    /**
     * @param $request
     * @return mixed
     * @throws Exception
     */
    public static function markMessagesAsSeen($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'message',
            'method' => 'markAsSeen',
            'params' => [
                'from' => $request->from ?? null,
            ],
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorMarkingMessageAsSeen"));
        }
        return $response->json();

    }

    public static function markMessagesAsUnseen($request)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'message',
            'method' => 'markAsUnseen',
            'params' => [
                'from' => $request->from ?? null,
                'messageKey' => $request->messageKey
            ],
        ]);

        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorMarkingMessageAsSeen"));
        }
        return $response->json();

    }

    public static function deleteMessage($request)
    {
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'message',
            'attribute' => $request->message_key,

        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorDeletingMessage"));
        }
        return $response->json();

    }

    public static function getUsersWithUnreadMessages()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'message',
            'method' => 'usersWithMessages',
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingUsersWithUnreadMessages"));
        }
        return $response->json();
    }


    public static function getUsersWithUnreadMessages2($arguments)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'message',
            'method' => 'usersWithMessages2',
            'params' => [
                'arguments' => $arguments
            ],
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.errorRetrievingUsersWithUnreadMessages"));
        }
        return $response->json();
    }

    /** -----------------------------------------------------------
     *  {END} Methods to deal with messages from user to entity
     *  and vice versa
     * ------------------------------------------------------------
     */


    /* ------------------------------------- Content (Pages/Events/News) ------------------------------------- */

    /**
     * @param $contentKey
     * @return mixed
     * @throws Exception
     */
    public static function getPage($contentKey)
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'page',
            'attribute' => $contentKey
        ]);
        if($response->statusCode() != 200){
            throw new Exception(trans("comModulesOrchestrator.error_retrieving_page"));
        }
        return $response->json();
    }

    public static function sendMessageToAll($message)
    {
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'message',
            'method'    => 'sendToAll',
            'params' => [
                'message' => $message,
            ],
        ]);

        if($response->statusCode() != 201){
            throw new Exception(trans("comModulesOrchestrator.error_sending_message_to_all"));
        }
        return $response->json();

    }

    public static function UserLoginLevelsVotes($userKey, $cbKey, $arrayVote)
    {

        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'user',
            'api_attribute' => $userKey,
            'method'        => 'userLoginLevelsVotes',
            'params' => [
                'cbKey' => $cbKey,
                'configPermission'=>$arrayVote
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_user_login_levels_votes"));
        }

        return $response->json()->data;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function UserIgnoredQuestionnaires()
    {
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'cbQuestionnaireUser',
            'method'        => 'getUserIgnoredQuestionnaires'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_user_ignored_questionnaires"));
        }
        return $response->json()->data;
    }

    public static function getListOfAvailableUsersToSendEmails()
    {
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'entity',
            'method'        => 'getListOfAvailableUsersToSendEmails'
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_user_ignored_questionnaires"));
        }
        return $response->json()->data;
    }

    public static function listAccessType(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'accesstype',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_list_access_type"));
        }
        return $response->json()->data;
    }

    public static function storeAccessMenu($request){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'accessmenu',
            'params'    => $request->all()
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_store_access_menu"));
        }
        return $response->json();
    }

    public static function getAccessMenu($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'accessmenu',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_access_menu"));
        }
        return $response->json();
    }

    public static function updateAccessMenu($request, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'accessmenu',
            'params'    => [
                'access_type_id' => $request->access_type_id,
                'entity_id' => $request->entity_id,
                'site_key' => $request->site_key,
                'name' => $request->name,
                'description' => $request->description,
                'active' => $request->active
            ],
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_access_menu"));
        }
        return $response->json();
    }

    public static function deleteAccessMenu($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'accessmenu',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_access_menu"));
        }
        return $response->json();
    }

    public static function activateAccessMenu($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'accessmenu',
            'api_attribute' => $id,
            'method' => 'activate',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_activate_access_menu"));
        }
        return $response->json();
    }

    public static function listAccessMenu(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'accessmenu',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_list_access_menu"));
        }
        return $response->json()->data;
    }

    public static function storeAccessPage($request){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'accesspage',
            'params'    => $request->all()
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_activate_access_menu"));
        }
        return $response->json();
    }

    public static function getAccessPage($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'accesspage',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_access_page"));
        }
        return $response->json();
    }

    public static function updateAccessPage($request, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'accesspage',
            'params'    => [
                'access_type_id' => $request->access_type_id,
                'entity_id' => 1,
                'name' => $request->name,
                'description' => $request->description,
                'active' => $request->active
            ],
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_access_page"));
        }
        return $response->json();
    }

    public static function deleteAccessPage($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'accesspage',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_access_page"));
        }
        return $response->json();
    }

    public static function listAccessPage(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'accesspage',
            'method'    => 'list',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_list_access_page"));
        }
        return json_decode($response->content());
    }

    public static function createUser($request){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'user',
            'params'    => [
                'user_key'  => $request->json()->user_key
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_create_user"));
        }
        return $response->json();
    }


    public static function setCooperators($topicKey, $cooperators)
    {
        $response = ONE::post([
            'component'     => 'empatia',
            'api'           => 'topic',
            'method'        => 'cooperators',
            'api_attribute' => $topicKey,
            'params' => [
                'cooperators' => $cooperators
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_cooperators"));
        }
        return $response->json();
    }


    public static function deleteCooperator($topicKey, $userKey)
    {
        $response = ONE::delete([
            'component'     => 'empatia',
            'api'           => 'topic',
            'method'        => 'cooperators',
            'api_attribute' => $topicKey,
            'params' => [
                'userKey' => $userKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_cooperator"));
        }
        return $response->json();
    }

    public static function updateCooperatorPermission($topicKey, $request)
    {
        $response = ONE::put([
            'component'     => 'empatia',
            'api'           => 'topic',
            'method'        => 'cooperators',
            'api_attribute' => $topicKey,
            'params' => [
                'userKey' => $request->input('userKey'),
                'permission' => $request->input('permission')
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_cooperator_permission"));
        }
        return $response->json();
    }

    public static function getEntityManagers(){
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'entity',
            'method'        => 'managersList',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_entity_users"));
        }
        return $response->json();
    }

    public static function setEntity($data){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'entity',
            'params' => $data
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_store_entity"));
        }
        return $response->json();
    }
    public static function deleteEntity($entityKey){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'entity',
            'attribute' => $entityKey
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_deleting_entity"));
        }
        return $response->json();
    }

    public static function setEntityLanguage($languageId, $entityKey){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'entity',
            'method' => 'addLanguage',
            'params' => [
                'language_id' => $languageId,
                'entity_key' => $entityKey,
                'default' => 1
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_store_entity_language"));
        }
        return $response->json();
    }

    public static function createEntityUser($user, $entity){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'user',
            'params'    => [
                'user_key'  => $user->user_key,
                'entity_key' => $entity
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_create_entity_user"));
        }
        return $response->json();
    }

    public static function storeBudget($request){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'budget',
            'params'    => $request->all()
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_create_budget"));
        }

        return $response->json();
    }

    public static function getBudget($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'budget',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_budget"));
        }
        return $response->json();
    }

    public static function updateBudget($name, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'budget',
            'params'    => [
                'name' => $name,
                'mp_id' => 1
            ],
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_budget"));
        }
        return $response->json();
    }

    public static function deleteBudget($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'budget',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_budget"));
        }
        return $response->json();
    }

    public static function listBudgets(){
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'budget',
            'method'        => 'list',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_list_budget"));
        }
        return json_decode($response->content());
    }


    /*** CATEGORY ***/
    public static function storeCategory($request){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'category',
            'params'    => $request->all()
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_create_category"));
        }
        return $response->json();
    }

    public static function getCategory($key){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'category',
            'attribute' => $key
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_category"));
        }
        return $response->json();
    }

    public static function updateCategory($request, $key){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'category',
            'params'    => $request->all(),
            'attribute' => $key
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_category"));
        }
        return $response->json();
    }

    public static function deleteCategory($key){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'category',
            'attribute' => $key,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_category"));
        }
        return $response->json();
    }

    public static function listCategories(){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'category',
            'method'    => 'list'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_list_category"));
        }
        return json_decode($response->content());
    }
    /*** END CATEGORY ***/

    public static function setPage($content, $request){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'page',
            'params'    => [
                'page_key' => $content->content_key,
                'type' => $request->type,
            ]
        ]);


        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_store_page"));
        }
        return $response->json();
    }

    public static function setCountry($params){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'country',
            'params'    => $params
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_store_country"));
        }
        return $response->json();
    }

    public static function getCountry($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'country',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_get_country"));
        }
        return $response->json();
    }

    public static function updateCountry($params, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'country',
            'params'    => $params,
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_country"));
        }
        return $response->json();
    }

    public static function deleteCountry($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'country',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_country"));
        }
        return $response->json();
    }


    public static function setCurrency($params){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'currency',
            'params'    => $params
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_currency"));
        }
        return $response->json();
    }

    public static function getCurrency($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'currency',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_currency"));
        }
        return $response->json();
    }

    public static function updateCurrency($params, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'currency',
            'params'    => $params,
            'attribute'     => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_currency"));
        }
        return $response->json();
    }

    public static function deleteCurrency($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'currency',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_currency"));
        }
        return $response->json();
    }

    public static function setLanguage($languageId, $entityKey, $default){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'entity',
            'method' => 'addLanguage',
            'params' => [
                'language_id' => $languageId,
                'entity_key' => $entityKey,
                'default' => $default
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_set_language"));
        }
        return $response->json();
    }

    public static function updateEntity($request, $entityKey){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'entity',
            'params' => $request->all(),
            'attribute' => $entityKey
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_entity"));
        }
        return $response->json();
    }

    public static function updateDefaultLanguage($entityKey, $languageId, $default){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'entity',
            'method' => 'defaultLanguage',
            'params' => [
                'entity_key' => $entityKey,
                'language_id' => $languageId,
                'default' => $default
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_default_language"));
        }
        return $response->json();
    }

    public static function setEntityUser($userKey, $entityKey, $role){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'user',
            'params' => [
                'user_key' => $userKey,
                'role' => $role,
                'entity_key' => $entityKey
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_entity_manager"));
        }
        return $response->json();
    }

    public static function setUser($userKey, $role){
        $response = ONE::post([
            'component' => 'empatia',
            'api' => 'user',
            'params' => [
                'user_key' => $userKey,
                'role' => $role
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_manager"));
        }

        return $response->json();
    }

    public static function updateUserRole($userKey, $entityKey, $role){
        $response = ONE::put([
            'component' => 'empatia',
            'api' => 'user',
            'attribute' => $userKey,
            'params' => [
                'role' =>$role,
                'entity_key' => $entityKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_user_role"));
        }

        return $response->json();
    }

    public static function setGeoArea($params){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'geoarea',
            'params'    => $params
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_geoarea"));
        }

        return $response->json();
    }

    public static function getGeoArea($key){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'geoarea',
            'attribute' => $key
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_geoarea"));
        }

        return $response->json();
    }

    public static function updateGeoArea($key, $name){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'geoarea',
            'params'    => [
                'name' => $name
            ],
            'attribute' => $key
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_geoarea"));
        }

        return $response->json();
    }

    public static function deleteGeoArea($key){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'geoarea',
            'attribute' => $key
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_geoarea"));
        }

        return $response->json();
    }

    public static function listGeoArea(){
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'geoarea',
            'method'        => 'list',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_geoarea"));
        }

        return $response->json();
    }

    public static function getProposals($kioskKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'api_attribute' => $kioskKey,
            'method'    => 'proposals',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_proposals"));
        }

        return $response->json()->data;
    }

    public static function getKioskType($kioskTypeId){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'kiosktype',
            'attribute' => $kioskTypeId,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_kiosk_type"));
        }

        return $response->json();
    }

    public static function getIdea($cbId){
        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'idea',
            'attribute' => $cbId,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_idea"));
        }

        return $response->json();
    }

    public static function setProposal($proposalKey, $kioskKey, $position){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'method'    => 'addProposal',
            'params'    => [
                'proposal_key' => $proposalKey,
                'kiosk_key' => $kioskKey,
                'position' => $position
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_set_proposal"));
        }

        return $response->json();
    }

    public static function deleteProposal($kioskKey, $id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'api_attribute' => $kioskKey,
            'method'    => 'destroyProposal',
            'attribute'    => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_proposal"));
        }

        return $response->json();
    }

    public static function updateProposalOrder($kioskKey, $ordering){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'api_attribute' => $kioskKey,
            'method'    => 'proposalsReorder',
            'params'    => [
                'positions' => $ordering
            ],
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_proposal_order"));
        }

        return $response->json();
    }

    public static function storeProposals($kioskKey, $data){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'kiosk',
            'api_attribute' => $kioskKey,
            'method'    => 'proposals/store',
            'params'    => ['proposals' => $data ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_store_proposals"));
        }

        return $response->json();
    }

    public static function storeLanguage($params){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'lang',
            'params'    => $params
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_store_language"));
        }

        return $response->json();
    }

    public static function getLanguage($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'lang',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_store_language"));
        }

        return $response->json();
    }

    public static function updateLanguage($params, $id){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'lang',
            'params'    => $params,
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_language"));
        }

        return $response->json();
    }

    public static function deleteLang($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'lang',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_language"));
        }

        return $response->json();
    }

    public static function setNewsletterSubscription($email, $active){
        $response = One::post([
                'component' => 'empatia',
                'api'       => 'newsletterSubscription',
                'params'    => [
                    'email' => $email,
                    'active'=> $active
                ]
            ]
        );

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_newsletter_subscription"));
        }

        return $response->json();
    }

    public static function setPhase($params){
        $response = ONE::post([
            'component' => 'orchestrator',
            'api'       => 'phase',
            'params'    => $params
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_phase"));
        }

        return $response->json();
    }

    public static function getPhase($id){
        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'phase',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_phase"));
        }

        return $response->json();
    }

    public static function updatePhase($name, $mpId, $id){
        $response = ONE::put([
            'component' => 'orchestrator',
            'api'       => 'phase',
            'params'    => [
                'name' => $name,
                'mp_id' => $mpId
            ],
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_phase"));
        }

        return $response->json();
    }

    public static function deletePhase($id){
        $response = ONE::delete([
            'component' => 'orchestrator',
            'api'       => 'phase',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_phase"));
        }

        return $response->json();
    }

    public static function storePermissions($roleKey, $code, $module, $api, $option, $value){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'permissions',
            'method'    => 'addPermission',
            'params'    => [
                'role_key' => $roleKey,
                'code' => $code,
                'module' => $module,
                'api' => $api,
                $option => $value
            ]
        ]);
        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_store_permissions"));
        }

        return $response->json();
    }

    public static function listSiteConf($groupKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'SiteConf',
            'method' => 'list',
            'attribute' => $groupKey,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_list_site_conf"));
        }

        return $response->json()->data;
    }

    public static function updateSiteConfValues($array, $siteKey){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'siteConfValues',
            'method'    => 'updateValues',
            'params'    => [
                'configurations' => $array,
                'siteKey' => $siteKey
            ]
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_list_site_conf"));
        }

        return $response->json();
    }

    public static function setTag($params){
        $response = ONE::post([
            'component' => 'orchestrator',
            'api'       => 'tag',
            'params'    => $params
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_tag"));
        }

        return $response->json();
    }

    public static function getTag($id){
        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'tag',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_tag"));
        }

        return $response->json();
    }

    public static function updateTag($id, $name, $entityId){
        $response = ONE::put([
            'component' => 'orchestrator',
            'api'       => 'tag',
            'params'    => [
                'name' => $name,
                'entity_id' => $entityId
            ],
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_tag"));
        }

        return $response->json();
    }

    public static function deleteTag($id){
        $response = ONE::delete([
            'component' => 'orchestrator',
            'api'       => 'tag',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_tag"));
        }

        return $response->json();
    }

    public static function listTag(){
        $response = ONE::get([
            'component'     => 'orchestrator',
            'api'           => 'tag',
            'method'        => 'list',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_list_tag"));
        }

        return $response->json();
    }

    public static function setTimezone($params){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'tz',
            'params'    => $params
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_timezone"));
        }

        return $response->json();
    }

    public static function getTimezone($id){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'tz',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_timezone"));
        }

        return $response->json();
    }

    public static function updateTimezone($id, $params){
        $response = ONE::put([
            'component' => 'empatia',
            'api'       => 'tz',
            'params'    => $params,
            'attribute'     => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_timezone"));
        }

        return $response->json();
    }

    public static function deleteTimezone($id){
        $response = ONE::delete([
            'component' => 'empatia',
            'api'       => 'tz',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_timezone"));
        }

        return $response->json();
    }

    public static function getEntityData($entity){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'entity',
            'method'    => $entity
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_entity_data"));
        }

        return $response->json();
    }

    public static function listEntityRoles($userKey){
        $response = ONE::get([
            'component' => 'empatia',
            'api'       => 'user',
            'api_attribute'    => $userKey,
            'method'            => 'listEntityRoles'
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_list_entity_roles"));
        }

        return $response->json()->data;
    }

    public static function setEntityRole($entityKey){
        $response = ONE::post([
            'component' => 'empatia',
            'api'       => 'user',
            'method'    => 'list',
            'params'    => [
                'role' => 'user',
                'entity_key' => $entityKey
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_list_entity_roles"));
        }

        return $response->json()->data;
    }

    public static function setZone($params){
        $response = ONE::post([
            'component' => 'orchestrator',
            'api'       => 'zone',
            'params'    => $params
        ]);

        if($response->statusCode()!= 201) {
            throw new Exception(trans("comModulesOrchestrator.error_set_zone"));
        }

        return $response->json();
    }

    public static function getZone($id){
        $response = ONE::get([
            'component' => 'orchestrator',
            'api'       => 'zone',
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_zone"));
        }

        return $response->json();
    }

    public static function updateZone($id, $params){
        $response = ONE::put([
            'component' => 'orchestrator',
            'api'       => 'zone',
            'params'    => $params,
            'attribute' => $id
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_update_zone"));
        }

        return $response->json();
    }

    public static function deleteZone($id){
        $response = ONE::delete([
            'component' => 'orchestrator',
            'api'       => 'zone',
            'attribute' => $id,
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_delete_zone"));
        }

        return $response->json();
    }

    public static function listZones(){
        $response = ONE::get([
            'component'     => 'orchestrator',
            'api'           => 'zone',
            'method'        => 'list',
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_list_zone"));
        }

        return $response->json();
    }

    public static function getAccessMenuInfo()
    {
        $response = ONE::get([
            'component' => 'empatia',
            'api' => 'accessmenu',
            'method' => 'info'
        ]);

        if ($response->statusCode() != 200) {
            throw new Exception(trans("comModulesOrchestrator.error_get_access_menu_info"));
        }

        return $response->json();

    }

    public static function getEntityUsers($request) {
        $response = ONE::get([
            'component'     => 'empatia',
            'api'           => 'entity',
            'method'        => 'userList',
            'params' => [
                'tableData' => One::tableData($request),
                'topicKey'  => $request->input('topicKey') ?? null
            ]
        ]);

        if($response->statusCode()!= 200) {
            throw new Exception(trans("comModulesOrchestrator.error_getting_entity_users"));
        }
        return $response->json();

    }
}
