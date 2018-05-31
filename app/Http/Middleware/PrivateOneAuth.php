<?php
/**
 * Created by PhpStorm.
 * User: Vitor Fonseca
 * Date: 08/10/2015
 * Time: 15:34
 */

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Auth\Guard;
use Session;
use ONE;
use Illuminate\Support\Facades\URL;


class PrivateOneAuth
{
    /**
     * Create a new filter instance.
     *
     * @internal param Guard $auth
     */
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Use keyword "all" to give permission to all managers
        $perms = [
            "CbsController"          => [
                                        "participation_admin",
                                        "participation_create",
                                        "participation_show",
                                        ],

            "EntitiesSitesController" => ["cms_sites"],
            "AccessMenusController"   => ["cms_menus"],
            "ContentManagerController"=> ["cms_pages"],

            "UsersController"        => ["users_list"],
            "EntityGroupsController" => ["users_groups"],
            "AccessesController"     => ["users_analytics"],

            "UsersController"        => ["moderation_users"],
            "ModerationController"   => [
                                        "moderation_participation",
                                        "moderation_comments"
                                        ],

            "EmailsController"         => ["communication_email"],
            "SmsController"            => ["communication_sms"],
            "EntityMessagesController" => ["communication_internalMessages"],

            "QuestionnairesController" => ["other_questionnaire"],
            "EventSchedulesController" => ["other_polls"],
            "ShortLinksController"     => ["other_short_links"],

            "EntitiesDividedController" => ["conf_entity"],
            "ParametersTemplateController" => ["conf_parameters_template"],
            "GamificationsController"   => ["conf_gamification"],
            "KiosksController"          => ["conf_kiosk"],
            "OpenDataController"        => ["conf_open_data"],

            "QuickAccessController"     => "all",
            "DashboardController"       => "all",

            "PermissionsController"     =>"all",
            "TranslationsController"    =>"all",


        ];

        $cbPerms = [
            "CbsController" => [
                            "show" => ['participation_details'],
                            "voteAnalysis" => ["participation_analytics",],
                            "showTopics" => [ "participation_list", ],
            //                "index" => "all",
                            "getAllComments" => [ "participation_comments", ],
                            "showConfigurations" => ['conf_process'],
                            "showNotifications" => ['conf_notifications'],
                            "showSecurityConfigurations"=>['security_login_levels'],
                            "getGroupsPads"=>['security_permissions'],
                            "showModerators"=>['advanced_moderators'],
                            "voteAnalysisEmpaville"=>['advanced_empaville'],
                            "showQuestionnaires"=>['advanced_quest'],
                            "showCbComments" => [ "participation_comments", ],
                            "showParameters"=>['conf_events'],
                            "showVotes"=>['conf_events'],
                            "showGroupPermissions"=>['security_permissions'],
                            "showExportTopics"=>['advanced_dataMigration'],
                         ],

            "TopicController" => [
                                "getIndexTableStatus" => [ "participation_list"],
                                "show" =>['topic_edit'],
                                "create" =>['topic_edit'],
                                "delete" =>['topic_edit'],
                                "edit" =>['topic_edit'],

                //               "all" => [ "all",  ],
//                                "create"=>['create_topic'],
                            ],
            "CbsParametersController" => [
                                    "getIndexTableParameters" => 'conf_parameters'
                                    ],
            "CbsVoteController" => [
                                    "getIndexTableVote"=>['getIndexTableVote'],
                                    ],
            "FlagsController"=>[
                                "index"=>['conf_flags'],
                                "getIndexTable"=>['conf_flags'],
                                ],
            "TechnicalAnalysisProcessesController"=>[
                                                    "index"=>['advanced_TA'],
                                                    "getIndexTable"=>['advanced_TA'],
                                                    "showQuestions"=>['advanced_TA'],
                                                    ],
            "OperationSchedulesController"=>[
                                            "index"=>['advanced_schedules'],
                                            "getIndexTable"=>['advanced_schedules'],
                                            ],
            "TranslationsController"=> [
                                        "index"=>['advanced_translations'],
                                    ],

            "TechnicalAnalysisController"=>[
                                            "create" => ['topic_edit'],
                                            "verifyIfExistsTechnicalAnalysis" => ['topic_edit'],
                                            ]
        ];

        $res = ONE::privateMiddleware($request, $next);

//        return $res;

        $action = class_basename($request->route()->getActionName());
        $controller = explode("@", $action);
        $controller = $controller[0];

        if(ONE::checkRoutePermissions($perms, $cbPerms, $controller)) {
            return $res;
        } else {
            return response()->json(['error' => 'Unauthorized'], 401)->send();
        }
    }
}