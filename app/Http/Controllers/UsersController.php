<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\Notify;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use App\ComModules\Vote;
use Exception;
use App\Http\Requests\UserRequest;
use App\One\One;
use Datatables;
use Faker\Factory as Faker;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use Symfony\Component\Routing\Loader\ObjectRouteLoader;
use View;
use URL;
use PDF;
use App\ComModules\EMPATIA;
use Carbon\Carbon;
use Chumper\Zipper\Zipper;


class UsersController extends Controller
{
    private $rolesType;
    public function __construct()
    {
        /** @var TYPE_NAME $this */

        if(ONE::isEntity()){
            $this->rolesType = ['manager' => 'Manager', 'user' => 'User'];

        }else{
            $this->rolesType = ['admin' => 'Admin', 'manager' => 'Manager', 'user' => 'User'];
        }

        View::share('title', trans('users.title'));


    }

    //TODO: Add extra parameters in request

    //Login, name, email, password

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     * @internal param string $role
     */
    public function index(Request $request)
    {
        $role = null;
        if(isset($request->role)){
            $role = $request->role;
        }

        $roles = $this->rolesType;

        if($role == 'admin') {
            if (isset($roles['manager'])) {
                unset($roles['manager']);
            }
            if (isset($roles['user'])) {
                unset($roles['user']);
            }
        } else {
            if (isset($roles['admin'])) {
                unset($roles['admin']);
            }
        }

        $title = trans('privateUsers.users');
        return view("private.user.index", compact('title','role', 'roles'));
    }

    public function getSidebar1(Request $request)
    {
        $active = $request->url;
        return view('private.sidebar.registration', compact('active'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function indexCompleted()
    {
        $sites = Orchestrator::getSiteList();

        $title = trans('privateUsers.list_user_completed');
        //$sidebar = 'registration';
        $active = 'confirm';

        Session::put('sidebarArguments', ['activeFirstMenu' => 'confirm']);
        return view("private.user.indexCompleted", compact('title', 'sites', 'sidebar', 'active'));
    }


    /**
     * Display a register form.
     *
     * @return View
     */
    public function register()
    {
        return view('users.register');
    }

    /**
     * Update a User Role.
     *
     * @return View
     */
    public function updateRole($userkey, $entityId,Request $request)
    {

        //ir ao orchestrator dizer que o user passou a manager
        try{
            Orchestrator::updateUser($entityId, $userkey, 'manager');
            Session::flash('message', trans('user.updateRole_ok'));
            return redirect()->action('EntitiesController@showManagers', $entityId);
        }
        catch (Exception $e) {
            return redirect()->back()->withErrors(["user.updateRole_fail" => $e->getMessage()]);
        }
    }

    public function updateRoleMan($userkey, $entityId,Request $request)
    {

        //ir ao orchestrator dizer que o user passou a manager
        try{
            Orchestrator::updateUser($entityId, $userkey, 'manager');
            Session::flash('message', trans('user.updateRole_ok'));
            return redirect()->action('EntitiesDividedController@showManagers');

        }
        catch (Exception $e) {
            return redirect()->back()->withErrors(["user.updateRole_fail" => $e->getMessage()]);
        }
    }


    /**
     *  a new resource.
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request)
    {
        if(Session::get('user_role') != 'admin' ) {
            return redirect()->back()->withErrors(["user.create" => trans('user.invalidPermission')]);
        }

        try{
            $role = 'admin';
            if(isset($request->role)){
                $role = $request->role;
            }
            if($role == 'manager' || $role == 'admin'){
                $inputRole = 'manager';
                return view('private.user.manager', compact('role', 'inputRole'));
            }
            $roles = $this->rolesType;
            //* Get list of entities */
            $entities = [];
            $object = Orchestrator::getEntities();

            foreach($object as $entity){
                $entities[$entity->entity_key] = $entity->name;
            }
            $title = trans('privateUsers.create_user');

            // User Parameters
            $registerParametersResponse = Orchestrator::getEntityRegisterParameters();

            //verify user parameters with responses
            $registerParameters = [];
            foreach ($registerParametersResponse as $parameter){
                $parameterOptions = [];
                $value = '';
                $file = null;
                if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown') {
                    foreach ($parameter->parameter_user_options as $option) {
                        $parameterOptions [] = [
                            'parameter_user_option_key' => $option->parameter_user_option_key,
                            'name' => $option->name
                        ];
                    }
                }
                $registerParameters []= [
                    'parameter_user_type_key'   => $parameter->parameter_user_type_key,
                    'parameter_type_code'       => $parameter->parameter_type->code,
                    'name'                      => $parameter->name,
                    'mandatory'                 => $parameter->mandatory,
                    'parameter_user_options'    => $parameterOptions
                ];
            }

            return view('private.user.user', compact('title', 'roles', 'entities', 'registerParameters','role'));

        }
        catch (Exception $e) {
            return redirect()->back()->withErrors(["user.updateRole_fail" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param $userKey
     * @return View
     */
    public function edit(Request $request, $userKey)
    {
        try {
            //role
            $inputRole = $request->role;
            $roles = $this->rolesType;
            if (isset($roles['admin'])) {
                unset($roles['admin']);
            }

            // User
            $user = Auth::getUserByKey($userKey);

            //get all site login levels
            $siteKey = $request->header('X-SITE-KEY') ?? null;
            $siteLoginLevels = Orchestrator::getLoginLevels($siteKey);

            $levels = [];
            foreach ($siteLoginLevels as $level) {
                $levels[$level->position] = $level->name;
            }

            //Get User Login Levels
            $entityKey = Session::get('X-ENTITY-KEY');
            if ($entityKey){
                $user->login_levels = Orchestrator::getUserLoginLevels($userKey) ? Orchestrator::getUserLoginLevels($userKey) : null;
                $loginLevels = Orchestrator::getAllEntityLoginLevels($entityKey) ? Orchestrator::getAllEntityLoginLevels($entityKey): null;
            }

            //Get user level (Site Login Levels)
            $userLevel = Orchestrator::getUserLevel($user->user_key);
            $user->user_level = isset($userLevel) ? $userLevel : null;

            $userOrchestrator = Orchestrator::getUserByKey($userKey);

            if ($userOrchestrator->admin == 1) {
                $role = 'admin';
            } else    {
                $role = $userOrchestrator->role ?? "";
            }

            if (Session::get('user_role') != 'admin'){
                return redirect()->back()->withErrors(["user.edit" => trans('user.invalidPermission')]);
            }

            if (ONE::isEntity()) {
                $object = $userOrchestrator->entities;
            } else {
                $object = Orchestrator::getEntities();
            }

            $entities = [];
            $entity = '';

            foreach ($object as $obj) {
                $entity = $obj->entity_key;
                $entities[$obj->entity_key] = $obj->name;
            }

            if(ONE::isEntity()){
                //TODO:change method when new version level is finished, maybe when get user return if he need to be moderated

                if ($role != 'admin') {
                    $userOrchestrator = Orchestrator::getUserByKey($userKey);
                    $user->moderated = $userOrchestrator->moderated;
                    if ($user->moderated == false) {
                        $user->moderation_site_key = $userOrchestrator->moderation_site_key;
                    }
                }

                // User Parameters
                $userParametersResponse = json_decode(json_encode($user->user_parameters), true);
                $registerParametersResponse = Orchestrator::getEntityRegisterParameters();

                //verify user parameters with responses
                $registerParameters = [];
                foreach ($registerParametersResponse as $parameter) {
                    $parameterOptions = [];
                    $value = '';
                    $file = null;
                    if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown') {
                        foreach ($parameter->parameter_user_options as $option) {
                            $selected = false;
                            foreach ($userParametersResponse as $userParameter) {
                                if ($userParameter["parameter_user_key"] == $parameter->parameter_user_type_key && $userParameter['value'] == $option->parameter_user_option_key)
                                    $selected = true;
                            }
                            $parameterOptions [] = [
                                'parameter_user_option_key' => $option->parameter_user_option_key,
                                'name' => $option->name,
                                'selected' => $selected
                            ];
                        }
                    }elseif($parameter->parameter_type->code == 'file'){
                        $id = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                        if ($id != '') {
                            $file = json_decode(json_encode(Files::getFile($id)), true);
                        }

                    } else {
                        $value = collect($userParametersResponse)->where("parameter_user_key",$parameter->parameter_user_type_key)->first()["value"]??"";
                    }
                    $registerParameters [] = [
                        'parameter_user_type_key' => $parameter->parameter_user_type_key,
                        'parameter_type_code' => $parameter->parameter_type->code,
                        'name' => $parameter->name,
                        'value' => isset($file) ? $file : $value,
                        'mandatory' => $parameter->mandatory,
                        'parameter_user_options' => $parameterOptions
                    ];
                }
            }

            $loginStatus = Orchestrator::getAllLoginStatus();
            $allStatus = [];
            foreach ($loginStatus as $state) {
                $allStatus[$state->code] = $state->code;
            }

            $userStatus = Orchestrator::getUserByKey($userKey);
            
            $status = [];
            
            if(!empty($userStatus->entities)){
                foreach ($userStatus->entities as $item) {

                    $status[$item->pivot->status] = !empty($item->pivot->status) ? $item->pivot->status : null;

                }
            }

            $title = trans('privateUsers.edit_user') . ' ' . (isset($user->name) ? $user->name : null);

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['status'] = $status ?? null;
            $data['user'] = $user;
            $data['allStatus'] = $allStatus;
            $data['entities'] = $entities ?? null;
            $data['entity'] = $entity ?? null;
            $data['roles'] = $roles;
            $data['role'] = $role;
            $data['inputRole'] = $inputRole;
            $data['registerParameters'] = $registerParameters ?? null;
            $data['levels'] = $levels;
            $data['loginLevels'] = $loginLevels ?? null;
            $data['sidebar'] = 'registration';
            $data['active'] = 'confirm';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'personRegistration']);

            if ($role == 'admin') {
                return view('private.user.manager', $data);
            }

            return view('private.user.user', $data);

            return redirect()->back()->withErrors(["private.user.edit" => $response->json()->error]);

        }
        catch(Exception $e) {

            return redirect()->back()->withErrors(["private.user.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $userKey
     * @return View
     */
    public function show(Request $request,$userKey)
    {
        try {
            $data = [];

            $roles = $this->rolesType;

            //role
            $inputRole = $request->role;

            // User
            if(Session::has('SITE-CONFIGURATION.sms_max_send'))
                $user = Auth::getUserByKey($userKey, true);
            else
                $user = Auth::getUserByKey($userKey);

            //get all site login levels
            $siteKey = Session::get('X-SITE-KEY') ?? null;
            $siteLoginLevels = Orchestrator::getLoginLevels($siteKey);

            $levels = [];
            foreach ($siteLoginLevels as $level){
                $levels[$level->position] = $level->name;
            }

            //Get user level
            $userLevel = Orchestrator::getUserLevel($user->user_key);
            $userLoginLevels = Orchestrator::getUserLoginLevels($user->user_key);

            $user->user_level = isset($userLevel) ? $userLevel : null;
            $userOrchestrator = Orchestrator::getUserByKey($userKey);
            $role = '';
            if($userOrchestrator->admin == 1){
                $role = 'admin';
            }else{
                $role = $userOrchestrator->role ?? "";
            }
            
            if(ONE::isEntity()){
            //TODO:change method when new version level is finished, maybe when get user return if he need to be moderated
                if ($inputRole != 'admin' && !is_null($siteKey)){

                    $user->moderated = $userOrchestrator->moderated;
                    if ($user->moderated == false) {
                        $user->moderation_site_key = $userOrchestrator->moderation_site_key;
                    }

                    $usersToModerate = Orchestrator::getManualLoginLevelUsers();

                    // Entity and Roles
                    $entities = [];
                    $userObj = Orchestrator::getUserByKey($userKey);

                    if(isset($userObj->entities)){
                        foreach($userObj->entities as $item){
                            $status = !empty($item->pivot->status) ? $item->pivot->status : null;;
                        }
                    }

                    if(ONE::isEntity()){
                        $entities = $userObj->entities;
                    }

                    // User Parameters
                    $userParametersResponse = json_decode(json_encode($user->user_parameters),true);
                    $registerParametersResponse = Orchestrator::getEntityRegisterParameters();

                    //verify user parameters with responses
                    $registerParameters = [];
                    foreach ($registerParametersResponse as $parameter){
                        $parameterOptions = [];
                        $value = '';
                        $file = null;
                        if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown') {
                            foreach ($parameter->parameter_user_options as $option) {
                                $selected = false;
                                foreach ($userParametersResponse as $userParameter) {
                                    if ($userParameter["parameter_user_key"] == $parameter->parameter_user_type_key && $userParameter['value'] == $option->parameter_user_option_key)
                                        $selected = true;
                                }
                                $parameterOptions [] = [
                                    'parameter_user_option_key' => $option->parameter_user_option_key,
                                    'name' => $option->name,
                                    'selected' => $selected
                                ];
                            }
                        }elseif($parameter->parameter_type->code == 'file'){
                            $id = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                            if($id != ''){
                                $file = json_decode(json_encode(Files::getFile($id)),true);
                            }

                        }else{
                            $value = collect($userParametersResponse)->where("parameter_user_key",$parameter->parameter_user_type_key)->first()["value"]??"";
                        }
                        $registerParameters []= [
                            'parameter_user_type_key'   => $parameter->parameter_user_type_key,
                            'parameter_type_code'       => $parameter->parameter_type->code,
                            'name'                      => $parameter->name,
                            'value'                     => isset($file) ? $file : $value,
                            'mandatory'                 => $parameter->mandatory,
                            'parameter_user_options'    => $parameterOptions
                        ];
                    }
                    $data['entities'] = $entities;
                    $data['registerParameters'] = $registerParameters;
                    $data['hasLoginLevels'] = !empty(Orchestrator::getAllEntityLoginLevels(ONE::getEntityKey()));
                    $userMessages = Orchestrator::getMessagesFromUser($userKey);

                    if($userMessages) {
                        $data['user_messages'] = count(collect($userMessages)->where('viewed', false)->where('to',ONE::getEntityKey()));
                    }
                }
            }

            // Check if registration is completed
            $userValidate = Orchestrator::getUserAuthValidate();
            $registerCompleted = true;
            if(!empty($userValidate->status) && $userValidate->status == 'registered'){
                $registerCompleted = false;
            }

            // Form title (layout)
            $title = trans('privateUsers.userDetails');

            // Return the view with data
            $data['title'] = $title;
            $data['user'] = $user;
            $data['userKey'] = $user->user_key;
            $data['status'] = $status ?? null;

            $data['roles'] = $roles;
            $data['role'] = $role;
            $data['inputRole'] = $inputRole;
            $data['registerCompleted'] = $registerCompleted;
            $data['moderation'] = $request->moderation ?? false;
            $data['levels'] = $levels;
            $data['sidebar'] = 'manager';
            $data['active'] = 'details';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'personRegistration']);

//            Todo: the user and manager views should probably be the same
            if($role == 'admin' || $role == 'manager'){
                $data['userKey'] = $user->user_key;
                $data['sidebar'] = 'manager';
                $data['active'] = 'details';
                Session::put('sidebarArguments', ['userKey' => $user->user_key, 'role' => $role, 'activeFirstMenu' => 'details']);
                return view('private.user.manager', $data);
            }

            return view('private.user.user', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["private.user.show" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $requestUser
     * @param $userKey
     * @return $this|View
     */
    public function update(UserRequest $requestUser, $userKey)
    {
        $inputRole = $requestUser->role ?? null;
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["user.update" => trans('user.invalidPermission')]);
        }

        try {
            if(!empty($requestUser->password) && strlen($requestUser->password ) <= 2 )
                return redirect()->back()->withErrors([trans('privateUsers.profileUpdatedNOK') => trans('privateUsers.passwordMin3Chars')]);

            $userDetails = $requestUser->all();

            $data['name'] = $requestUser->name;
            $data['email'] = $requestUser->email;
            $data['password'] = isset($requestUser->password) ? $requestUser->password : null;
            $data['identity_card'] = isset($requestUser->identity_card) ? $requestUser->identity_card : null;
            $data['vat_number'] = isset($requestUser->vat_number) ? $requestUser->vat_number : null;

            $data['sidebar'] = 'manager';
            $data['active'] = 'details';

            $role = $requestUser->role;
            unset($userDetails['_token']);
            unset($userDetails['form_name']);
            unset($userDetails['_method']);
            unset($userDetails['name']);
            unset($userDetails['email']);
            unset($userDetails['identity_card']);
            unset($userDetails['role']);
            if(isset($userDetails['password_confirmation'])){
                unset($userDetails['password_confirmation']);
            }
            if(isset($userDetails['password'])){
                unset($userDetails['password']);
            }
            if(isset($userDetails['vat_number'])){
                unset($userDetails['vat_number']);
            }

            $userLevel = null;
            if(isset($userDetails['user_level'])){
                $userLevel = $userDetails['user_level'];
                unset($userDetails['user_level']);
            }

            $user = Auth::updateUser($userKey,$data,$userDetails);

            $userValidate = Orchestrator::getUserAuthValidate();

            if(!empty($userValidate->status) && $userValidate->status == 'registered'){
                $userStatus = Orchestrator::updateUserStatus('authorized',$userKey);
            }

            //* Send to Orchestrator the user type and key*/
            $response = Orchestrator::updateUser($requestUser->entityKey, $userKey, $requestUser->role);

            //TODO: status is going to be substituted by levels
            if(isset($requestUser->status)){
                Orchestrator::updatedUserStatus($requestUser->all()['status'], $userKey);
            }

            /** @deprecated - user level - to remove */
            if(!is_null($userLevel)){
                if (empty($userLevel)){
                    $userLevel = '0';
                }
                Orchestrator::setUserLevel($user->user_key, $userLevel);
            }

            /** Check and update user Login Level*/
            $userLoginLevels = Orchestrator::checkAndUpdateUserLoginLevel($userKey);

            if (isset($user->new_email) && ($user->new_email == 1)){
                $emailType = 'registry_confirmation';
                $tags = [
                    "name" => $user->name,
                    "link" => URL::action('AuthController@confirmEmail', $user->confirmation_code)
                ];
                Notify::sendEmail($emailType, $tags, (array) $user);
            }

            Session::flash('message', trans('privateUsers.update_ok'));
            return redirect()->action('UsersController@show', ['userKey' => $userKey,'role' => $inputRole]);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["user.update" => $e->getMessage()]);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $requestUser
     * @return $this|View
     */
    public function store(UserRequest $requestUser, $internalCall = false)
    {
        try {
            $role = 'user';
            if(isset($requestUser->role)){
                $role = $requestUser->role;
                if(Session::get('user_role') != 'admin'){
                    if ($internalCall)
                        return ["error" => trans('privateEntitiesDivided.permission_message')];
                    else
                        return redirect()->back()->withErrors(["user.create" => trans('privateEntitiesDivided.permission_message')]);
                }
            }

            if(!empty($requestUser->password) && strlen($requestUser->password ) <= 2 ) {
                if ($internalCall)
                    return ["error" => trans('privateUsers.profile_updated_nok')];
                else
                    return redirect()->back()->withErrors([trans('privateUsers.profile_updated_nok') => trans('privateUsers.password_min_3_chars')]);
            }

            $userDetails = $requestUser->all();
            $data['name'] = $requestUser->name;
            $data['email'] = $requestUser->email;
            $data['password'] = isset($requestUser->password) ? $requestUser->password : null;
            $data['identity_card'] = isset($requestUser->identity_card) ? $requestUser->identity_card : null;
            $data['vat_number'] = isset($requestUser->vat_number) ? $requestUser->vat_number : null;
            unset($userDetails['_token']);
            unset($userDetails['form_name']);
            unset($userDetails['_method']);
            unset($userDetails['name']);
            unset($userDetails['email']);
            unset($userDetails['identity_card']);
            if(isset($userDetails['password_confirmation'])){
                unset($userDetails['password_confirmation']);
            }
            if(isset($userDetails['password'])){
                unset($userDetails['password']);
            }
            if(isset($userDetails['vat_number'])){
                unset($userDetails['vat_number']);
            }
            $userData = Auth::storeNewUser($data,$userDetails);
            $user = $userData->user;

            //* Send to Orchestrator the user type and key*/
            if(isset($requestUser->entityKey)) {
                $entityKey = $requestUser->entityKey;
                $response = Orchestrator::setEntityUser( $user->user_key, $entityKey, $requestUser->role);
            } else {
                $response = Orchestrator::setUser($user->user_key, $requestUser->role);
            }

            // Change status to authorized
            if($requestUser->role != 'admin')
                Orchestrator::updateUserStatus('authorized' ,$user->user_key);

            if ($internalCall) {
                return ["success" => $user->user_key];
            } else {
                Session::flash('message', trans('privateUsers.store_ok'));
                return redirect()->action('UsersController@show', ['userKey' => $user->user_key, 'role' => $role]);
            }
        }
        catch(Exception $e) {
            if ($internalCall)
                return ["error" => $e->getMessage()];
            else
                return redirect()->back()->withErrors(["user.store" => $e->getMessage()]);
        }
    }



    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function showProfile()
    {
        $roles = $this->rolesType;

        try {
            $title = trans('privateUsers.your_profile');

            $user = Auth::getUser();

            // Entity
            $response = Orchestrator::getUserByKey($user->user_key);

            if($response->admin){
                $role = 'admin';
                $entity = null;
            }else{
                if (isset($response->entity_key)){
                    $entity = $response->entity_key;
                } else {
                    if (Session::has('X-ENTITY-KEY')){
                        $entityKey = Session::get('X-ENTITY-KEY');

                        if(collect($response->entities)->where('entity_key', $entityKey)->count() > 0){
                            $entity = $entityKey;
                        }
                    }
                }
                $role = $response->role;
            }

            // Entities
            $entities = [];
            if(!empty($entity)){
                $response = Orchestrator::getEntityData($entity);
            }

            $uploadKey = Files::getUploadKey();

            if($response){
                $entityName = !empty($response->name) ? $response->name : "";
            }
            return view('private.user.profile', compact('user', 'entities', 'entityName', 'roles', 'role', 'title', 'uploadKey'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["private.user.show" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function editProfile()
    {
        $roles = $this->rolesType;

        try {
            $title = trans('privateUsers.your_profile');
            $user = Auth::getUser();

            // Entity
            $response = Orchestrator::getUserByKey($user->user_key);

            if($response->admin){
                $role = 'admin';
                $entity = null;
            }else{
                $entity = $response->entity_key;
                $role = $response->role;
            }


            // Entities
            $entities = [];
            if(!empty($entity)){
                $response = Orchestrator::getEntityData($entity);
            }

            if($response){
                $entityName = $response->name;
            }

            return view('private.user.profile', compact('user', 'entities', 'entityName', 'roles', 'role', 'title'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["private.user.show" => $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $userKey
     * @return Response
     */
    public function destroy(Request $request,$userKey)
    {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["user.create" => trans('privateEntitiesDivided.permission_message')]);
        }

        try {
            $role = $request->role;
            Orchestrator::deleteUser($userKey);

            Session::flash('message', trans('privateUsers.delete_ok'));
            return action('UsersController@index',['role' => $role]);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateUsers.delete_nok') => $e->getMessage()]);
        }
    }


    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $id
     * @return View
     */
    public function delete(Request $request,$userKey){
        $role = $request->role;
        $data = array();

        $data['action'] = action("UsersController@destroy", ['userKey' => $userKey, 'role' => $role]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this User?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }


    /**
     * List all resources from storage.
     *
     * @param Request $request
     * @return mixed
     */
    public function tableUsers(Request $request)
    {
        $role = $request->role;

        $anonymized = !empty($request->get("anonymized"));

        $response = Auth::getUserList($request, $anonymized);
        $data = $response->data;
        $recordsTotal = $response->recordsTotal;
        $recordsFiltered = $response->recordsFiltered;

//        if(ONE::verifyUserPermissions('auth', 'user', 'show') and ONE::verifyUserPermissions('auth', 'manager', 'show'))
            $usersKeys = collect($data)->pluck('user_key');
//        elseif(ONE::verifyUserPermissions('auth', 'user', 'show') and !ONE::verifyUserPermissions('auth', 'manager', 'show'))
//            $usersKeys = collect($data)->where('pivot.role', 'user')->pluck('user_key');
//        elseif(!ONE::verifyUserPermissions('auth', 'user', 'show') and ONE::verifyUserPermissions('auth', 'manager', 'show'))
//            $usersKeys = collect($data)->where('pivot.role', 'manager')->pluck('user_key');
//        else
//            $usersKeys = [];

        //TODO:change method when new version level is finished, maybe when get user return if he need to be moderated
        if ($role == 'user') {
            $usersToModerate = Orchestrator::getUsersToModerate();
        } else {
            $usersToModerate = [];
        }
        $collection = Collection::make($data);

        $editUser = true;
        $deleteUser = true;
        $editManager = true;
        $deleteManager = true;

        if ($anonymized)
            $deleteManager = false;

        // in case of json
        $dataTable = Datatables::of($collection)
            ->editColumn('select_users', function ($user) use ($anonymized) {
                return '<input class="user_key" type="checkbox" value="'.$user->user_key.'" id="'.$user->user_key.'" ' . ($anonymized?"disabled":"") .'/>';
            })
            ->editColumn('name', function ($user) use($role){
                return "<a href='".action('UsersController@show', ['userKey' => $user->user_key, 'role' => $user->role ?? null])."'>".$user->name."</a>";
            })
            ->editColumn('authorize', function ($user)  use ($usersToModerate) {

                $content = "";
                if ($user->confirmed == 0) {
                    $content .= '<span class="badge badge-info" style="display: block;margin-bottom: 10px;">' . trans("privateUsers.missing_email_confirmation") . '</span>';
                }
                if(array_key_exists($user->user_key,$usersToModerate)){
                    $content .= '<span class="badge badge-danger" style="display: block;">'.trans("privateUsers.user_need_moderation").'</span>';
                }

                return $content;
            })
            ->editColumn('role', function ($user) use($role){
                $content = "";
                if  (isset($user->role) && $user->role == 'user'){
                    $content .= '<span class="badge badge-secondary">'.trans("privateUsers.user").'</span>';
                }
                if  (isset($user->role) && $user->role == 'manager'){
                    $content .= '<span class="badge badge-primary">'.trans("privateUsers.manager").'</span>';
                }
                if  (isset($user->role) && $role == 'admin'){
                    $content .= '<span class="badge badge-danger">'.trans("privateUsers.admin").'</span>';
                }
                return $content;
            })
            ->addColumn('action', function ($user) use($role, $editUser, $deleteUser, $editManager, $deleteManager){
                if(isset($user->role) && $user->role == 'user' and $editUser == true and $deleteUser == true)
                    return ONE::actionButtons( ['userKey' => $user->user_key,'role' => $user->role], ['form' => 'users','edit' => 'UsersController@edit', 'delete' => 'UsersController@delete']);
                elseif(isset($user->role) && $user->role == 'user' and $editUser == false and $deleteUser == true)
                    return ONE::actionButtons( ['userKey' => $user->user_key,'role' => $user->role], ['form' => 'users', 'delete' => 'UsersController@delete']);
                elseif(isset($user->role) && $user->role == 'user' and $editUser == true and $deleteUser == false)
                    return ONE::actionButtons( ['userKey' => $user->user_key,'role' => $user->role], ['form' => 'users','edit' => 'UsersController@edit']);
                elseif(isset($user->role) && $user->role == 'user' and $editUser == false and $deleteUser == false)
                    return null;
                elseif(isset($user->role) && $user->role == 'manager' and $editManager == true and $deleteManager == true)
                    return ONE::actionButtons( ['userKey' => $user->user_key,'role' => $user->role], ['form' => 'users','edit' => 'UsersController@edit', 'delete' => 'UsersController@delete']);
                elseif(isset($user->role) && $user->role == 'manager' and $editUser == false and $deleteUser == true)
                    return ONE::actionButtons( ['userKey' => $user->user_key,'role' => $user->role], ['form' => 'users', 'delete' => 'UsersController@delete']);
                elseif(isset($user->role) && $user->role == 'manager' and $editManager == true and $deleteManager == false)
                    return ONE::actionButtons( ['userKey' => $user->user_key,'role' => $user->role], ['form' => 'users','edit' => 'UsersController@edit']);
                elseif(isset($user->role) && $user->role == 'manager' and $editManager == false and $deleteManager == false)
                    return null;
            })
            ->rawColumns(['select_users','name','authorize','role','action'])
            ->with('filtered', $recordsFiltered)
            ->skipPaging()
            ->setTotalRecords($recordsTotal)
            ->make(true);

        return $dataTable;
    }


    /**
     * Updates user status.
     *
     * @param $userKey
     * @param $status
     * @param $role
     * @return View
     */
    public function updateStatus($userKey, $status, $role){
        try{
            Orchestrator::updateUserStatus($status,$userKey);

            // Notification by Email
            if($status == "authorized"){
                $user = Auth::getUserByKey($userKey);

                // Email / Notify
                $emailType = 'account_authorized';
                $tags = [
                    "name" => $user->name,
                    "link" => URL::action("PublicCbsController@showCbsWithTopics")
                ];
                $response = Notify::sendEmail($emailType, $tags, (array) $user);
            }

            Session::flash('message', trans('publicUsers.userStatusUpdatedOK'));
            return action('UsersController@index',['role' => $role]);
        }
        catch (Exception $e) {
            return redirect()->back()->withErrors(["private.user.indexWithStatus" => $e->getMessage()]);
        }
    }

    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $userKey
     * @param $status
     * @param $statusList
     * @return View
     */
    public function updateStatusConfirm($userKey, $status, $role){

        $data = array();

        $data['action'] = action("UsersController@updateStatus", [$userKey, $status, $role]);
        $data['title'] = "AUTHORIZE";
        $data['msg'] = "Are you sure you want to authorize this User?";
        $data['btn_ok'] = "Authorize";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.activateModal", $data);

    }

    /**
     * List all resources from storage.
     *
     * @param Request $request
     * @return mixed
     */
    public function tableUsersCompleted(Request $request)
    {
        $siteKey = $request->input('site_key');

        //TODO:change method when new version level is finished, maybe when get user return if he need to be moderated


        //TODO: merge functions
        $status = 'authorized';
//        $data = Orchestrator::getManualLoginLevelUsers();
        $data = Auth::usersToModerate($request, $siteKey, $status);


        $manage = $data->users;
        $recordsTotal = $data->recordsTotal;
        $recordsFiltered = $data->recordsFiltered;

        $moderation = false;
        if(empty($request->home)){
            $moderation = true;
            $collection = Collection::make($manage);
        }else{
            $collection = Collection::make($manage)->take(10);
        }

        $authorize = true;

        // in case of json
        return Datatables::of($collection)
            ->editColumn('name', function ($user) use($moderation) {
                return "<a href='".action('UsersController@show', ['user_key' =>$user->user_key,'role' => 'user','moderation' => $moderation])."'>".$user->name."</a>";
            })
            ->editColumn('authorize', function ($user){
                $html = '';

                $html .= "<a href='" . action('UsersController@manualCheckUserLoginLevel', ['userKey' => $user->user_key]) . "' class='btn btn-success btn-xs btn-block'><i class='glyphicon glyphicon-thumbs-up'></i></a>";

                return $html;
//                return "<a href='javascript:oneActivate(\"".action('UsersController@moderateUser',['userKey' => $user->user_key, 'site_key' => $data->{$user->user_key}->site_key ])."\")' class='btn btn-success btn-xs'><i class='glyphicon glyphicon-thumbs-up'></i> ".trans("users.authorize")."</a>";
            })
            ->addColumn('action', function ($user) use($moderation) {
                return ONE::actionButtons(['user_key' =>$user->user_key,'role' => 'user','moderation' => $moderation], ['show' => 'UsersController@show']);
            })
            ->rawColumns(['name','authorize','action'])
            ->with('total', $recordsTotal)
            ->with('filtered', $recordsFiltered)
            ->skipPaging()
            ->make(true);
    }

    /**
     * @param Request $request
     * @param $userKey
     */
    public function manualCheckUserLoginLevel(Request $request, $userKey)
    {
        try{
            $status = Orchestrator::updateStatusCheckAndUpdateUserLoginLevel($userKey);

            Session::flash('message', trans('privateUsers.manual_check_user_login_level_success'));
            return redirect()->back();
        }catch(Exception $e){
            return redirect()->back()->withErrors([trans('privateUsers.manual_check_user_login_level_failed') => $e->getMessage()])->getTargetUrl();
        }
    }

    /**
     * List all resources from storage.
     *
     * @return mixed
     */
    public function tableUsersCompletedQuickView()
    {
        $data = Orchestrator::getUsersWithStatus("authorized");
        $usersKey = [];
        foreach ($data as $item){
            $usersKey[] = $item->user_key;
        }

        $manage = Auth::listUserConfirmed($usersKey);
        $collection = Collection::make($manage);

        // in case of json
        return Datatables::of($collection)
            ->editColumn('name', function ($user) {
                return "<a href='".action('UsersController@showReadOnly', $user->user_key)."'>".$user->name."</a>";
            })
            ->editColumn('authorize', function ($user) {
                return "<a href='javascript:oneActivate(\"".action('UsersController@updateStatusConfirmCompleted',['userKey' => $user->user_key, 'status' => 'authorized', 'redirect' => 1])."\")' class='btn btn-success btn-xs'><i class='glyphicon glyphicon-thumbs-up'></i> ".trans("users.authorize")."</a>";
            })
            ->addColumn('action', function ($user) {
                return ONE::actionButtons($user->user_key, ['show' => 'UsersController@showReadOnly']);
            })
            ->rawColumns(['name','authorize','action'])
            ->make(true);
    }

    /** Check Users manual Login Level
     * @param Request $request
     * @param $userKey
     * @param $loginLevelKey
     * @return UsersController|bool
     */
    public function manualCheckLoginLevel(Request $request, $userKey, $loginLevelKey){
        try{

            $data = Orchestrator::updateManualLoginLevelUser($userKey,$loginLevelKey);
            if($request->moderation){
                return action('UsersController@indexCompleted');
            }
            return redirect()->back()->getTargetUrl();
        }catch(Exception $e) {
            if(!empty($request->page) && $request->page == 'private_home'){
                return false;
            }
            return redirect()->back()->withErrors([trans('privateUsers.manual_login_level_failed') => $e->getMessage()])->getTargetUrl();
        }

    }

    /**
     * Updates user status.
     *
     * @param $userKey
     * @param $status
     * @return View
     */
    public function updateStatusCompleted(Request $request, $userKey, $status){
        try{

            Orchestrator::updateUserStatus($status,$userKey);

            if($status == "authorized"){
                $user = Auth::getUserByKey($userKey);

                // Email / Notify
                $emailType = 'account_authorized';
                $tags = [
                    "name" => $user->name,
                    "link" => URL::action("PublicCbsController@showCbsWithTopics")
                ];
                $response = Notify::sendEmail($emailType, $tags, (array) $user);
            }

            Session::flash('message', trans('publicUsers.userStatusUpdatedOK'));

            if ($request->redirect)
                return action('QuickAccessController@index');
            else
                return action('UsersController@indexCompleted');
        }
        catch (Exception $e) {
            return redirect()->back()->withErrors(["private.user.indexWithStatus" => $e->getMessage()]);
        }
    }

    /**
     * Show confirm popup to remove the specified resource from storage.
     *
     * @param $userKey
     * @param $status
     * @return View
     */
    public function updateStatusConfirmCompleted(Request $request, $userKey, $status){
        $data = array();
        if ($request->redirect)
            $data['action'] = action("UsersController@updateStatusCompleted", ['userKey' => $userKey, 'status' => $status, 'redirect' => 1]);
        else
            $data['action'] = action("UsersController@updateStatusCompleted", [$userKey, $status]);
        /*$data['title'] = "AUTHORIZE";
        $data['msg'] = "Are you sure you want to authorize this User?";
        $data['btn_ok'] = "Authorize";
        $data['btn_ko'] = "Cancel";*/

        $data['title'] = trans('user.authorize');
        $data['msg'] = trans('user.are_you_sure_you_want_to_authorize_this_user');
        $data['btn_ok'] = trans('user.authorize');
        $data['btn_ko'] = trans('user.cancel');
        return view("_layouts.activateModal", $data);

    }

    /**
     * List all resources from storage.
     *
     * @return mixed
     */
    public function tableRolesUser($userKey)
    {
        $manage = Orchestrator::listEntityRoles($userKey);
        $collection = Collection::make($manage);

        // in case of json
        return Datatables::of($collection)
            ->editColumn('name', function ($role) use  ($userKey){
                return "<a >".$role->name."</a>";
            })
            ->addColumn('action', function ($user) use  ($userKey){
                return ONE::actionButtons( $userKey, ['edit' => 'UsersController@edit', 'delete' => 'UsersController@delete']);
            })
            ->rawColumns(['name','action'])
            ->make(true);
    }




    public function tableUsersManager(Request $request)
    {
        $response = Orchestrator::setEntityRole($request->entityKey);

        $usersKey = [];
        foreach ($response->json()->data as $item){
            $usersKey[] = $item->user_key;
        }

        $manage = Auth::listUser($usersKey);
        $collection = Collection::make($manage);

        // in case of json
        return Datatables::of($collection)
            ->addColumn('action', function ($user) use ($request) {
                $html  = "<a href='".action('UsersController@updateRole', [$user->user_key, $request->entityKey])."' class='btn btn-flat btn-info btn-xs' title='".trans('user.addManager')."'><i class='fa fa-plus'></i></a>";
                return $html;

            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function tableUsersManagerMan(Request $request)
    {
        $response = Orchestrator::setEntityRole($request->entityKey);

        $usersKey = [];
        foreach ($response as $item){
            $usersKey[] = $item->user_key;
        }
        $manage = Auth::listUser($usersKey);
        $collection = Collection::make($manage);

        // in case of json
        return Datatables::of($collection)
            ->addColumn('action', function ($user) use ($request) {
                $html  = "<a href='".action('UsersController@updateRoleMan', [$user->user_key, $request->entityKey])."' class='btn btn-flat btn-info btn-xs' title='".trans('user.addManager')."'><i class='fa fa-plus'></i></a>";
                return $html;

            })
            ->rawColumns(['action'])
            ->make(true);
    }


    /**
     * Display the specified resource.
     *
     * @param  $userKey
     * @return View
     */
    public function showReadOnly($userKey)
    {
        /*
                if(!ONE::checkUserPermissions('auth_user-view'))
                    return redirect()->back()->withErrors(["user.show" => trans('user.invalidPermission')]);
        */
        $roles = $this->rolesType;

        try {
            // User
            $user = Auth::getUserByKey($userKey);

            // Entity and Roles
            $entities = [];
            $userObj = Orchestrator::getUserByKey($userKey);


            foreach($userObj->entities as $item){
                $status = !empty($item->pivot->status) ? $item->pivot->status : null;;

            }

            $role = '';
            if($userObj->admin == 1){
                $role = 'admin';
            }else{
                $role = $userObj->role;
            }
            if(ONE::isEntity()){
                $entities = $userObj->entities;
            }

            // User Parameters
            $userParametersResponse = json_decode(json_encode($user->user_parameters),true);
            $registerParametersResponse = Orchestrator::getEntityRegisterParameters();

            //verify user parameters with responses
            $registerParameters = [];
            foreach ($registerParametersResponse as $parameter){
                $parameterOptions = [];
                $value = '';
                $file = null;
                if($parameter->parameter_type->code == 'radio_buttons' || $parameter->parameter_type->code == 'check_box' || $parameter->parameter_type->code == 'dropdown') {
                    foreach ($parameter->parameter_user_options as $option) {
                        $selected = false;
                        foreach ($userParametersResponse as $userParameter) {
                            if ($userParameter["parameter_user_key"] == $parameter->parameter_user_type_key && $userParameter['value'] == $option->parameter_user_option_key)
                                $selected = true;
                        }
                        $parameterOptions [] = [
                            'parameter_user_option_key' => $option->parameter_user_option_key,
                            'name' => $option->name,
                            'selected' => $selected
                        ];
                    }
                }elseif($parameter->parameter_type->code == 'file'){
                    $id = isset($userParametersResponse[$parameter->parameter_user_type_key][0]) ? $userParametersResponse[$parameter->parameter_user_type_key][0]['value'] : '';
                    if($id != ''){
                        $file = json_decode(json_encode(Files::getFile($id)),true);
                    }

                }else{
                    $value = collect($userParametersResponse)->where("parameter_user_key",$parameter->parameter_user_type_key)->first()["value"]??"";
                }
                $registerParameters []= [
                    'parameter_user_type_key'   => $parameter->parameter_user_type_key,
                    'parameter_type_code'       => $parameter->parameter_type->code,
                    'name'                      => $parameter->name,
                    'value'                     => isset($file) ? $file : $value,
                    'mandatory'                 => $parameter->mandatory,
                    'parameter_user_options'    => $parameterOptions
                ];
            }

            // Check if registration is completed
            $userValidate = Orchestrator::getUserAuthValidate();
            $registerCompleted = true;
            if(!empty($userValidate->status) && $userValidate->status == 'registered'){
                $registerCompleted = false;
            }

            // Form title (layout)
            $title = trans('privateUsers.show_user_readonly').' '.(isset($user->name) ? $user->name: null);

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['user'] = $user;
            $data['userObj'] = $userObj;
            $data['status'] = $status ?? null;
            $data['entities'] = $entities;
            $data['roles'] = $roles;
            $data['role'] = $role;
            $data['registerParameters'] = $registerParameters;
            // $data['userParameters'] = $userParameters;
            $data['registerCompleted'] = $registerCompleted;

            return view('private.user.userReadOnly', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["private.user.show" => $e->getMessage()]);
        }
    }

    public function updatePassword(Request $request, $userKey)
    {
        try{
            $oldPassword = $request->old_password;
            $password = $request->password;

            $user = Auth::updateUserPassword($oldPassword,$password, $userKey);
            Session::flash('message', trans('publicUsers.updatePasswordOk'));
            return redirect()->action('UsersController@edit',['userKey' => $userKey,'role' => $request->get("role",""), 'f' => 'users']);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([trans('privateUsers.update_password_nok') => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     */
    public function tableUserSearch(Request $request)
    {
        $users = Auth::getAllUsers();

        $email = $request->test;
        $u = null;
        foreach($users as $user){
            if($user->email == $email){

                $u = $user;
            }
        }

        $collection = Collection::make($u);

        // in case of json
        return Datatables::of($collection)
//            ->addColumn('action', function ($collection){
//                return ONE::actionButtons(null, null);
//            })
            ->make(true);

    }

    public function getView(){
        return view('private.new_entity');
    }

    /**
     * @param Request $request
     * @param $userKey
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param $entityGroupKey
     */
    public function showPermissions(Request $request, $userKey)
    {
        try{
            $entityModulesList = Orchestrator::getActiveEntityModules(Session::get('X-ENTITY-KEY'));

            $userPermissionsList = Orchestrator::getPermissionsList(['userKey' => $userKey]);

            $permissions=[];
            $modulePermissions=[];
            foreach ($userPermissionsList as $userPermission){
                if($userPermission->permission_show){
                    $modulePermissions[] = $userPermission->module->module_key.'_show';
                }
                elseif($userPermission->permission_create) {
                    $modulePermissions[] = $userPermission->module->module_key.'_create';

                }elseif( $userPermission->permission_update){
                    $modulePermissions[] = $userPermission->module->module_key.'_update';

                } elseif($userPermission->permission_delete){
                    $modulePermissions[] = $userPermission->module->module_key.'_delete';
                }

                if (isset($userPermission->module->module_key,$userPermission->module_type->module_type_key))
                    $permissions[$userPermission->module->module_key][$userPermission->module_type->module_type_key] = $userPermission;
            }

            $modulePermissions = array_unique($modulePermissions);
            $data = [];


            $data['userKey'] = $userKey;
            $data['modules'] = $entityModulesList;
            $data['permissions'] = $permissions;
            $data['modulePermissions'] = $modulePermissions;
            $data['role'] = $request->role;

            $data['sidebar'] = 'manager';
            $data['active'] = 'permissions';

            Session::put('sidebarArguments', ['userKey' => $userKey, 'role' =>  $request->role, 'activeFirstMenu' => 'permissions']);


            return view('private.user.permissions', $data);
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["privateUserPermission.show" => $e->getMessage()]);
        }

    }

    /**
     *
     * @param Request $request
     * @param $userKey
     * @return $this|\Illuminate\Http\RedirectResponse
     * @internal param $entityGroupKey
     */
    public function storePermissions(Request $request, $userKey)
    {
        try{
            $entityModulesList = Orchestrator::getActiveEntityModules(Session::get('X-ENTITY-KEY'));
            $data = [];

            foreach ($entityModulesList as $moduleKey => $entityModules){
                foreach ($entityModules->types as $moduleTypeKey => $moduleType){
                    $temp['module_key'] = $moduleKey;
                    $temp['module_type_key'] = $moduleTypeKey;
                    $temp['permission_show'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['show']) ? true : false;
                    $temp['permission_create'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['create']) ? true : false;
                    $temp['permission_update'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['update']) ? true : false;
                    $temp['permission_delete'] = isset($request->modules_types[$moduleKey][$moduleTypeKey]['delete']) ? true : false;
                    $data[]= $temp;
                }

            }

            $dataSend['user_key'] = $userKey;
            $dataSend['entity_permissions'] = $data;

            Orchestrator::setPermissions($dataSend);
            return redirect()->action('UsersController@showPermissions', ["userKey" => $userKey]);

        } catch(Exception $e) {
            return redirect()->back()->withErrors(["privateEntityPermission.add" => $e->getMessage()]);
        }
    }


    public function moderateUser(Request $request, $userKey, $siteKey){
        try{
            $data = Orchestrator::setUsersToModerate($userKey,$siteKey );
            Session::flash('message', trans('privateUser.moderation_ok'));
            if($request->moderation){
                return redirect()->action('UsersController@indexCompleted');
            }
            return redirect()->back();
        }catch(Exception $e) {
            return redirect()->back()->withErrors(["privateEntityPermission.add" => $e->getMessage()]);
        }

    }

    public function showUserMessages(Request $request, $user_key=null)
    {
        //  If request origin is from topic detail receives/sends cbKey/topicKey/type, for return button correct redirection
        $cbKey = !empty($request->cbKey) ? $request->cbKey : null;
        $topicKey = !empty($request->topicKey) ? $request->topicKey : null;
        $type = !empty($request->type) ? $request->type : null;

        try{
            $title = trans('privateUsers.user_messages');
            $messages = Orchestrator::getMessagesFromUser($user_key);
            $user = Orchestrator::getUserByKey($user_key);
            $userKeys = array_keys(collect($messages)->groupBy('from')->toArray());
            $messageUsers = Auth::listUser($userKeys);

            if($messageUsers) {
                foreach ($messageUsers as $messageUser) {
                    if($messages) {
                        foreach ($messages as $message) {
                            if ($message->from == $messageUser->user_key) {
                                $message->from_username = $messageUser->name . ' ' . ($messageUser->surname ?? '');
                            }
                        }
                    }
                }
            }

            $response = CB::getUserTopicsTimeline($user_key);
            $topics = $response->topics;

            return view('private.user.messages', compact('messages','user_key', 'user', 'title', 'cbKey', 'topicKey', 'type', 'topics'));

        }catch(Exception $e) {
            return redirect()->back();
        }
    }


    /**
     * List all resources from storage.
     *
     * @param Request $request
     * @return mixed
     */
    public function excel(Request $request)
    {
        try {
            $requestedUserKeys = collect(json_decode($request->get("userKeys")));
            $orchestratorUsers = collect(Orchestrator::getAllUsers())->keyBy("user_key");

            if (!$requestedUserKeys->isEmpty()) {
                $orchestratorUsers = $orchestratorUsers->filter(function($value,$key) use ($requestedUserKeys) {
                    return $requestedUserKeys->contains($value->user_key);
                });
            }
            $usersKeys = $orchestratorUsers->keys();

            $authUsers = collect(Auth::listUser($usersKeys));
            $entityUserParameters = collect(Orchestrator::getEntityRegisterParameters());
            $userTopicsCounts = EMPATIA::getUserTopicsCount($usersKeys);
            $entityEventVotes = EMPATIA::getEntityVoteEvents();
            $voteEventTopicsKeys = EMPATIA::getTopicsKeysForVoteEvents(collect($entityEventVotes)->pluck("vote_key"));
            $userVotesCounts = Vote::getUserVotesCount(collect($entityEventVotes)->pluck("vote_key"),$usersKeys,$voteEventTopicsKeys);

            $csvString = "User ID, "  .trans("privateUsers.name") . ", " . trans("privateUsers.email") . ", " . trans("privateUsers.roles") . ", " . trans("privateUsers.created_at");

            foreach ($entityUserParameters as $entityUserParameter) {
                $csvString .= ", " . $entityUserParameter->name;
            }
            $csvString .= ", Topics";
            foreach ($entityEventVotes as $entityEventVote) {
                $csvString .=
                    ", " . $entityEventVote->name . " (+)" .
                    ", " . $entityEventVote->name . " (-)";
            }

            $csvString .= "\r\n";

            unset($requestedUserKeys,$usersKeys,$voteEventTopicsKeys);

            foreach ($authUsers as $user) {
                $csvString .= $user->user_key . ", " . $user->name . ", " . $user->email . ", ";

                switch($orchestratorUsers->get($user->user_key)->pivot->role) {
                    case "manager":
                        $csvString .= trans("privateUsers.role_manager");
                        break;

                    case "admin":
                        $csvString .= trans("privateUsers.role_admin");
                        break;

                    case "user":
                    default:
                        $csvString .= trans("privateUsers.role_user");
                }

                $csvString .= ", " . $user->created_at;

                $userParameters = collect($user->user_parameters);
                foreach ($entityUserParameters as $entityUserParameter) {
                    $currentParameter = $userParameters->where("parameter_user_key",$entityUserParameter->parameter_user_type_key);

                    if ($currentParameter->count()>0) {
                        if (!empty($entityUserParameter->parameter_user_options)) {
                            $entityUserParameter->parameter_user_options = collect($entityUserParameter->parameter_user_options);

                            $choosenOption = $entityUserParameter->parameter_user_options->where("parameter_user_option_key",$currentParameter->first()->value);
                            if ($choosenOption->count()>0)
                                $csvString .= ", " . $choosenOption->first()->name;
                            else
                                $csvString .= ", ";
                        } else
                            $csvString .= ", " . $currentParameter->first()->value;
                    } else
                        $csvString .= ", ";
                }

                $csvString .= ", " . strval($userTopicsCounts->{ $user->user_key }??0);
                foreach ($entityEventVotes as $entityEventVote) {
                    $csvString .=
                        ", " . "+" . ($userVotesCounts->{ $entityEventVote->vote_key }->positive->{ $user->user_key } ?? 0) .
                        ", " . "+" . ($userVotesCounts->{ $entityEventVote->vote_key }->negative->{ $user->user_key } ?? 0);
                }

                $csvString .= "\r\n";
            }

            $headers = [
                'Content-Encoding'    => 'UTF-8',
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="users.csv"',
            ];

            return \Response::make($csvString, 200, $headers);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["users.excel" => $e->getMessage()]);
        }
    }

    public function pdfList(Request $request){
        try {
            $requestedUserKeys = collect(json_decode($request->get("userKeys")));
            $orchestratorUsers = collect(Orchestrator::getAllUsers())->keyBy("user_key");

            if (!$requestedUserKeys->isEmpty()) {
                $orchestratorUsers = $orchestratorUsers->filter(function($value,$key) use ($requestedUserKeys) {
                    return $requestedUserKeys->contains($value->user_key);
                });
            }
            $usersKeys = $orchestratorUsers->keys();

            $authUsers = collect(Auth::listUser($usersKeys));
            $entityUserParameters = collect(Orchestrator::getEntityRegisterParameters());
            $userTopicsCounts = EMPATIA::getUserTopicsCount($usersKeys);
            $entityEventVotes = EMPATIA::getEntityVoteEvents();
            $voteEventTopicsKeys = EMPATIA::getTopicsKeysForVoteEvents(collect($entityEventVotes)->pluck("vote_key"));
            $userVotesCounts = Vote::getUserVotesCount(collect($entityEventVotes)->pluck("vote_key"),$usersKeys,$voteEventTopicsKeys);

            $usersArray = [];

            foreach ($authUsers as $user) {
                switch($orchestratorUsers->get($user->user_key)->pivot->role) {
                    case "manager":
                        $userRole = trans("privateUsers.role_manager");
                        break;

                    case "admin":
                        $userRole = trans("privateUsers.role_admin");
                        break;

                    case "user":
                    default:
                        $userRole = trans("privateUsers.role_user");
                }

                $currentUser = array(
                    "name" => $user->name,
                    "email" => $user->email,
                    "role" => $userRole,
                    "created_at" => $user->created_at,
                    "topics" => $userTopicsCounts->{ $user->user_key }??0,
                    "positiveVotes" => $userVotesCounts->positive->{ $user->user_key }??0,
                    "negativeVotes" => $userVotesCounts->negative->{ $user->user_key }??0,
                    "parameters" => array()
                );

                $userParameters = collect($user->user_parameters);
                foreach ($entityUserParameters as $entityUserParameter) {
                    $currentParameter = $userParameters->where("parameter_user_key",$entityUserParameter->parameter_user_type_key);

                    $currentUserParameterToArray = array(
                        "name" => $entityUserParameter->name,
                        "value" => trans("privateUsers.not_defined")
                    );

                    if ($currentParameter->count()>0) {
                        if (!empty($entityUserParameter->parameter_user_options)) {
                            $entityUserParameter->parameter_user_options = collect($entityUserParameter->parameter_user_options);

                            $choosenOption = $entityUserParameter->parameter_user_options->where("parameter_user_option_key",$currentParameter->first()->value);
                            if ($choosenOption->count()>0)
                                $currentUserParameterToArray["value"] = $choosenOption->first()->name;
                        } else
                            $currentUserParameterToArray["value"] = $currentParameter->first()->value;
                    }

                    $currentUser["parameters"][] = $currentUserParameterToArray;
                }

                $usersArray[] = $currentUser;
            }

            $allUsersArray = array_chunk($usersArray, 50);

            $pdf = array();
            foreach ($allUsersArray as $usersArray) {
                $pdf[] = PDF::loadView('private.user.pdf', compact('usersArray'))
                    ->setPaper('a4','portrait')->setWarnings(false);
            }
            /* unset everything to relase memory */
            unset($orchestratorUsers,$usersKeys,$authUsers,$entityUserParameters,$userTopicsCounts,$entityEventVotes,$userVotesCounts,$user,$userRole,$currentUser,$currentParameter,$currentUserParameterToArray,$choosenOption,$entityUserParameter,$userParameters,$allUsersArray,$usersArray);

            if (count($pdf) > 1) {
                do {
                    $zipFileName = storage_path("app/users-" . Carbon::now()->format("Y-m-d_his") . ".zip");
                    if (\File::exists($zipFileName))
                        $zipFileName = "";
                } while (empty($zipFileName));
                $zipFile = (new Zipper)->make($zipFileName);

                $fileNames = array();
                foreach ($pdf as $index => $pdfItem) {
                    do {
                        $fileName = storage_path("app/PDFTemp-" . str_random(32) . ".pdf");
                        if (\File::exists($fileName))
                            $fileName = "";
                    } while (empty($fileName));

                    $pdfItem->save($fileName);
                    $zipFile->add($fileName, "users-" . $index . ".pdf");
                    $fileNames[] = $fileName;
                    $pdfItem = null;
                    $pdf[$index] = null;
                }

                $zipFile->make($zipFileName);
                \File::delete($fileNames);
                return response()->download($zipFileName)->deleteFileAfterSend(true);
            } else {
                return $pdf[0]->download('users.pdf');
            }

            $pdf = PDF::loadView('private.user.pdf', compact('usersArray'))
                ->setPaper('a4','portrait')->setWarnings(false);
            return $pdf->download('users.pdf');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["users.pdfList" => $e->getMessage()]);
        }
    }


    /**
     * RETURN THE VIEW FOR IN PERSON REGISTRATION
     * @param $type
     * @param $cbKey
     * @param $voteEventKey
     * @return View
     */
    public function inPersonRegistration($type, $cbKey, $voteKey){
        try {
            $cb = CB::getCb($cbKey);
            $title = trans("privateUsers.inPersonRegistrationTitleFor").' '.$type.' '.$cb->title;
            $parameters = Orchestrator::getEntityRegisterParameters();

            $sidebar = 'vote';
            $active = 'inPersonRegistration';
            return view('private.user.inPersonRegistration', compact('title', 'parameters', 'voteKey','sidebar','active','type','cbKey'));
        }catch(Exception $e) {
            return redirect()->back();
        }
    }


    /**
     * STORES THE USER FOR IN PERSON REGISTRATION
     * @param $userBasicRegisterFields
     * @param $userParameters
     * @return array
     */
    public function storeInPersonRegistration($userBasicRegisterFields, $userParameters)
    {
        try {
            //STORE THE USER
            $storeNewUserResponse = Auth::storeInPersonRegistration($userBasicRegisterFields, $userParameters);

            //STORE FAILED
            if(!isset($storeNewUserResponse->user)){
                if($storeNewUserResponse['error'] == 408){ //comModulesAuth.parametersAlreadyUsed
                    $data['parameters'] = $userParameters;
                    $getUserResponse = Auth::getUserAccordingToSuppliedFields($search = 'PARAMETERS', $data);
                    $userInfo['user_key'] = $getUserResponse[0]->user_key;
                    $userInfo['name'] = $getUserResponse[0]->name;
                    $userInfo['surname'] = $getUserResponse[0]->surname;
                    $userInfo['existed'] = true;
                    return $userInfo;
                }elseif($storeNewUserResponse['error'] == 409){ //comModulesAuth.errorIdNumberOrEmailAlreadyExists
                    $getUserResponse = Auth::getUserAccordingToSuppliedFields($search = 'EMAIL', $userBasicRegisterFields);
                    $userInfo['user_key'] = $getUserResponse->user_key;
                    $userInfo['name'] = $getUserResponse->name;
                    $userInfo['surname'] = $getUserResponse->surname;
                    $userInfo['existed'] = true;
                    return $userInfo;
                }else{ //comModulesAuth.errorInStoreUser
                    return ['error' => trans('comModulesAuth.errorInStoreUser')];
                }

                //STORE OK
            }else{
                $user_key = $storeNewUserResponse->user->user_key;
                //ATTACH TO CURRENT ENTITY
                Orchestrator::storeUser($user_key,ONE::getEntityKey());

                $userInfo['user_key'] = $storeNewUserResponse->user->user_key;
                $userInfo['name'] = $storeNewUserResponse->user->name;
                $userInfo['surname'] = isset($storeNewUserResponse->user->surname) ?? '';
                return $userInfo;
            }

        }catch(Exception $e){
            return ['error' => trans('comModulesAuth.errorInStoreUser')];
        }
    }


    /**
     * PRINT NEW LINE TO INFORM THE PO
     * @param $userResponse
     * @return string
     */
    public function generateHtmlLineForUser($userResponse)
    {
        if(isset($userResponse['existed'])){
            return '<div class="new-user-line">
                    <div class="col-xs-6">'.$userResponse['name'].' '.$userResponse['surname'].'</div> 
                    <div class="col-xs-4"><span class="color-blue"><i>'.trans('privateUsers.existed').'</i> <b><i>'.trans('privateUsers.registered_for_voting').'</i></b></span></div>
                    <div class="col-xs-2">
                    <a href="'.action('UsersController@show', ['userKey' => $userResponse['user_key']]).'" class="btn btn-flat btn-info btn-xs my-new-user-btn" title="'.trans('privateUsers.view_details').'"><i class="fa fa-eye"></i></a>
                    </div>
                    </div>';

        }else{
            return '<div class="new-user-line"><div class="col-xs-6">'.$userResponse['name'].' '.$userResponse['surname'].'</div>
                    <div class="col-xs-4"><span class="color-green"><i>'.trans('privateUsers.new_user').'</i><b><i>'.trans('privateUsers.registered_for_voting').'</i></b></span></div>
                    <div class="col-xs-2">
                    <a href="'.action('UsersController@show', ['userKey' => $userResponse['user_key']]).'" class="btn btn-flat btn-info btn-xs my-new-user-btn" title="'.trans('privateUsers.view_details').'"><i class="fa fa-eye"></i></a>
                    </div>
                    </div>';



        }
    }


    /**
     * BUILD THE OBJECT TO ATTACH A USER TO THE EVENT VOTE CODE
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function saveInPersonRegistration(Request $request)
    {
        try{
            $attachToCode = true;
            if(isset($request['doNotAttachToCode'])){
                $attachToCode = false;
            }
            $voteEventKey = $request['vote_event_key'];
            $faker = Faker::create();
            $inputs = $request['inputs'];
            $userBasicRegisterFields = [];
            $userParameterFields = collect($inputs)->filter(function($item){
                if(str_contains($item['name'],'parameter_')) {
                    return true;
                }
            });

            foreach ($inputs as $input){
                $userBasicRegisterFields[$input['name']] = $input['value'];
            }

            if(empty($userBasicRegisterFields['email'])){
                $userBasicRegisterFields['email'] = AuthController::generateFakeEmail($userBasicRegisterFields['name'],$userBasicRegisterFields['surname']);
            }

            //WE NEED TO GENERATE A PASSWORD
            $userBasicRegisterFields['password'] = $faker->password;

            foreach ($userParameterFields as $parameterField){
                $userParameters[str_replace('parameter_','',$parameterField['name'])] = $parameterField['value'];
            }

            $storeUserResponse = $this->storeInPersonRegistration($userBasicRegisterFields, $userParameters);

            if(!$attachToCode){
                if(!isset($storeUserResponse['error'])){
                    return response()->json(["success" => $storeUserResponse['user_key']]);
                }else{
                    return response()->json(["error" => trans('comModulesAuth.errorInStoreUser')]);
                }
            }

            if(!isset($storeUserResponse['error'])){
                //ATTACH THIS USER TO THE VOTE EVENT WITH THE CODE ASSOCIATED
                $attachResponse = Vote::attachUserToVoteEventWithCode($storeUserResponse['user_key'],$voteEventKey,$userBasicRegisterFields['code']);
                if(is_array($attachResponse)){

                    return response()->json(["error" => $attachResponse['message']]);
                }else{
                    return $this->generateHtmlLineForUser($storeUserResponse);
                }
            }else{
                return response()->json(["error" => trans('comModulesAuth.errorInStoreUser')]);
            }

        }catch(Exception $e) {
            return response()->json(["error" => trans('comModulesAuth.errorInStoreUser')]);
        }
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createMessageToAll(Request $request)
    {
        try {
            return view('private.user.messagesToAll');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["messageToAll.create" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessageToAll(Request $request) {
        try {
            if (!empty($request->get("message",""))) {
                $sendMessageResponse = Orchestrator::sendMessageToAll($request->message);
                $dataToReturn = array(
                    "success" => true,
                    "messages" => array(
                        "success" => $sendMessageResponse->success,
                        "failed" => $sendMessageResponse->failed
                    ),
                    "emails" => array(
                        "sent" => false
                    )
                );

                if ($request->get("send_email", "false") == "true") {
                    $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'];

                    $entityKey = Session::get('X-ENTITY-KEY');
                    $entity = is_null($entityKey) ? null : Orchestrator::getEntity(Session::get('X-ENTITY-KEY'));


                    $userKeys = collect(Orchestrator::getAllUsers())->pluck("user_key");
                    $usersData = collect(Auth::listUser($userKeys));
                    $dataForNotify = [];
                    foreach ($usersData as $userData) {
                        $dataForNotify[$userData->user_key] = array(
                            "destiny" => $userData->email,
                            "tags" => array(
                                "name" => $userData->name,
                                "message" => $request->message,
                                "sender" => $entity->name ?? null,
                                "link" => $url
                            )
                        );
                    }

                    $sendEmailResponse = Notify::sendManyEmails("message_notification", $dataForNotify);
                    $dataToReturn["emails"] = array(
                        "sent" => true,
                        "success" => $sendEmailResponse->success ?? 0,
                        "failed" => $sendEmailResponse->failed ?? 0
                    );
                }

                return response()->json($dataToReturn);
            }
            return response()->json(["failed"=>true],400);
        } catch (Exception $e) {
            return response()->json(["failed"=>true,"e"=>$e->getMessage()],500);
        }
    }

    /**
     * @return $this
     */
    public function getTinyMCE()
    {
        return view('private._private.tinymce')->with('action', action('UsersController@getTinyMCEView'));

    }
    public function getTinyMCEView($type = null)
    {
        $types[] = trans('contents.files');

        $uploadToken = Files::getUploadKey();

        return view('private._private.tinymce-content', compact('uploadToken'));

    }

    /**
     * sends the message from the user to the entity
     * @param Request $request
     * @return $this
     */
    public function markMessagesAsUnseen(Request $request)
    {
        try {
            Orchestrator::markMessagesAsUnseen($request);
            return 'success';
        }
        catch(Exception $e) {
            return 'error';
        }
    }

    /**
     * @param Request $request
     * @param $userKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function manualGrantLoginLevel(Request $request, $userKey)
    {
        try {
            $loginLevelKey = $request->input('loginLevelKey');

            if (isset($loginLevelKey)){
                $response = Orchestrator::manualGrantLoginLevel($loginLevelKey, $userKey);

                Session::flash('message', trans('user.manualGrantLoginLevel_ok'));
                return redirect()->back();
            }
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["privateUserPermission.show" => $e->getMessage()]);

        }
    }

    /**
     * @param Request $request
     * @param $userKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function manualRemoveLoginLevel(Request $request, $userKey)
    {
        try {
            $loginLevelKey = $request->input('loginLevelKey');

            if (isset($loginLevelKey)){
                $response = Orchestrator::manualRemoveLoginLevel($loginLevelKey, $userKey);

                Session::flash('message', trans('user.manualRemoveLoginLevel_ok'));
                return redirect()->back();
            }
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["privateUserPermission.show" => $e->getMessage()]);

        }
    }

    /**
     * @param $userKey
     * @return mixed
     */
    public function tableUserLoginLevels($userKey)
    {
        //Get User Login Levels
        $entityKey = Session::get('X-ENTITY-KEY');

        $loginLevels = Orchestrator::getUserLoginLevels($userKey) ? (array) Orchestrator::getUserLoginLevels($userKey) : null;

        $collection = Collection::make($loginLevels);
        // in case of json
        return Datatables::of($collection)
            ->editColumn('name', function ($collection) use ($entityKey){
                return "<a href='" . action("EntityLoginLevelsController@show",["loginLevelKey"=>$collection->login_level_key,"entity_key"=>$entityKey]) . "' target='_blank'>" . $collection->name . "</a>";
            })
            ->editColumn('created_at', function ($collection) {
                if ($collection->manual == 1){
                    return '<span data-toggle="tooltip" title="'.$collection->created_by.'">'.$collection->created_at.'</span> <span class="badge badge-danger">'.trans('privateUsers.manual').'</span>';
                }
                return '<span data-toggle="tooltip" title="'.$collection->created_by.'">'.$collection->created_at.'</span>';
            })
            ->rawColumns(['name','created_at'])
            ->make(true);
    }

    /**
     * @param $userKey
     * @return mixed
     */
    public function tableManageUserLoginLevels($userKey)
    {
        //Get User Login Levels
        $entityKey = Session::get('X-ENTITY-KEY');

        $userLoginLevels = Orchestrator::getUserLoginLevels($userKey) ? (array) Orchestrator::getUserLoginLevels($userKey) : null;
        $loginLevels = Orchestrator::getAllEntityLoginLevels($entityKey) ? (array) Orchestrator::getAllEntityLoginLevels($entityKey) : null;

        $collection = Collection::make($loginLevels);
        // in case of json
        return Datatables::of($collection)
            ->editColumn('name', function ($collection) use ($entityKey){
                return "<a href='" . action("EntityLoginLevelsController@show",["loginLevelKey"=>$collection->login_level_key,"entity_key"=>$entityKey]) . "' target='_blank'>" . $collection->name . "</a>";
            })
            ->addColumn('manage', function ($collection) use($userLoginLevels, $userKey) {
                if (isset($userLoginLevels[$collection->login_level_key]))
                    return '<a href="' . action('UsersController@manualRemoveLoginLevel', ['userKey' => $userKey ?? null , 'loginLevelKey' => $collection->login_level_key]) . '" class="btn btn-flat btn-danger btn-xs login-level-operation" data-toggle="tooltip" title="' . trans('form.delete') . '"><i class="fa fa-remove"></i></a> ';
                else
                    return '<a href="' . action('UsersController@manualGrantLoginLevel', ['userKey' => $userKey ?? null , 'loginLevelKey' => $collection->login_level_key]) . '" class="btn btn-flat btn-warning btn-xs login-level-operation" data-toggle="tooltip" title="' . trans('form.add') . '"><i class="fa fa-plus"></i></a> ';

            })
            ->rawColumns(['name','manage'])
            ->make(true);
    }

    /**
     * @param $userKey
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function checkAndUpdateUserLoginLevel($userKey)
    {
        try {
            Orchestrator::checkAndUpdateUserLoginLevel($userKey);
            Session::flash('message', trans('user.automaticUpdate_ok'));
            return redirect()->back();
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["user.updateLoginLevels_fail" => $e->getMessage()]);
        }
    }

    public function anonymizeUser(Request $request, $userKey) {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["user.anonymize" => trans('privateEntitiesDivided.permission_message')]);
        }

        try{
            $anonymizeResponse = EMPATIA::anonymizeUsers($userKey);
            if (!empty($anonymizeResponse->success)) {
                if ($anonymizeResponse->success)
                    Session::flash('message', trans('privateUsers.anonymize_ok'));
                else
                    Session::flash('message', trans('privateUsers.anonymize_failed'));
            } else {
                Session::flash('message', trans('privateUsers.anonymize_failed'));
            }
        } catch(Exception $e) {
            Session::flash('message',$e->getMessage());
        }
    }

    public function anonymizeUsers(Request $request) {
        if(Session::get('user_role') != 'admin'){
            return redirect()->back()->withErrors(["user.anonymize" => trans('privateEntitiesDivided.permission_message')]);
        }

        try{
            $requestedUserKeys = json_decode($request->get("userKeys"));
            EMPATIA::anonymizeUsers($requestedUserKeys);

            return redirect()->back()->withMessage("privateUsers.anonymize_ok");
        } catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateUsers.anonymize_nok') => $e->getMessage()]);
        }
    }
}
