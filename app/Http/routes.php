<?php

//Route::get('private/cb/{cbKey}/finishPhase/{topicCheckpointNewId}', 'CbsController@finishPhase');
//Route::get('private/cb/{cbKey}/finishPhase2/{topicCheckpointNewId}', 'CbsController@finishPhase2');


/*
 * Send Vote Receipt for Vote Event
 * (Commented for security reasons)
 */
/*Route::group(['middleware' => ['web','privateAuthOneAdmin','authOne']], function () {
    Route::get("sendVoteReceipts/{eventKey}/{timestamp?}/{submittedStatus?}", function ($eventKey,$timestamp = null, $submittedStatus = null) {
        try {
            $submittedStatus = (!is_null($submittedStatus) && $submittedStatus>0) ? 1 : 0;
            $timestamp = (!is_null($timestamp) && $timestamp<0) ? null : $timestamp;

            $eventVotes = App\ComModules\Vote::getEventAndVotes($eventKey);
            $userVotes = array();
            $timestamps = array();
            $topicKeys = array();
            foreach ($eventVotes->votes as $eventVote) {
                if ($eventVote->submitted==$submittedStatus) {
                    $currentVoteTimestamp = \Carbon\Carbon::parse($eventVote->updated_at)->timestamp;

                    if (is_null($timestamp) || (!is_null($timestamp) && $timestamp>$currentVoteTimestamp)) {
                        $userVotes[$eventVote->user_key][] = $eventVote->vote_key;
                        $timestamps[$eventVote->user_key] = $currentVoteTimestamp;

                        $topicKeys[$eventVote->vote_key] = $eventVote->vote_key;
                    }
                }
            }

            if (count($userVotes)>0 && count($topicKeys)>0) {
                $users = \App\ComModules\Auth::listUser(array_keys($userVotes));
                $topics = collect(App\ComModules\CB::getTopicsByKeys($topicKeys))->keyBy('topic_key')->toArray();
                if (count($users)>0 && count($topics)>0) {
                    $emailsSent = 0;
                    foreach ($users as $user) {
                        if (empty(Session::get("SITE-CONFIGURATION.user_email_domain","")) || !ends_with($user->email,Session::get("SITE-CONFIGURATION.user_email_domain",""))) {
                            // <Submission Timestamp>.<User key>.<Event ID>/<Number of Votes Submitted>
                            $uniqueID = ($timestamps[$user->user_key] ?? "") . "." . $user->user_key . "." . $eventVotes->eventID . "/" . count($userVotes[$user->user_key]);

                            $userTopics = array();
                            foreach ($userVotes[$user->user_key] as $vote) {
                                $userTopics[$vote] = $topics[$vote];
                            }

                            $tags = [
                                "name" => $user->name,
                                "votesCount" => count($user->user_key),
                                "voteList" => view('public.' . ONE::getEntityLayout() . '.cbs.submittedVotesReceiptList', ["topics" => $userTopics])->render(),
                                "uniqueID" => $uniqueID
                            ];
                            try {
                                App\ComModules\Notify::sendEmail('vote_submitted', $tags, (array)$user);
                                $emailsSent++;
                                echo "Sent email to <b>" . ($user->email ?? "no email") . "</b>:" . $uniqueID . "<br>";
                            } catch (Exception $e) {
                                echo $e->getMessage() . "|" . $e->getLine() . "|" . $e->getFile() . "<br>";
                            }
                        } else
                            echo "Didn't send email to <b>" . ($user->email ?? "no email") . "</b> (no own email)<br>";
                    }
                    echo "finished: " . $emailsSent . " emails sent.";
                } else
                    echo "no users or topics";
            } else
                echo "no users";
        } catch (Exception $e) {
            dd($e);
        }
    });
});*/

$contentTypes = "pages|news|events|articles|faqs|municipal_faqs|gatherings"; //HardCoded ContentTypes to filter routes

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Breadcrumbs::register('private', function ($breadcrumbs) {
    $breadcrumbs->push(trans('private.private'), url("/private"));
});


Breadcrumbs::register('public', function ($breadcrumbs) {
    $breadcrumbs->push('Public', url("/public"));
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::get('test_import', function()
{
    $FILE = base_path().'/empatia_translations.csv';
    $DIR = base_path().'/test_import';

    if(!File::exists($FILE)) {
        echo "File does not exist!";
        return;
    }

    $content = explode("\n", File::get($FILE));

    $created = array();

    /** HEADER **/
    echo "Header: ".$content[0]."<br>";
    $lang = explode(";",trim($content[0]));
    if(count($lang) <= 1 || $lang[0] != "code") {
        echo "Header does not include proper header!";
        return;
    }

    $lang = array_except($lang, "0");

    /** TODO: VALIDATE LANGUAGES!!! **/

    /** Remove all translations **/
    File::cleanDirectory($DIR);

    /** FOR EACH LANGUAGE **/
    foreach($lang as $lpos => $l) {
        // Create dir if does not exist
        echo "Make dir: ".$DIR."/".$l."<br>";
        File::makeDirectory($DIR."/".$l, 493, true, true);

        for($i = 1; $i < count($content); $i++) {
            $line = explode(";", $content[$i]);
            if(count($line) <= 1) continue;

            $f = explode(".", $line[0]);

            $code = $f[1];

            $f = $DIR."/".$l."/".$f[0].".php";

            if(!File::exists($f)) {
                File::put($f, "<?php\n\nreturn array (\n");
                array_push($created, $f);
            }
            File::append($f, "\t'".$code."' => '".htmlentities(trim(preg_replace('/\s+/', ' ', $line[$lpos])), ENT_NOQUOTES, "UTF-8")."',\n");

            echo $i.": ".$line[0]." >> ".htmlentities($line[$lpos])."<br>";
        }
    }

    foreach($created as $f) {
        File::append($f, ");\n");
    }
});

Route::get('test_export', function()
{
    $DIR = base_path().'/resources/lang';

//	echo "=== START ===";
    $out = array();
    $l = array();

    $langs = File::directories($DIR);

    foreach($langs as $langDir) {
        $lang = File::name($langDir);
        array_push($l, $lang);

//		echo "<br><br>=== LANG: $lang => $langDir<br>";

        $files = File::files($langDir);
        foreach($files as $fileName) {
            if(File::extension($fileName) == "php") {
                $file = File::name($fileName);
//				echo ">> File: $file => $fileName<br>";

                $content = File::getRequire($fileName);
                foreach($content as $key => $trans) {
                    if(is_array($trans)) {
//						echo "-- Translation is array! ".implode(';',$trans)."<br>";
                        array_set($out, $file.".".$key.".".$lang, "ERROR ARRAY!!!");
                    } else {
//						echo "++ $key:$trans<br>";
                        array_set($out, $file.".".$key.".".$lang, trim($trans, "\n\r\0\x0B"));
                    }

//					break;
                }
            } else {
//				echo "-- Ignoring non php file: ".$fileName."<br>";
            }

//			break;
        }

//		break;
    }

#	echo "<br><br>==========================<br>";
#	echo "<pre>";
#	print_r($out);
#	echo "</pre>";

    $print = false;

    if(!$print) {
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=empatia_translations.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    /** HEADER **/
    $line = "code;";
    foreach($l as $lang) {
        $line.= '"'.$lang.'";';
    }

    if(!$print)
        echo $line."\n";
    else
        echo $line."<br>";

    /** CONTENT **/
    foreach($out as $t => $value) {
        foreach($value as $code => $v) {
            $line = '"'.$t.".".$code.'";';

            foreach($l as $lang) {
                if(array_has($out, $t.".".$code.".".$lang)) {
                    $line.= '"'.html_entity_decode(array_get($out, $t.".".$code.".".$lang), ENT_NOQUOTES, "cp1252").'";';
                } else {
                    $line.= ';';
                }
            }

            if(!$print)
                echo $line."\n";
            else
                echo $line."<br>";
        }
    }
});



Route::group(['middleware' => ['web']], function () use ($contentTypes) {
    Route::get("splashed",function() {
        Session::put("splashScreen",time());
        return response()->redirectTo("/");
    })->name("splashScreen.pass");

    Route::post('/auth/verifyLogin', ['as' => 'auth.verifyLogin', 'uses' => 'AuthController@verifyLogin']);
    Route::get('/auth/logout', 'AuthController@logout');
    Route::post('/auth/exists', 'AuthController@verifyEmailExists');
    Route::get('/auth/migrate','AuthController@migrateUserToEntityConfirmation');
    Route::post('/auth/migrate', 'AuthController@migrateUserToEntity');
    Route::post('/auth/validateSmsToken', 'AuthController@validateSmsToken');

    Route::get('tracking/showTracking', ['as'=>'showTracking', 'uses'=>'TrackingController@showTracking']);
    Route::get('tracking/getTrackingTable', ['as'=>'getTrackingTable', 'uses'=>'TrackingController@getTrackingTable']);
    Route::get('tracking/show', ['as'=>'show', 'uses'=>'TrackingController@show']);
    Route::get('tracking/show/{id}', ['as'=>'show', 'uses'=>'TrackingController@show']);
    
    // Admin Login
    Route::group(['middleware' => ['privateAuthOneAdmin']], function () {
        /* ---- Auth Controller ---- */
        Route::get('/auth/admin', 'AuthController@adminLogin');
        /* ---- END Auth Controller ---- */

        /* ---- One Controller ---- */
        Route::post('one/setPrivateLanguage', 'OneController@setPrivateLanguage');
        /* ---- END One Controller ---- */
    });

    /* Authentication Social Network Controller */
    Route::get('auth/facebook/callback', 'AuthSocialNetworkController@handleFacebookCallback');
    Route::get('auth/facebook/remove', 'AuthSocialNetworkController@removeFacebook');
    Route::get('auth/facebook', 'AuthSocialNetworkController@redirectToFacebook');
    /* ---- END Authentication Social Network Controller ---- */

    Route::group(['middleware' => ['privateAuthOne']], function () use ($contentTypes) {
        Route::get('/showGraphics', ['as'=>'showGraphics', 'uses'=>'PerformanceController@showGraphics']);
        /*Route::get('/private', ['as' => 'private', function () {
            return view('private');
        }]);*/

        Route::get('tracking/showTracking', ['as'=>'showTracking', 'uses'=>'TrackingController@showTracking']);
        Route::get('tracking/getTrackingTable', ['as'=>'getTrackingTable', 'uses'=>'TrackingController@getTrackingTable']);
        Route::get('tracking/show', ['as'=>'show', 'uses'=>'TrackingController@show']);
        Route::get('tracking/show/{id}', ['as'=>'show', 'uses'=>'TrackingController@show']);
        Route::get('/private', ['as' => 'private', 'uses' => 'QuickAccessController@index']);
        Route::get('/private/wizard/install','QuickAccessController@firstInstallWizard');
        Route::get('/private/wizard/install/finish','QuickAccessController@firstInstallWizardFinish');
        Route::get('PerformanceController/loadAvgGraphPage', ['as' => 'loadAvgGraphPage', 'uses' => 'PerformanceController@loadAvgGraphPage']);
        Route::get('private/getActivePads', ['as' => 'quickAccessController.getActivePads', 'uses' => 'QuickAccessController@getActivePads']);
        Route::get('private/getPostsToModerate', ['as' => 'quickAccessController.getPostsToModerate', 'uses' => 'QuickAccessController@getPostsToModerate']);
        Route::get('private/getUsersWithUnreadMessages', ['as' => 'quickAccessController.getUsersWithUnreadMessages', 'uses' => 'QuickAccessController@getUsersWithUnreadMessages']);
        Route::post('one/getContent', 'OneController@getContent');
        Route::post('one/getContentUsers', 'OneController@getContentUsers');
        Route::post('one/getSidebar', 'OneController@getSidebar');

        /* Dashboard Controller */
        Route::get('private/dashboard/getRegisteredUsers', ['as' => 'dashboard.getRegisteredUsers', 'uses' => 'DashboardController@getRegisteredUsers']);
        Route::get('private/dashboard/getLoggedInUsers', ['as' => 'dashboard.getLoggedInUsers', 'uses' => 'DashboardController@getLoggedInUsers']);
        Route::get('private/dashboard/ideas', ['as' => 'dashboard.ideas', 'uses' => 'DashboardController@ideas']);
        Route::get('private/dashboard/comments', ['as' => 'dashboard.comments', 'uses' => 'DashboardController@comments']);
        Route::get('private/dashboard/votes', ['as' => 'dashboard.votes', 'uses' => 'DashboardController@votes']);
        Route::get('private/dashboardVotes/proposals/{cbKey?}', ['as' => 'dashboard.proposals','uses' => 'DashboardVotesController@proposals'] );
        Route::get('private/empavilleDashboard/{cbKey?}', ['as' => 'empavilleDashboard.proposals','uses' => 'EmpavilleDashboardController@proposals'] );
        Route::get('private/empavilleTotals/{cbKey?}', ['as' => 'empavilleDashboard.totals','uses' => 'EmpavilleDashboardController@totals'] );
        Route::get('private/empavilleByGender/{cbKey?}', ['as' => 'empavilleDashboard.byGender','uses' => 'EmpavilleDashboardController@byGender'] );
        Route::get('private/empavilleGeoArea/{cbKey?}', ['as' => 'empavilleDashboard.geoArea','uses' => 'EmpavilleDashboardController@byGeoArea'] );
        Route::get('private/empavilleProfessions/{cbKey?}', ['as' => 'empavilleDashboard.professions','uses' => 'EmpavilleDashboardController@proposalsByProfession'] );
        Route::get('private/empavilleByChannel/{cbKey?}', ['as' => 'empavilleDashboard.channels','uses' => 'EmpavilleDashboardController@byChannel'] );
        Route::get('private/empavilleByAge/{cbKey?}', ['as' => 'empavilleDashboard.age','uses' => 'EmpavilleDashboardController@byAge'] );
        /* ---- END Dashboard Controller ---- */

        /* Kiosks Controller */
        Route::post('private/kiosk/{kioskKey}/proposals/store', ['as' => 'kiosk.default', 'uses' => 'KiosksController@storeProposals']);
        Route::post('private/kiosk/getVoteEvents', 'KiosksController@getVoteEvents');
        Route::get('private/kiosk/getVoteOptions', 'KiosksController@getVoteOptions');
        Route::get('private/kiosk/{kioskKey}/download', 'KiosksController@download');
        Route::post('private/kiosk/{kioskKey}/updateProposalOrder', 'KiosksController@updateProposalOrder');
        Route::get('private/kiosk/{kioskKey}/proposal/{id}/deleteProposal', ['as' => 'kiosk.defaultConfirm', 'uses' => 'KiosksController@deleteProposal']);
        Route::delete('private/kiosk/{kioskKey}/proposal/{id}/destroyProposal', ['as' => 'kiosk.default', 'uses' => 'KiosksController@destroyProposal']);
        Route::get('private/kiosk/{kioskKey}/proposal/{proposalKey}/addAction', ['as' => 'kiosk.default', 'uses' => 'KiosksController@addProposalAction']);
        Route::get('private/kiosk/{kioskKey}/proposal/add', ['as' => 'kiosk.addProposal', 'uses' =>  'KiosksController@addProposal']  );
        Route::get('private/kiosk/{kioskKey}/tableAddProposal', 'KiosksController@tableAddProposal');
        Route::get('private/kiosk/getIndexTable', 'KiosksController@getIndexTable');
        Route::get('private/kiosk/{idea}/delete', ['as' => 'kiosks.destroy', 'uses' => 'KiosksController@delete']);
        Route::resource('private/kiosk', 'KiosksController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Kiosks Controller ---- */


        Route::group(['middleware' => ['privateAuthOne'], 'name' => 'poll'], function () {
            /* Event Schedule Controller */
            Route::put('private/eventSchedule/{key}/updatePeriods', 'EventSchedulesController@updatePeriods');
            Route::any('private/eventSchedule/{key}/updatePeriods', 'EventSchedulesController@updatePeriods');
            Route::get('private/eventSchedule/{key}/showPeriods', ['as' => 'eventSchedule.showPeriods', 'uses' => 'EventSchedulesController@showPeriods']);
            Route::get('private/eventSchedule/{key}/editPeriods', ['as' => 'eventSchedule.editPeriods', 'uses' => 'EventSchedulesController@editPeriods']);
            Route::put('private/eventSchedule/attendance/{key}', ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@updateAttendance']);
            Route::post('private/eventSchedule/attendance/{key}', ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@storeAttendance']);
            Route::get('private/eventSchedule/deleteAttendance/{eventKey}/{key}', ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@deleteAttendance']);
            Route::delete('private/eventSchedule/deleteAttendance/{eventKey}/{key}', ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@destroyAttendance']);
            Route::get('private/eventSchedule/attendance/{key}', ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@attendance']);
            Route::get('private/eventSchedule/table', 'EventSchedulesController@getIndexTable');
            Route::get('private/eventSchedule/{key}/delete', ['as' => 'eventSchedule.delete', 'uses' => 'EventSchedulesController@delete']);
            Route::resource('/private/eventSchedule', 'EventSchedulesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        });
	/* Second Cycle Controller */

        /* Event Schedule Controller */
        Route::put('private/eventSchedule/{key}/updatePeriods', 'EventSchedulesController@updatePeriods');
        Route::any('private/eventSchedule/{key}/updatePeriods', 'EventSchedulesController@updatePeriods');
        Route::get('private/eventSchedule/{key}/showPeriods', ['as' => 'eventSchedule.showPeriods', 'uses' => 'EventSchedulesController@showPeriods']);
        Route::get('private/eventSchedule/{key}/editPeriods', ['as' => 'eventSchedule.editPeriods', 'uses' => 'EventSchedulesController@editPeriods']);
        Route::put('private/eventSchedule/attendance/{key}', ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@updateAttendance']);
        Route::post('private/eventSchedule/attendance/{key}', ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@storeAttendance']);
        Route::get('private/eventSchedule/deleteAttendance/{eventKey}/{key}', ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@deleteAttendance']);
        Route::delete('private/eventSchedule/deleteAttendance/{eventKey}/{key}', ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@destroyAttendance']);
        Route::get('private/eventSchedule/attendance/{key}',  ['as' => 'eventSchedule.attendance', 'uses' => 'EventSchedulesController@attendance']);
        Route::get('private/eventSchedule/table', 'EventSchedulesController@getIndexTable');
        Route::get('private/eventSchedule/{key}/delete', ['as' => 'eventSchedule.delete', 'uses' => 'EventSchedulesController@delete']);
        Route::resource('/private/eventSchedule', 'EventSchedulesController',['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* Second Cycle Controller */


        Route::get('private/second_cycle/{cbKey}/init', 'SecondCycleController@initialize');
        Route::get('private/second_cycle/{type}/{cbKey}/manage', [ 'as' => 'secondCycle.manage', 'uses' => 'SecondCycleController@manage']);
        Route::get('private/second_cycle/{type}/{cbKey}/manageCb', [ 'as' => 'secondCycle.manage', 'uses' => 'SecondCycleController@manageCb']);
        Route::get('private/second_cycle/{cbKey}/update_files/{cbKeyChild}', 'SecondCycleController@update_files');
        Route::get('private/second_cycle/{cbKey}/create/{level}/{parentTopicKey?}', ['as' => 'secondCycle.manage','uses' => 'SecondCycleController@create']);
        Route::get('private/second_cycle/{cbKey}/edit/{topicKey}', ['as' => 'secondCycle.edit', 'uses' =>'SecondCycleController@edit']);
        Route::get('private/second_cycle/{cbKey}/show/{topicKey}', ['as' => 'secondCycle.show', 'uses' =>'SecondCycleController@internalShow']);
        Route::get('private/second_cycle/{cbKey}/delete/{topicKey}', 'SecondCycleController@delete');
        Route::delete('private/second_cycle/{cbKey}/destroy/{topicKey}', 'SecondCycleController@destroy');
        Route::post('private/second_cycle/{cbKey}/store/{level}/{parentTopicKey?}', 'SecondCycleController@store');
        Route::post('private/second_cycle/{cbKey}/update/{topicKey}', 'SecondCycleController@update');

        /* ---- END Second Cycle Controller ---- */


//        /* Question Controller */
//
//
//        Route::get('/questionnaire/intro', ['as' => 'questionnaire.intro', 'uses' => 'PublicQController@intro']);
//        Route::get('/questionnaire/{id}', ['as' => 'questionnaire.showQ', 'uses' => 'PublicQController@showQ']);
//        Route::resource('/questionnaire', 'PublicQController');
//        /* END Question Controller */


        /*  Topics Review Replies*/
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/showTopicReviewReplies/{topicReviewKey}', 'TopicReviewRepliesController@index')->name('topicReviewReply.index');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/topicReview/{topicReviewKey}/topicReviewReply/{topicReviewReplyKey}/delete', 'TopicReviewRepliesController@delete')->name('topicReviewReply.destroy');
        Route::resource('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/topicReview/{topicReviewKey}/topicReviewReply', 'TopicReviewRepliesController',['only' => ['show', 'edit', 'update', 'store', 'destroy','create']]);

        /*  END Topics Review Replies */


        /*  Topics Review */

        Route::post('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/topicReview/getReviewsFromUser', ['as' => 'topic.getReviewsFromUser', 'uses' => 'TopicReviewsController@getReviewsFromUser']);
        Route::get('private/type/{type}/cbs/{cbKey}/showTopicReviews/{topicKey}', ['as' => 'topic.showTopicReviews', 'uses' => 'TopicReviewsController@index']);
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/topicReviewUsersTable', 'TopicReviewsController@topicReviewUsersTable');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/topicReviewGroupsTable', 'TopicReviewsController@topicReviewGroupsTable');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/topicReview/{topicReviewKey}/delete', 'TopicReviewsController@delete')->name('topicReview.destroy');
        Route::resource('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/topicReview', 'TopicReviewsController');

        /*  END Topics Review */

        //Accesses
        Route::get('private/access/tableAccesses', ['as' => 'accesses.showAccesses', 'uses' => 'AccessesController@tableAccesses']);
        Route::get('private/access/analytic/{entityKey?}', ['as' => 'analytic.analytic', 'uses' => 'AccessesController@analytic']);
        Route::get('private/access/analyticEntityKey', ['as' => 'analytic.analyticEntityKey', 'uses' => 'AccessesController@analyticEntityKey']);
        Route::resource('/private/access', 'AccessesController');
        /* END OF ACCESSES */

        //Gamification
        Route::resource('/private/gamification', 'GamificationsController', ['only' =>'index']);
        /* END OF Gamification */

        /* Topics Controller */
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/status/{status}/{version}', 'TopicController@changeActiveVersionStatus')->where(["newStatus"=>"0|1"]);
        Route::get('private/type/{type}/cbs/{cbKey}/entityUsers', 'TopicController@entityUsers');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/showCooperatorsTable', 'TopicController@showCooperatorsTable');
        Route::post('private/topic/{topicKey}/addCooperator', 'TopicController@addCooperator');
        Route::put('private/topic/{topicKey}/updateCooperatorPermission', 'TopicController@updateCooperatorPermission');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/deleteCooperator/{userKey}', 'TopicController@deleteCooperator');
        Route::delete('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/{userKey}/destroyCooperator', 'TopicController@destroyCooperator');
        Route::get('private/type/{type}/cbs/{cbKey}/showCooperators/{topicKey}', ['as' => 'topic.showCooperators', 'uses' => 'TopicController@showCooperators']);
        Route::post('private/type/{type}/cbs/{cbKey}/pdfList', 'TopicController@pdfList');
        Route::post('private/type/{type}/cbs/{cbKey}/excel', 'TopicController@excel');
        Route::get('private/topic/getAbusesPrivate', 'TopicController@getAbusesPrivate');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/getAbuses', 'TopicController@getAbuses');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/getIndexTablePosts', 'TopicController@getIndexTablePosts');
        Route::get('private/type/{type}/cbs/{cbKey}/getIndexTable', 'TopicController@getIndexTable');
        Route::get('private/type/{type}/cbs/getFullTopicsTable', 'TopicController@getFullTopicsTable');
        Route::get('private/type/{type}/cbs/getTopicsTechnicalEvaluation', 'TopicController@getTopicsTechnicalEvaluation');

        Route::get('private/type/{type}/cbs/showTopic/{topicKey}', ['as' => 'topic.showTopic', 'uses' => 'TopicController@showTopic']);
        Route::get('private/type/{type}/cbs/{cbKey}/showPosts/{topicKey}', ['as' => 'topic.showPosts', 'uses' => 'TopicController@showPosts']);

        Route::match(["get","post"],'private/type/{type}/cbs/{cbKey}/getIndexTableStatus', 'TopicController@getIndexTableStatus');
        Route::post('private/type/{type}/cbs/{cbKey}/topic/updateStatus', 'TopicController@updateStatus');
        Route::post('private/type/cbs/topic/updateStatusTopic', 'TopicController@updateStatusTopic');
        Route::post('private/topic/statusHistory', 'TopicController@statusHistory');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/createWithUser', ["as" => "topic.withUser.create","uses" => 'TopicController@createWithUser']);
        Route::post('private/type/{type}/cbs/{cbKey}/topic/createWithUser', 'TopicController@storeWithUser');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/delete', ['as' => 'topic.destroy', 'uses' => 'TopicController@delete']);
        Route::resource('private/type/{type}/cbs/{cbKey}/topic', 'TopicController',["except"=>["show"]]);
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/version/{version?}', ['as' => 'topic.show', 'uses' => 'TopicController@show']);
        /* ---- END TOPICS Controller ---- */

        /* Posts Controller */
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/post/{postKey}/active/{value}/{redirect}', ['as' => 'post.block', 'uses' => 'PostController@active']);
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/post/{postKey}/blocked/{value}/{redirect}', ['as' => 'post.block', 'uses' => 'PostController@blocked']);
        // Route::match(['PUT', 'PATCH'], '/private/entities/{entityKey}/updateManager/{userKey}',['as' => 'entities.updateManager', 'uses' => 'EntitiesController@updateManager']);
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/post/{postKey}/delete', ['as' => 'post.destroy', 'uses' => 'PostController@delete']);
        Route::delete('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/post/{postKey}/destroy', 'PostController@destroy');
        /* ---- END POSTS Controller ---- */

        /* Post Manager Controller */
        Route::post('private/postManager/getIndexTable', 'PostManagerController@getIndexTable');
        Route::get('private/postManager/{postKey}/active/{value}', ['as' => 'post.block', 'uses' => 'PostManagerController@active']);
        Route::get('private/postManager/{postKey}/blocked/{value}', ['as' => 'post.block', 'uses' => 'PostManagerController@blocked']);
        // Route::match(['PUT', 'PATCH'], '/private/entities/{entityKey}/updateManager/{userKey}',['as' => 'entities.updateManager', 'uses' => 'EntitiesController@updateManager']);
        Route::get('private/postManager/{postKey}/delete', ['as' => 'post.destroy', 'uses' => 'PostManagerController@delete']);
        Route::delete('private/postManager/{postKey}/destroy', 'PostManagerController@destroy');
        Route::resource('private/postManager', 'PostManagerController');
        /* ---- END POSTS Controller ---- */

        /* Ideas Controller */

        Route::post('private/ideas/voteEvent/getMethodsData', 'CbsVoteController@getMethodsData');
        Route::post('private/ideas/voteEvent/getMethods', 'CbsVoteController@getMethods');
        Route::post('private/ideas/voteEvent/getMethodConfigurations', 'CbsVoteController@getMethodConfigurations');
        Route::post('private/ideas/voteEvent/getParameterTypes', 'CbsVoteController@getParameterTypes');

        /* ---- END Ideas Controller ---- */

        /* Idea parameter Controller */
        Route::post('private/ideas/parameters/getParameterOptions', 'CbsParametersController@getParameterOptions');
        Route::post('private/ideas/parameters/addParameterOptions', 'CbsParametersController@addParameterOptions');
        Route::post('/private/ideas/parameters/addImageMap', 'CbsParametersController@addImageMap');
        Route::get('private/type/{type}/cbs/{cbKey}/getIndexTableParameters', 'CbsParametersController@getIndexTableParameters');
        Route::get('private/type/{type}/cbs/{cbKey}/parameters/{paramId}/delete', ['as' => 'idea.parameter.destroy', 'uses' => 'CbsParametersController@delete']);
        Route::get('private/type/{type}/cbs/{cbKey}/parameters/getNewOptionFields', ['as' => 'idea.parameter.getNewOptionFields', 'uses' => 'CbsParametersController@getNewOptionFields']);
        Route::get('private/type/{type}/cbs/{cbKey}/parameters/getNewFields', ['as' => 'idea.parameter.getNewFields', 'uses' => 'CbsParametersController@getNewFields']);
        Route::post('private/type/{type}/cbs/{cbKey}/parameters/getParameterType', ['as' => 'idea.parameter.getParameterType', 'uses' => 'CbsParametersController@getParameterType']);
        Route::resource('private/type/{type}/cbs/{cbKey}/parameters', 'CbsParametersController');

        /*  END Ideas parameter Controller */

        /* Idea vote Controller */
        Route::post('private/vote/{voteEventKey}/getStatistics', 'CbsVoteController@getStatistics');



        Route::get('private/type/{type}/cbs/{cbKey}/vote/{voteEventKey}/statistics',['as' => 'cb.vote.statistics', 'uses' => 'CbsVoteController@voteEventStatistics']);

        /** [BEGIN] DEAL WITH IN PERSON VOTING**/
        Route::get('private/type/{type}/cbs/{cbKey}/vote/{voteEventKey}/registerInPersonVoting', ['as' => 'cb.vote.registerInPersonVoting', 'uses' => 'CbsVoteController@registerInPersonVoting']);
        Route::get('private/type/{type}/cbs/{cbKey}/vote/{voteEventKey}/inPersonRegistration', ['as' => 'cb.vote.inPersonRegistration', 'uses' => 'UsersController@inPersonRegistration']);

        Route::post('private/user/saveInPersonRegistration', ['as' => 'cb.vote.saveInPersonRegistration', 'uses' => 'UsersController@saveInPersonRegistration']);
        Route::post('private/vote/saveInPersonVotes', ['as' => 'cb.vote.saveInPersonVotes', 'uses' => 'CbsVoteController@saveInPersonVotes']);
        Route::post('private/vote/replaceUserVotesWithInPersonVotes', ['as' => 'cb.vote.replaceUserVotesWithInPersonVotes', 'uses' => 'CbsVoteController@replaceUserVotesWithInPersonVotes']);
        Route::post('private/vote/publicUserVotingRegistrationStoreVotes', ['as' => 'cb.vote.publicUserVotingRegistrationStoreVotes', 'uses' => 'CbsVoteController@publicUserVotingRegistrationStoreVotes']);

        /** [END] DEAL WITH IN PERSON VOTING**/

        Route::get('private/type/{type}/cbs/{cbKey}/getIndexTableVote', 'CbsVoteController@getIndexTableVote');
        Route::get('private/type/{type}/cbs/{cbKey}/voteEvent/{voteKey}/getIndexTableVoteList', 'CbsVoteController@getIndexTableVoteList');
        Route::get('private/type/{type}/cbs/{cbKey}/voteEvent/{voteKey}/delete', ['as' => 'idea.vote.destroy', 'uses' => 'CbsVoteController@delete']);
        Route::get('private/type/{type}/cbs/{cbKey}/voteEvent/{voteKey}/deleteVote', ['as' => 'idea.vote.delete', 'uses' => 'CbsVoteController@deleteVote']);

        Route::get('private/type/{type}/cbs/{cbKey}/vote/{voteKey}/mapVotesToParameter', ['as' => 'private.cbs.mapVotesToParameter.create', 'uses' => 'CbsVoteController@mapVotesToParameter']);
        Route::post('private/type/{type}/cbs/{cbKey}/vote/{voteKey}/mapVotesToParameter', ['as' => 'private.cbs.mapVotesToParameter.store', 'uses' => 'CbsVoteController@mapVotesToParameterSubmit']);
        Route::get('private/type/{type}/cbs/{cbKey}/vote/weightVote', 'CbsVoteController@weightVote');
        Route::get('private/type/{type}/cbs/{cbKey}/vote/{voteKey}/voteList', ['as' => 'private.cbs.voteList', 'uses' => 'CbsVoteController@voteList']);
        Route::get('private/type/{type}/cbs/{cbKey}/vote/{voteKey}/submitVote', 'CbsVoteController@submitVote');
        Route::post('private/type/{type}/cbs/{cbKey}/vote/{voteKey}/voteId/{voteId}/submitUserVote', 'CbsVoteController@submitUserVote');
        Route::delete('private/type/{type}/cbs/{cbKey}/vote/{voteKey}/voteId/{voteId}/deleteUserVote', 'CbsVoteController@deleteUserVote');
        Route::resource('private/type/{type}/cbs/{cbKey}/vote', 'CbsVoteController');

        /*  END Ideas vote Controller */

        /* ConferenceEvents Controller */
        Route::get('private/conferenceEvents/getIndexTable', 'ConferenceEventsController@getIndexTable');
        Route::get('private/conferenceEvents/{eventKey}/delete', 'ConferenceEventsController@delete');
        Route::get('private/conferenceEvents/{eventKey}/getFile', 'ConferenceEventsController@getEventFile');
        Route::post('/private/conferenceEvents/addEventImage', 'ConferenceEventsController@addEventImage');
        Route::resource('private/conferenceEvents', 'ConferenceEventsController');
        /* END ConferenceEvents Controller */

        /* ConferenceEventSession Controller*/
        Route::get('private/conferenceEvents/{eventKey}/session/getIndexTable', 'ConferenceEventSessionController@getIndexTable');
        Route::get('private/conferenceEvents/{eventKey}/session/{sessionKey}/delete', 'ConferenceEventSessionController@delete');
        Route::resource('private/conferenceEvents/{eventKey}/session', 'ConferenceEventSessionController');

        /* END ConferenceEventSession Controller*/

        /* ConferenceEventSpeaker Controller*/
        Route::get('private/conferenceEvents/{eventKey}/session/{sessionKey}/speaker/getIndexTable', 'ConferenceEventSpeakerController@getIndexTable');
        Route::get('private/conferenceEvents/{eventKey}/session/{sessionKey}/speaker/{speakerKey}/delete', 'ConferenceEventSpeakerController@delete');
        Route::resource('private/conferenceEvents/{eventKey}/session/{sessionKey}/speaker', 'ConferenceEventSpeakerController');

        /* END ConferenceEventSpeakerController*/

        /* ConferenceEventSponsors Controller*/
        Route::get('private/conferenceEvents/{eventKey}/sponsor/getIndexTable', 'ConferenceEventSponsorsController@getIndexTable');
        Route::get('private/conferenceEvents/{eventKey}/sponsor/{sponsorKey}/delete', 'ConferenceEventSponsorsController@delete');
        Route::get('private/conferenceEvents/sponsor/{sponsorKey}/getFile', 'ConferenceEventSponsorsController@getFile');
        Route::post('/private/conferenceEvents/sponsor/addImageSponsor', 'ConferenceEventSponsorsController@addImageSponsor');
        Route::resource('private/conferenceEvents/{eventKey}/sponsor', 'ConferenceEventSponsorsController');

        /* END ConferenceEventSponsorsController*/

        /* Moderation Controller*/

        Route::post('private/moderation/getComments', 'ModerationController@ajaxShowComments');

        Route::get('private/moderation/topicsTable', 'ModerationController@getAllTopicsTable');
        Route::get('private/moderation/postsTable', 'ModerationController@getPostsToModerate');
        Route::get('private/moderation/topics', 'ModerationController@topicsToModerate')->name('moderation.topicsToModerate');
        Route::get('private/moderation/posts', 'ModerationController@postsToModerate')->name('moderation.postsToModerate');

        /* END Moderation Controller*/

        Route::group(['middleware' => ['privateAuthOne'], 'name' => 'cbs'], function (){
            Route::get('private/deleteLine', 'TranslationsController@deleteLine');
            Route::get('private/exportTranslations', 'TranslationsController@exportTranslations');
            Route::post('private/importTranslations', 'TranslationsController@importTranslations');
            Route::resource('private/translation', 'TranslationsController');

            Route::post('private/cb/setTranslation', ['as' => 'private.cb.translations', 'uses' => 'CbsController@setTranslation']);
            Route::get('private/cb/translations', ['as' => 'private.cb.translations', 'uses' => 'CbsController@translations']);
            /* Cbs Controller */
            Route::get('private/wizard/cb/{type?}', ['as' => 'private.wizard.cb', 'uses' => 'CbsController@createWizard']);
            Route::post('private/wizard/cb/{type?}', ['as' => 'private.wizard.cb', 'uses' => 'CbsController@storeWizard']);
            Route::get('private/type/{type}/cbs/{cb_key}/analysis/getVotesSummaryTable', 'CbsController@getVotesSummaryTable');

            /** Export topics routes */
            Route::get('private/type/{type}/pads/{pad_key}/export', 'CbsController@showExportTopics')->name('cbs.exportTopics.show');
            Route::get('private/type/{type}/pads/{pad_key}/topicsToExport', 'CbsController@topicsToExport')->name('cbs.exportTopics.topicsToExport');
            Route::post('private/type/{type}/pads/{pad_key}/export', 'CbsController@exportTopics')->name('cbs.exportTopics.store');

            Route::post('private/pads/mappingParams', 'CbsController@mappingParams')->name('cbs.exportTopics.mappingParams');
            Route::post('private/pads/mappingParamOptions', 'CbsController@mappingParamOptions')->name('cbs.exportTopics.mappingParamOptions');
            /** END Export topics routes */

//        --------------------------------

            Route::get('private/type/{type}/cbs/{cb_key}/showTemplate/{configuration_code}', 'CbsController@showNotificationEmailTemplate')->name('cbs.notificationTemplate.show');
            Route::get('private/type/{type}/cbs/{cb_key}/editTemplate/{configuration_code}', 'CbsController@private/menus/')->name('cbs.notificationTemplate.edit');
            Route::get('private/type/{type}/cbs/{cb_key}/createTemplate/{configuration_code}', 'CbsController@createNotificationEmailTemplate')->name('cbs.notificationTemplate.create');
            Route::put('private/type/{type}/cbs/{cb_key}/updateTemplate/{configuration_code}', 'CbsController@updateNotificationEmailTemplate')->name('cbs.notificationTemplate.update');
            Route::post('private/type/{type}/cbs/{cb_key}/updateTemplate/{configuration_code}', 'CbsController@storeNotificationEmailTemplate')->name('cbs.notificationTemplate.store');

//        --------------------------------

            Route::post('private/cbs/addModalVote', 'CbsController@addModalVote');
            Route::post('private/cbs/addModalParameter', 'CbsController@addModalParameter');
            Route::post('private/cbs/addParameterTemplateSelection', 'CbsController@addParameterTemplateSelection');
            Route::post('private/cbs/addParameterTemplate', 'CbsController@addParameterTemplate');
            Route::post('private/cbs/getParameter', 'CbsController@getParameter');
            Route::post('private/cbs/addVote', 'CbsController@addVote');
            Route::post('private/cbs/addParameter', 'CbsController@addParameter');
            Route::post('private/cbs/getAllTemplates', 'CbsController@getAllTemplates');
            Route::post('private/cbs/getListOfCbsByType', 'CbsController@getListOfCbsByType');
            Route::post('private/cbs/getListOfTopicsByCb', 'CbsController@getListOfTopicsByCb');
            Route::post('private/cbs/getUsers', 'CbsController@getUsers');
            Route::post('private/cbs/availableStatuses', 'CbsController@availableStatuses');
            Route::get('private/type/{type}/cbs/getCbTemplate', ['as' => 'private.cbs.getCbTemplate', 'uses' => 'CbsController@getCbTemplate']);
            Route::get('private/cbs/indexManager', ['as' => 'private.cbs.index_manager', 'uses' => 'CbsController@indexManager']);
            Route::get('private/cbs/getIndexTable', 'CbsController@getIndexTable');
            Route::post('private/cbs/getActivePads', 'CbsController@getActivePads');
            Route::post('private/cbs/getVoteAnalysis', 'CbsController@getVoteAnalysis' )->name('cbs.voteAnalysis');
            Route::get('private/type/{type}/cbs/getDetailsView', ['as' => 'private.cbs.getDetailsView', 'uses' => 'CbsController@getDetailsView']);

            Route::post('private/type/{type}/cbs/updateCb', ['as' => 'private.cbs.updateCb', 'uses' => 'CbsController@updateCb']);
            Route::get('private/type/{type}/cbs/{cb_key}/comments', 'CbsController@showCbComments')->name('cbs.comments');
            Route::match(["get","post"],'private/type/{type}/cbs/{cb_key}/getAllComments', 'CbsController@getAllComments')->name('cbs.commentsTable');
            Route::get('private/type/{type}/cbs/{cb_key}/analysis', 'CbsController@voteAnalysis')->name('cbs.analysis');
            Route::get('private/type/{type}/cbs/{cb_key}/empavilleAnalysis', 'CbsController@voteAnalysisEmpaville')->name('cbs.empavilleAnalysis');
            Route::delete('private/type/{type}/cbs/{cbId}/deleteModerator/{id}', 'CbsController@deleteModerator');
            Route::get('private/type/{type}/cbs/{cbId}/deleteModeratorConfirm/{id}', ['as' => 'private.cbs.deleteModerator.confirm', 'uses' => 'CbsController@deleteModeratorConfirm']);
            Route::get('private/type/{type}/cbs/{cbId}/allManagers', ['as' => 'private.cbs.allManagers', 'uses' => 'CbsController@allManagers']);
            Route::get('private/type/{type}/cbs/allUsers/{cbId?}', ['as' => 'private.cbs.allUsers', 'uses' => 'CbsController@allUsers']);
            Route::get('private/type/{type}/cbs/{cbId}/delete', 'CbsController@delete');
            Route::get('private/type/{type}/cbs/{cbId}/showTopics', ['as' => 'private.cbs.showTopics', 'uses' => 'CbsController@showTopics']);
            Route::get('private/type/{type}/cbs/{cbId}/showParameters', ['as' => 'private.cbs.showParameters', 'uses' => 'CbsController@showParameters']);
            Route::get('private/type/{type}/cbs/{cbId}/showVotes', ['as' => 'private.cbs.showVotes', 'uses' => 'CbsController@showVotes']);
            Route::get('private/type/{type}/cbs/{cbId}/showModerators', ['as' => 'private.cbs.showModerators', 'uses' => 'CbsController@showModerators']);
            Route::get('private/type/{type}/cbs/{cbId}/show/cbsConfigurations', 'CbsController@showConfigurations')->name('private.cbsConfigurations.show');
            Route::get('private/type/{type}/cbs/{cbId}/edit/cbsConfigurations', 'CbsController@editConfigurations')->name('private.cbsConfigurations.edit');
            Route::get('private/type/{type}/cbs/{cb_key}/showUsers', 'CbsController@showUsers')->name('cbs.showUsers');
            Route::get('private/type/{type}/cbs/{cb_key}/cbsPermissions', 'CbsController@permissions')->name('cbs.permission');
            Route::post('private/type/{type}/cbs/{cb_key}/updatePermission', 'CbsController@updatePermission');
//        -----------------------------------------------------
            Route::get('private/type/{type}/cbs/{cbId}/show/cbsNotifications', 'CbsController@showNotifications')->name('private.cbsNotifications.show');
            Route::get('private/type/{type}/cbs/{cbId}/edit/cbsNotifications', 'CbsController@editNotifications')->name('private.cbsNotifications.edit');
            Route::get('private/type/{type}/cbs/{cbId}/edit/{configuration_code}/editNotificationEmailTemplate', 'CbsController@editNotificationEmailTemplate');
//        -----------------------------------------------------


            Route::post('private/type/{type}/cbs/{cbId}/addModerator', 'CbsController@addModerator');
            Route::post('private/type/{type}/cbs/{cbId}/storeTemplate', 'CbsController@storeCbTemplate');
            Route::get('private/type/{type}/cbs/{cbId}/showGroupPermissions', ['as' => 'private.cbs.showGroupPermissions', 'uses' => 'CbsController@showGroupPermissions']);
            Route::get('private/type/{type}/cbs/{cbId}/showPermissions', ['as' => 'private.cbs.showPermissions', 'uses' => 'CbsController@showPermissions']);
            Route::post('private/type/{type}/cbs/{cbId}/getGroupsPads', ['as' => 'private.cbs.getGroupsPads', 'uses' => 'CbsController@getGroupsPads']);
            Route::post('private/type/{type}/cbs/{cbId}/storePermissions', ['as' => 'private.cbs.storePermissions', 'uses' => 'CbsController@storePermissions']);
            Route::get('private/type/{type}/cbs/moderateRouting/{action}/{step}', 'CbsController@moderateRouting');
            Route::get('private/type/{type}/cbs/{cbId}/advancedEdit', ['as' => 'private.cbs.deleteModerator.advancedEdit', 'uses' => 'CbsController@advancedEdit']);
            Route::get('private/type/{type}/cbs/{cbId}/show/cbsSecurityConfigurations', 'CbsController@showSecurityConfigurations')->name('private.securityConfigurations.show');
            Route::get('private/type/{type}/cbs/{cbId}/edit/cbsSecurityConfigurations', 'CbsController@editSecurityConfigurations')->name('private.securityConfigurations.edit');
            Route::match(['PUT', 'PATCH'],'/private/type/{type}/cbs/{cbId}/update/cbsSecurityConfigurations',['as' => 'private.securityConfigurations.update', 'uses' => 'CbsController@updateSecurityConfigurations']);

            Route::get('private/type/{type}/cbs/{cbKey}/show/showQuestionnaires', 'CbsController@showQuestionnaires')->name('private.cbsQuestionnaires.show');
            Route::get('private/type/{type}/cbs/{cbKey}/edit/editQuestionnaires', 'CbsController@editQuestionnaires')->name('private.cbsQuestionnaires.edit');
            Route::match(['PUT', 'PATCH'],'/private/type/{type}/cbs/{cbKey}/update/questionnaire',['as' => 'private.cbsQuestionnaires.update', 'uses' => 'CbsController@updateQuestionnaires']);
//        Route::get('private/type/{type}/cbs/{cbKey}/action/{actionCode}/showQuestionnaireTemplate', ['as' => 'private.questionnaireTemplate.show', 'uses' => 'CbsController@showQuestionnaireTemplate']);
            Route::get('private/type/{type}/cbs/{cbKey}/action/{actionCode}/editQuestionnaireTemplate', ['as' => 'private.questionnaireTemplate.edit', 'uses' => 'CbsController@editQuestionnaireTemplate']);
            Route::match(['PUT', 'PATCH'],'/private/type/{type}/cbs/{cbKey}/action/{actionCode}/updateQuestionnaireTemplate',['as' => 'private.questionnaireTemplate.update', 'uses' => 'CbsController@updateQuestionnaireTemplate']);

            Route::post('private/cbs/questionnaires/updateCbQuestionnaireTranslations','CbsController@updateQuestionnaireTemplate')->name('private.questionnaireTemplate.update');
            Route::post('private/cbs/questionnaires/questionnaireTranslationsModal', 'CbsController@editQuestionnaireTemplate')->name('private.questionnaireTemplate.edit');

            Route::get('private/type/{type}/cbs/{cbId}/duplicateCb', 'CbsController@duplicate')->name('private.cbs.duplicate');
            Route::get('private/cbs/stepType', 'CbsController@stepType')->name('private.cbs.stepType');
            Route::get('private/cbs/updateChecklistItem', 'CbsController@updateChecklistItem');
            Route::get('private/cbs/createChecklistItem', 'CbsController@createChecklistItem');
            Route::get('private/cbs/addCheckList', 'CbsController@addCheckList');
            Route::get('private/cbs/removeCheckListItem', 'CbsController@removeCheckListItem');
            Route::get('private/type/{type}/cbs/categoryFilter', 'CbsController@categoryFilter');
            Route::resource('private/type/{type}/cbs', 'CbsController');

            /* --- END Cbs Controller --- */
        });

        /* Cb Config types Controller */
        Route::get('private/cbConfigType/{id}/showConfigurations', 'CbsConfigTypesController@showConfigurations')->name('private.cbsConfigTypes.show');;
        Route::get('private/cbConfigType/getIndexTable', 'CbsConfigTypesController@getIndexTable');
        Route::get('private/cbConfigType/{id}/delete', 'CbsConfigTypesController@delete');
        Route::resource('private/cbConfigType', 'CbsConfigTypesController');

        /* --- END Cb Config types Controller --- */

        /* Cb Config Controller */
        Route::get('/private/configType/{configTypeId}/getIndexTable', 'CbsConfigsController@getIndexTable');
        Route::get('/private/configType/{configTypeId}/config/{id}/delete', 'CbsConfigsController@delete');
        Route::resource('/private/configType/{configTypeId}/config', 'CbsConfigsController');
        /* ---- END Cb Config Controller ---- */



        /* USER Controller */
 Route::group(['middleware' => ['privateAuthOne'], 'name' => 'user'], function () {
     Route::get('private/user/{userKey}/manualCheckLoginLevel', ['as' => 'users.moderatUserLoginLevel', 'uses' => 'UsersController@manualCheckUserLoginLevel']);
        Route::post('private/user/{userKey}/manualCheck/{login_level_key}', ['as' => 'users.moderatUser', 'uses' => 'UsersController@manualCheckLoginLevel']);
        Route::get('private/user/{userKey}/moderate/{siteKey}', ['as' => 'users.moderatUser', 'uses' => 'UsersController@moderateUser']);
        Route::get('private/user/completed/{userKey}/updateStatus/{status}/confirm/{redirect}', ['as' => 'users.updateStatus', 'uses' => 'UsersController@updateStatusConfirmCompleted']);
        Route::get('private/user/completed/{userKey}/updateStatus/{status}/update/', ['as' => 'users.updateStatus', 'uses' => 'UsersController@updateStatusCompleted']);
        //Route::get('private/user/completed/{userKey}/updateStatus/{status}/update/', ['as' => 'users.updateStatus', 'uses' => 'UsersController@updateStatusCompleted']);
        //Route::get('private/user/tableUsersCompletedQuickView/{value}', 'UsersController@tableUsersCompletedQuickView');
        Route::get('private/user/tableUsersCompletedQuickView/', 'UsersController@tableUsersCompletedQuickView');
        Route::get('private/user/tableUsersCompleted/', 'UsersController@tableUsersCompleted');

        Route::get('private/user/index/completed', ['as' => 'users.indexCompleted', 'uses' => 'UsersController@indexCompleted']);
        Route::get('private/user/{userKey}/updateStatus/{status}/confirm/{role}', ['as' => 'users.updateStatus', 'uses' => 'UsersController@updateStatusConfirm']);
        Route::get('private/user/{userKey}/updateStatus/{status}/update/{role}', ['as' => 'users.updateStatus', 'uses' => 'UsersController@updateStatus']);
        Route::get('private/user/{userKey}/destroy', 'UsersController@destroy');
        Route::get('private/user/{user}/delete', ['as' => 'users.delete', 'uses' => 'UsersController@delete']);
        Route::post('private/user/excel', 'UsersController@excel');
        Route::post('private/user/pdfList', 'UsersController@pdfList');
        Route::delete('private/user/anonymizeUsers', 'UsersController@anonymizeUsers');
        Route::get('private/user/table/', 'UsersController@tableUsers');
        Route::get('private/user/tableRolesUser/{type}', 'UsersController@tableRolesUser');
        Route::get('private/user/entityUsers', 'UsersController@tableUsersManager');
        Route::get('private/user/entityUsersMan', 'UsersController@tableUsersManagerMan');
        Route::get('private/user/showProfile', ['as' => 'user.profile', 'uses' => 'UsersController@showProfile']);
        Route::get('private/user/edit', ['as' => 'profile.edit', 'uses' => 'UsersController@editProfile']);
        Route::get('private/user/{user}/showReadOnly', ['as' => 'profile.showReadOnly', 'uses' => 'UsersController@showReadOnly']);
        Route::get('private/user/updateRole/{userKey}/{entityKey}', 'UsersController@updateRole');
        Route::get('private/user/updateRoleMan/{userKey}/{entityKey}', 'UsersController@updateRoleMan');

        Route::get('private/user/messages/{userKey?}/showUserMessages', ['as' => 'users.showUserMessages', 'uses' => 'UsersController@showUserMessages']);
        Route::get('private/user/messages/markMessagesAsUnseen', ['as' => 'users.markMessagesAsUnseen', 'uses' => 'UsersController@markMessagesAsUnseen']);

        Route::get('private/sendMessageToAllUsers',['as'=>'messageToAll.create','uses'=>'UsersController@createMessageToAll']);
        Route::post('private/sendMessageToAllUsers',['as' => 'messageToAll.store', 'uses' => 'UsersController@sendMessageToAll']);
        Route::get('/private/sendMessageToAllUsers/getTinyMCE', 'UsersController@getTinyMCE');
        Route::get('/private/sendMessageToAllUsers/getTinyMCEView/{type?}', 'UsersController@getTinyMCEView');


        Route::post('private/user/{userKey}/updatePassword', 'UsersController@updatePassword');
        Route::get('private/user/{userKey}/manualGrantLoginLevel', 'UsersController@manualGrantLoginLevel');
        Route::get('private/user/{userKey}/manualRemoveLoginLevel', 'UsersController@manualRemoveLoginLevel');
        Route::get('private/user/{userKey}/tableUserLoginLevels', 'UsersController@tableUserLoginLevels');
        Route::get('private/user/{userKey}/tableManageUserLoginLevels', 'UsersController@tableManageUserLoginLevels');
        Route::get('private/user/{userKey}/checkAndUpdateUserLoginLevel', 'UsersController@checkAndUpdateUserLoginLevel');

            /* Dynamic BackOffice User Menu Routes */
            Route::get('private/user/{userKey}/BEUserMenu', "UserBEMenuController@userIndex")->name("user.BEUserMenu.index");
            Route::post('private/user/{userKey}/BEUserMenu', "UserBEMenuController@userStore")->name("user.BEUserMenu.create");
            Route::get('private/user/{userKey}/BEUserMenu/import', ['as' => 'user.BEUserMenu.import', 'uses' => 'UserBEMenuController@userImport']);
            Route::get('private/user/{userKey}/BEUserMenu/create', "UserBEMenuController@userCreate")->name("user.BEUserMenu.create");
            Route::post('/private/user/{userKey}/BEUserMenu/updateOrder', 'UserBEMenuController@userUpdateOrder');
            Route::get('private/user/{userKey}/BEUserMenu/{key}/delete', "UserBEMenuController@userDelete")->name("user.BEUserMenu.delete");
            Route::delete('private/user/{userKey}/BEUserMenu/{key}', "UserBEMenuController@userDestroy")->name("user.BEUserMenu.destroy");
            Route::get('private/user/{userKey}/BEUserMenu/{key}', "UserBEMenuController@userShow")->name("user.BEUserMenu.show");
            Route::patch('private/user/{userKey}/BEUserMenu/{key}', "UserBEMenuController@userUpdate")->name("user.BEUserMenu.update");
            Route::get('private/user/{userKey}/BEUserMenu/{key}/edit', "UserBEMenuController@userEdit")->name("user.BEUserMenu.edit");
            Route::delete('private/user/{userKey}/anonymize', "UsersController@anonymizeUser");

            Route::resource('private/users', 'UsersController');
        });
        /* ---- END USER Controller ---- */

        /* Entity Groups Permissions */
        Route::get('/private/user/{userKey}/showPermissions', ['as' => 'users.permissions', 'uses' => 'UsersController@showPermissions']);
        Route::Post('/private/user/{userKey}/createPermissions', ['as' => 'users.storePermissions', 'uses' => 'UsersController@storePermissions']);

        /* END Entity Groups Permissions */

        /* InPersonRegistration Controller */
        Route::post('/private/inPersonVoting/{userKey}', 'InPersonRegistrationController@voteSubmit');
        Route::get('/private/inPersonRegistration/{userKey}/voteInPerson', 'InPersonRegistrationController@voteInPerson');
        Route::get('/private/inPersonRegistration/getIndexTable', 'InPersonRegistrationController@getIndexTable');
        Route::get('/private/inPersonRegistration/{userKey}/delete', ['as' => 'inPersonRegistration.destroy', 'uses' => 'InPersonRegistrationController@delete']);
        Route::resource('private/inPersonRegistration', 'InPersonRegistrationController');
        /* ---- END InPersonRegistration Controller ---- */


        /* Menus Controller */
        Route::post('/private/menus/updateOrder', 'MenusController@updateOrder');
        Route::get('/private/menus/index/{accessM?}', ['as' => 'menus.index', 'uses' => 'MenusController@index']);
        Route::get('/private/menus/create/{accessMenu?}', ['as' => 'menus.create', 'uses' => 'MenusController@create']);
        Route::get('/private/menus/{menu}/delete', ['as' => 'menus.destroy', 'uses' => 'MenusController@delete']);
        Route::resource('/private/menus', 'MenusController', ['only' => ['show', 'edit', 'update', 'store', 'destroy']]);
        /* ---- END Menus Controller ---- */


        /* Access Menus Controller */
        Route::get('/private/accessMenus/table', 'AccessMenusController@tableAccessMenus');
        Route::get('/private/accessMenus/tableT', 'AccessMenusController@tableAccessTypes');
        Route::get('/private/accessMenus/{accessMenu}/delete', ['as' => 'accessMenu.destroy', 'uses' => 'AccessMenusController@delete']);
        Route::get('/private/accessMenus/{accessMenu}/activateConfirm', ['as' => 'accessMenu.activate', 'uses' => 'AccessMenusController@activateConfirm']);
        Route::get('/private/accessMenus/{accessMenu}/activate', ['as' => 'accessMenu.activate', 'uses' => 'AccessMenusController@activate']);
        Route::get('/private/accessMenus/{accessMenu}/showMenus', ['as' => 'accessMenu.showMenus', 'uses' => 'AccessMenusController@showMenus']);
        Route::post('/private/accessMenus/{accessMenu}/getSidebar2', ['as' => 'accessMenu.getSidebarAccessMenu', 'uses' => 'AccessMenusController@getSidebar2']);
        Route::resource('/private/accessMenus', 'AccessMenusController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END  Access Menus Controller ---- */

        /* Entities Controller */
        Route::get('private/wizard/entity', ['as' => 'private.wizard.entity', 'uses' => 'EntitiesController@createWizard']);
        Route::post('private/wizard/entity', 'EntitiesController@storeWizard');
        Route::match(['PUT', 'PATCH'], '/private/entities/{entityKey}/updateManager/{userKey}',['as' => 'entities.updateManager', 'uses' => 'EntitiesController@updateManager']);
        Route::get('/private/entities/{entityKey}/editManager/{userKey}',['as' => 'entities.edit', 'uses' => 'EntitiesController@editManager']);
        Route::get('/private/entities/{entityKey}/showManager/{userKey}',['as' => 'entities.show', 'uses' => 'EntitiesController@showManager']);
        Route::post('/private/entities/{entityKey}/storeManager',  ['as' => 'entities.create', 'uses' => 'EntitiesController@storeManager']);
        Route::get('/private/entities/{entityKey}/addManager',  ['as' => 'entities.create', 'uses' => 'EntitiesController@addManager']);
        Route::get('/private/entities/{entityKey}/createManager',  ['as' => 'entities.createManager', 'uses' => 'EntitiesController@createManager']);
        Route::get('/private/entities/{entity}/lang/{language_id}/addLanguage', ['as' => 'language.default', 'uses' => 'EntitiesController@addLanguageAction']);
        Route::get('/private/entities/{entity}/lang/{language_id}/deleteConfirm', ['as' => 'language.defaultConfirm', 'uses' => 'EntitiesController@deleteLangConfirm']);
        Route::delete('/private/entities/{entity}/lang/{language_id}/delete', ['as' => 'language.default', 'uses' => 'EntitiesController@deleteLang']);
        Route::get('/private/entities/{entity}/lang/{language_id}/makeDefaultConfirm', ['as' => 'language.defaultConfirm', 'uses' => 'EntitiesController@makeLangDefaultConfirm']);
        Route::get('/private/entities/{entity}/lang/{language_id}/makeLangDefault', ['as' => 'language.default', 'uses' => 'EntitiesController@makeLangDefault']);
        Route::get('/private/entities/{entity}/user/{language_id}/deleteConfirm', ['as' => 'language.defaultConfirm', 'uses' => 'EntitiesController@deleteUserConfirm']);
        Route::delete('/private/entities/{entity}/user/{user_key}/delete', ['as' => 'language.default', 'uses' => 'EntitiesController@deleteUser']);
        Route::get('/private/entities/{entity}/table/users', 'EntitiesController@tableUsersEntity');
        Route::get('/private/entities/{entityKey}/lang/add', ['as' => 'entities.addLang', 'uses' =>  'EntitiesController@addLanguage']  );
        Route::get('/private/entities/{entity}/table/languages', 'EntitiesController@tableLanguagesEntity');
        Route::get('/private/entities/{entity}/table/addLanguage', 'EntitiesController@tableAddLanguageEntity');

        Route::get('/private/entities/{entityKey}/authMethod/{authMethodKey}/addAuthMethod', 'EntitiesController@addAuthMethodAction');
        Route::delete('/private/entities/{entityKey}/authMethod/{authMethodKey}/delete', 'EntitiesController@deleteAuthMethod');
        Route::get('/private/entities/{entity}/table/addAuthMethod', ['as' => 'entities.addAuthMethod', 'uses' =>  'EntitiesController@addAuthMethod']);
        Route::get('/private/entities/{entity}/table/tableAuthMethod', 'EntitiesController@tableAuthMethod');
        Route::get('/private/entities/{entity}/table/tableAddAuthMethod', 'EntitiesController@tableAddAuthMethod');
        Route::get('/private/entities/{entityKey}/authMethod/{authMethodKey}/deleteConfirm', 'EntitiesController@deleteAuthMethodConfirm');

        Route::get('/private/entities/{entity}/table/tableEntityModule', 'EntitiesController@tableEntityModule');
        Route::post('/private/entities/{entity}/updateEntityModules', ['as' => 'entities.updateEntityModules', 'uses' =>  'EntitiesController@updateEntityModules']);
        Route::get('/private/entities/{entity}/table/addEntityModule', ['as' => 'entities.addEntityModule', 'uses' =>  'EntitiesController@addEntityModule']);

        Route::get('/private/entities/{entityKey}/table/layouts', 'EntitiesController@tableLayoutsEntity');
        Route::get('/private/entities/{entityKey}/layout/add', ['as' => 'entities.addLayout', 'uses' => 'EntitiesController@addLayout']);
        Route::get('/private/entities/{entityKey}/table/addLayout', 'EntitiesController@tableAddLayout');
        Route::get('/private/entities/{entityKey}/layout/{layoutKey}/deleteConfirm', 'EntitiesController@deleteLayoutConfirm');
        Route::get('/private/entities/{entityKey}/layout/{layoutKey}/addLayout', ['as' => 'entities.layout.add', 'uses' => 'EntitiesController@addLayoutAction']);
        Route::delete('/private/entities/{entityKey}/layout/{layoutKey}/delete', 'EntitiesController@deleteLayout');

        Route::get('/private/entities/table', 'EntitiesController@tableEntities');
        Route::get('/private/entities/{entity}/delete', 'EntitiesController@delete');
        Route::get('/private/entities/showEntity', 'EntitiesController@showEntity');
        Route::get('/private/entities/showSites/{entityKey}', ['as' => 'entities.showSites', 'uses' => 'EntitiesController@showSites']);
        Route::get('/private/entities/showLayouts/{entityKey}', ['as' => 'entities.showSites', 'uses' =>  'EntitiesController@showLayouts']);
        Route::get('/private/entities/showLanguages/{entityKey}', ['as' => 'entities.showLanguages', 'uses' =>  'EntitiesController@showLanguages']);
        Route::get('/private/entities/showManagers/{entityKey}', ['as' => 'entities.showManagers', 'uses' =>  'EntitiesController@showManagers']);
        Route::get('/private/entities/showModules/{entityKey}', ['as' => 'entities.showModules', 'uses' =>  'EntitiesController@showModules']);
        Route::get('/private/entities/showAuthMethods/{entityKey}', ['as' => 'entities.showAuthMethods', 'uses' =>  'EntitiesController@showAuthMethods']);
        Route::get('/private/entities/showSites/{entityKey}/showUseTerms/{siteKey}', ['as' => 'entities.showUseTerms', 'uses' =>  'EntitiesController@showUseTerms']);
        Route::get('/private/entities/showSites/{entityKey}/showHomePageConfigurations/{siteKey}', ['as' => 'entities.showHomePageConfigurations', 'uses' =>  'EntitiesController@showHomePageConfigurations']);
        Route::get('/private/entities/showSites/{entityKey}/showConfigurations/{siteKey}', ['as' => 'entities.showConfigurations', 'uses' =>  'EntitiesController@showConfigurations']);
        Route::post('/private/entities/setEntityKey', 'EntitiesController@setEntityKey');
        Route::post('/private/entities/{entityKey}/getSidebar', 'EntitiesController@getSidebar');
        Route::post('/private/entities/{entityKey}/getSidebarSites/{siteKey}', 'EntitiesController@getSidebarSites');


        Route::resource('/private/entities', 'EntitiesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Entities Controller ---- */

        /*Entities Controller (sites)*/
        Route::get('/private/entities/{entity}/deleteSiteConfirm/{siteKey}/', ['as' => 'entities.deleteSiteConfirm', 'uses' => 'EntitiesController@deleteSiteConfirm']);
        Route::get('/private/entities/{entity}/editSite/{siteKey}', ['as' => 'entities.editSite', 'uses' => 'EntitiesController@editEntitySite']);
        Route::get('/private/entities/{entity}/showSite/{siteKey}', ['as' => 'entities.showSite', 'uses' =>'EntitiesController@showEntitySite']);
        Route::get('/private/entities/{entity}/createSite',  ['as' => 'entities.createManager', 'uses' =>'EntitiesController@createEntitySite']);
        Route::post('/private/entities/{entity}/storeEntitySite',  ['as' => 'entities.createManager', 'uses' =>'EntitiesController@storeEntitySite']);
        Route::get('/private/entities/{entity}/table/sites', 'EntitiesController@tableSitesEntity');
        Route::delete('/private/entities/{entity}/site/{siteKey}/delete', ['as' => 'entities.destroySite', 'uses' => 'EntitiesController@destroyEntitySite']);
        Route::match(['PUT','PATCH'], '/private/entities/{entity}/updateSite/{siteKey}', 'EntitiesController@updateEntitySite');
        Route::post('/private/entities/manualUpdateTopicVotesInfo', 'EntitiesController@manualUpdateTopicVotesInfo');

        /* ---- END Entities Controller (sites) ---- */

        /** -----------------------------------------------------------
         *  {BEGIN} Routes to deal with the entity registration values
         * ------------------------------------------------------------
         */
        Route::post('private/entitiesDivided/{dashBoardElementId}/updateEntityDashBoardElements', 'EntitiesDividedController@updateEntityDashBoardElements');

        Route::get('/private/entitiesDivided/{type}/showEntityRegistrationValues', ['as' => 'entitiesDivided.entity.showEntityRegistrationValues', 'uses' => 'EntitiesDividedController@showEntityRegistrationValues']);
        Route::get('/private/entitiesDivided/{entityKey}/getEntityRegistrationValues/{type}', ['as' => 'entitiesDivided.entity.getEntityRegistrationValues', 'uses' => 'EntitiesDividedController@getEntityRegistrationValues']);
        Route::post('private/entitiesDivided/{entityKey}/uploadEntityRegistrationValues', 'EntitiesDividedController@uploadEntityRegistrationValues');
        Route::post('private/entitiesDivided/{entityKey}/addSingleEntityRegistrationValue', 'EntitiesDividedController@addSingleRegistrationValue');
        Route::get('/private/entitiesDivided/registrationValue/{valueId}/deleteRegistrationValueConfirm/{type}/entity/{entityKey}', ['as' => 'entitiesDivided.entity.deleteRegistrationValueConfirm', 'uses' => 'EntitiesDividedController@deleteRegistrationValueConfirm']);
        Route::delete('/private/entitiesDivided/registrationValue/{valueId}/deleteRegistrationValue/{type}/entity/{entityKey}', ['as' => 'entitiesDivided.entity.registrationValue.destroy', 'uses' => 'EntitiesDividedController@destroyRegistrationValue']);

        /** -----------------------------------------------------------
         *  {END} Routes to deal with the entity registration values
         * ------------------------------------------------------------
         */

        /*Entities Divided Controller*/

        Route::get('/private/entitiesDivided/{entityKey}/edit', ['as' => 'entitiesDivided.edit.entity', 'uses' => 'EntitiesDividedController@edit']);
        Route::get('/private/entitiesDivided/showEntity', ['as' => 'entitiesDivided.entity.show', 'uses' => 'EntitiesDividedController@showEntity']);

        Route::get('/private/entitiesDivided/showLayouts', ['as' => 'entitiesDivided.entityLayout.show', 'uses' => 'EntitiesDividedController@showLayouts']);
        Route::get('/private/entitiesDivided/manageDashBoardElements', ['as' => 'entitiesDivided.entityDashBoard.manage', 'uses' => 'EntitiesDividedController@manageDashBoardElements']);
        Route::get('/private/entitiesDivided/tableLayoutsEntity', ['as' => 'entitiesDivided.entityLayout.tableEntity', 'uses' => 'EntitiesDividedController@tableLayoutsEntity']);
        Route::get('/private/entitiesDivided/tableAddLayout', ['as' => 'entitiesDivided.entityLayout.tableAdd', 'uses' => 'EntitiesDividedController@tableAddLayout']);
        Route::get('/private/entitiesDivided/addLayout', ['as' => 'entitiesDivided.entityLayout.add', 'uses' => 'EntitiesDividedController@addLayout']);
        Route::get('/private/entitiesDivided/addLayoutAction/{layoutKey}', ['as' => 'entitiesDivided.entityLayout.addAction', 'uses' => 'EntitiesDividedController@addLayoutAction']);
        Route::get('/private/entitiesDivided/{layoutKey}/deleteLayoutConfirm', ['as' => 'entitiesDivided.entityLayout.deleteConfirm', 'uses' => 'EntitiesDividedController@deleteLayoutConfirm']);
        Route::delete('/private/entitiesDivided/{layoutKey}/deleteLayout', ['as' => 'entitiesDivided.entityLayout.delete', 'uses' => 'EntitiesDividedController@deleteLayout']);

        Route::get('/private/entitiesDivided/showManagers', ['as' => 'entitiesDivided.manager.show', 'uses' => 'EntitiesDividedController@showManagers']);
        Route::get('/private/entitiesDivided/createManager', ['as' => 'entitiesDivided.create.manager', 'uses' => 'EntitiesDividedController@createManager']);
        Route::get('/private/entitiesDivided/addManager', ['as' => 'entitiesDivided.manager.add', 'uses' => 'EntitiesDividedController@addManager']);
        Route::get('/private/entitiesDivided/tableUsersEntity', ['as' => 'entitiesDivided.manager.tableUsersEntity', 'uses' => 'EntitiesDividedController@tableUsersEntity']);
        Route::get('/private/entitiesDivided/{userKey}/editManager', ['as' => 'entitiesDivided.manager.edit', 'uses' => 'EntitiesDividedController@editManager']);
        Route::get('/private/entitiesDivided/{userKey}/deleteUserConfirm', ['as' => 'entitiesDivided.manager.deleteUserConfirm', 'uses' => 'EntitiesDividedController@deleteUserConfirm']);
        Route::post('/private/entitiesDivided/storeManager', ['as' => 'entitiesDivided.manager.store', 'uses' => 'EntitiesDividedController@storeManager']);
        Route::match(['PUT', 'PATCH'],'/private/entitiesDivided/{userKey}/updateManager', ['as' => 'entitiesDivided.manager.update', 'uses' => 'EntitiesDividedController@updateManager']);
        Route::delete('/private/entitiesDivided/{userKey}/deleteUser', ['as' => 'entitiesDivided.manager.deleteUser', 'uses' => 'EntitiesDividedController@deleteUser']);

        Route::get('/private/entitiesDivided/showAuthMethods', ['as' => 'entitiesDivided.entityAuthMethod.show', 'uses' => 'EntitiesDividedController@showAuthMethods']);
        Route::get('/private/entitiesDivided/tableAuthMethod', ['as' => 'entitiesDivided.entityAuthMethod.table', 'uses' => 'EntitiesDividedController@tableAuthMethod']);
        Route::get('/private/entitiesDivided/addAuthMethod', ['as' => 'entitiesDivided.entityAuthMethod.add', 'uses' => 'EntitiesDividedController@addAuthMethod']);
        Route::get('/private/entitiesDivided/{authMethodKey}/deleteAuthMethodConfirm', ['as' => 'entitiesDivided.entityAuthMethod.deleteConfirm', 'uses' => 'EntitiesDividedController@deleteAuthMethodConfirm']);
        Route::get('/private/entitiesDivided/tableAddAuthMethod', ['as' => 'entitiesDivided.entityAuthMethod.tableAdd', 'uses' => 'EntitiesDividedController@tableAddAuthMethod']);
        Route::get('/private/entitiesDivided/{authMethodKey}/addAuthMethodAction', ['as' => 'entitiesDivided.entityAuthMethod.addAction', 'uses' => 'EntitiesDividedController@addAuthMethodAction']);
        Route::delete('/private/entitiesDivided/{authMethodKey}/deleteAuthMethod', ['as' => 'entitiesDivided.entityAuthMethod.delete', 'uses' => 'EntitiesDividedController@deleteAuthMethod']);

        Route::get('/private/entitiesDivided/showLanguages', ['as' => 'entitiesDivided.entityLanguage.show', 'uses' => 'EntitiesDividedController@showLanguages']);
        Route::get('/private/entitiesDivided/addLanguage', ['as' => 'entitiesDivided.entityLanguage.add', 'uses' => 'EntitiesDividedController@addLanguage']);
        Route::get('/private/entitiesDivided/tableAddLanguageEntity', ['as' => 'entitiesDivided.entityLanguage.tableAddEntity', 'uses' => 'EntitiesDividedController@tableAddLanguageEntity']);
        Route::get('/private/entitiesDivided/tableLanguagesEntity', ['as' => 'entitiesDivided.entityLanguage.tableEntity', 'uses' => 'EntitiesDividedController@tableLanguagesEntity']);
        Route::get('/private/entitiesDivided/{languageId}/addLanguageAction', ['as' => 'entitiesDivided.entityLanguage.addAction', 'uses' => 'EntitiesDividedController@addLanguageAction']);
        Route::get('/private/entitiesDivided/{languageId}/makeLangDefaultConfirm', ['as' => 'entitiesDivided.entityLanguage.makeDefaultConfirm', 'uses' => 'EntitiesDividedController@makeLangDefaultConfirm']);
        Route::get('/private/entitiesDivided/{languageId}/makeLangDefault', ['as' => 'entitiesDivided.entityLanguage.makeDefault', 'uses' => 'EntitiesDividedController@makeLangDefault']);
        Route::get('/private/entitiesDivided/{languageId}/deleteLangConfirm', ['as' => 'entitiesDivided.entityLanguage.deleteConfirm', 'uses' => 'EntitiesDividedController@deleteLangConfirm']);
        Route::delete('/private/entitiesDivided/{languageId}/deleteLang', ['as' => 'entitiesDivided.entityLanguage.deleteConfirm', 'uses' => 'EntitiesDividedController@deleteLang']);

        Route::get('/private/entitiesDivided/delete', 'EntitiesDividedController@delete');
        Route::get('/private/entitiesDivided/destroy', 'EntitiesDividedController@destroy');
        Route::match(['PUT', 'PATCH'],'/private/entitiesDivided/update', 'EntitiesDividedController@update');

        /* ---- END Entities Divided Controller ---- */

        /*Email Template Controller*/
        Route::get('/private/entitiesDivided/entitySites/{siteKey}/emailTemplate/create', ['as' => 'entitiesDivided.emailTemplate.create', 'uses' =>'EmailTemplatesController@create']);
        Route::get('/private/entitiesDivided/entitySites/emailTemplate/{templateKey}/delete', ['as' => 'entitiesDivided.emailTemplate.delete', 'uses' =>'EmailTemplatesController@delete']);
        Route::resource('/private/entitiesDivided/entitySites/emailTemplate', 'EmailTemplatesController',['except' => ['create']]);
        /* ---- END Email Template Controller ---- */


        /*Entities Sites Controller*/
        Route::get('/private/entitiesDivided/entitySites/tableSitesEntity', 'EntitiesSitesController@tableSitesEntity');
        Route::get('/private/entitiesDivided/entitySites/{siteKey}/tableSiteAdditionalUrls', 'EntitiesSitesController@getSiteAdditionalUrlsTable');
        Route::get('/private/entitiesDivided/entitySites/{siteKey}/deleteConfirm', 'EntitiesSitesController@deleteConfirm');
        Route::get('/private/entitiesDivided/entitySites/{siteKey}/tableSiteEmailsManagers', 'EntitiesSitesController@tableSiteEmailsManagers');
        Route::get('/private/entitiesDivided/entitySites/{siteKey}/showHomePageConfigurations', ['as' => 'entitiesDivided.entitySites.homePageConfigurations', 'uses' => 'EntitiesSitesController@showHomePageConfigurations']);
        Route::get('/private/entitiesDivided/entitySites/{siteKey}/showEmailTemplates', ['as' => 'entitiesDivided.entitySites.showEmailTemplates', 'uses' => 'EntitiesSitesController@showEmailTemplates']);

        Route::get('/private/entitiesDivided/entitySites/{siteKey}/showUseTerms/version/{version?}', ['as' => 'entitiesDivided.entitySites.showUseTerms', 'uses' => 'EntitiesSitesController@showUseTerms']);
        Route::get('/private/entitiesDivided/entitySites/{siteKey}/showPrivacyPolicy/version/{version?}', ['as' => 'entitiesDivided.entitySites.showPrivacyPolicy', 'uses' => 'EntitiesSitesController@showPrivacyPolicy']);

        Route::get('/private/entitiesDivided/entitySites/{siteKey}/showStepperLoginList', 'EntitiesSitesController@showStepperLoginList')->name('entitySites.showStepperLoginList');
        Route::get('/private/entitiesDivided/entitySites/{siteKey}/showSiteLevels', 'EntitiesSitesController@showSiteLevels')->name('entitySites.showSiteLevels');
        Route::get('/private/entitiesDivided/entitySites/{siteKey}/showAdditionalUrl/{urlId}', 'EntitiesSitesController@showSiteAdditionalUrl');
        Route::resource('/private/entitiesDivided/entitySites', 'EntitiesSitesController');


        Route::get('/private/entitiesDivided/entitySites/additionalUrls/{siteKey}/create', ['as' => 'entitiesDivided.entitySites.SiteAdditionalUrlsController.create', 'uses' => 'SiteAdditionalUrlsController@create']);
        Route::get('/private/entitiesDivided/entitySites/additionalUrls/{url_id}/edit', ['as' => 'entitiesDivided.entitySites.SiteAdditionalUrlsController.edit', 'uses' => 'SiteAdditionalUrlsController@edit']);

        Route::get('/private/entitiesDivided/entitySites/additionalUrls/{urlId}/deleteConfirm', 'SiteAdditionalUrlsController@deleteConfirm');

        Route::resource('/private/entitiesDivided/entitySites/additionalUrls', 'SiteAdditionalUrlsController', ['except' => ['create','edit']]);

        /* ---- END Entities Sites Controller ---- */

        /* ---- Entity Notifications BEGIN ---- */
        Route::get('/private/entitiesDivided/{entityKey}/showNotifications','EntitiesController@showNotifications')->name('entitiesDivided.entityNotifications.show');
        Route::get('/private/entitiesDivided/{entityKey}/editNotifications','EntitiesController@editNotifications')->name('entitiesDivided.entityNotifications.edit');
        Route::put('/private/entitiesDivided/{entityKey}/updateNotifications','EntitiesController@updateNotifications')->name('entitiesDivided.entityNotifications.update');

        Route::get('/private/entitiesDivided/{entityKey}/showTemplate/{notification_code}','EntitiesController@showEntityNotificationTemplate')->name('entitiesDivided.entityNotificationTemplate.show');
        Route::get('/private/entitiesDivided/{entityKey}/editTemplate/{notification_code}','EntitiesController@editEntityNotificationTemplate')->name('entitiesDivided.entityNotificationTemplate.edit');
        Route::get('/private/entitiesDivided/{entityKey}/createTemplate/{notification_code}','EntitiesController@createEntityNotificationTemplate')->name('entitiesDivided.entityNotificationTemplate.create');
        Route::post('/private/entitiesDivided/{entityKey}/storeTemplate/{notification_code}','EntitiesController@storeEntityNotificationTemplate')->name('entitiesDivided.entityNotificationTemplate.store');
        Route::put('/private/entitiesDivided/{entityKey}/updateTemplate/{notification_code}','EntitiesController@updateEntityNotificationTemplate')->name('entitiesDivided.entityNotificationTemplate.update');
        /* ---- Entity Notifications END ---- */

        /*  Entity Login Levels Controller  */
        Route::get('/private/updateAllUserLevels', 'EntityLoginLevelsController@updateAllUserLevels')->name('entityLoginLevels.updateAllUserLevels');

        Route::post('/private/entityLoginLevels/parameters/updateParameter', 'EntityLoginLevelsController@updateParameter')->name('entityLoginLevels.updateParameters');
        Route::get('/private/entityLoginLevels/{login_level_key}/parametersTable', 'EntityLoginLevelsController@getIndexParametersTable')->name('entityLoginLevels.parametersTable');
        Route::get('/private/entityLoginLevels/{login_level_key}/showParameters', 'EntityLoginLevelsController@showParameters')->name('entityLoginLevels.showParameters');

        Route::get('/private/entitiesDivided/{entityKey}/showEntityLevels', 'EntityLoginLevelsController@index')->name('entitiesDivided.showEntityLevels');
        Route::get('/private/entityLoginLevels/table', 'EntityLoginLevelsController@getIndexTable')->name('entityLoginLevels.table');
        Route::get('/private/entityLoginLevels/{login_level_key}/delete', 'EntityLoginLevelsController@delete');
        Route::resource('/private/entityLoginLevels', 'EntityLoginLevelsController',['except' => ['index']]);
        /* ---- END Entity Login Levels Controller  ---- */



        /* ---- Stepper Login Controller  ---- */

        Route::get('/private/stepperLogin/table', 'StepperLoginController@getIndexTable')->name('stepperLogin.table');
        Route::get('/private/stepperLogin/LoginLevelReorder', 'StepperLoginController@showLevelReorder')->name('stepperLogin.showLevelReorder');
        Route::post('/private/stepperLogin/configurations/updateParameter', 'StepperLoginController@updateParameter')->name('stepperLogin.updateParameters');
        Route::get('/private/stepperLogin/configurationsTable', 'StepperLoginController@getIndexConfigurationsTable')->name('stepperLogin.configurationsTable');
        Route::get('/private/stepperLogin/showConfigurations', 'StepperLoginController@showConfigurations')->name('stepperLogin.showConfigurations');
        Route::post('/private/stepperLogin/updateOrder', 'StepperLoginController@updateOrder');

        Route::get('/private/stepperLogin/{loginLevelKey}/delete', 'StepperLoginController@delete');
        Route::resource('/private/stepperLogin', 'StepperLoginController');

        /* ---- END Stepper Login Levels Controller  ---- */


        /** Site Ethics - use terms and privacy policy*/
        Route::get('/private/entitiesDivided/entitySites/{site_key}/siteEthic/{site_ethic_key}/delete', 'SiteEthicsController@delete')->name('entitiesDivided.siteEthic.delete');
        Route::get('/private/entitiesDivided/entitySites/siteEthic/{site_ethic_key}/activateVersion/{version}', 'SiteEthicsController@activateVersion');
        Route::resource('/private/entitiesDivided/entitySites/{site_key}/siteEthic', 'SiteEthicsController',['only' => ['edit', 'update', 'store', 'destroy', 'create']]);



        Route::get('/private/entities/tableLayoutsEntityMan', ['as' => 'entities.tableLayoutsEntityMan', 'uses' =>'EntitiesController@tableLayoutsEntityMan']);


        /* Currencies Controller */
        Route::get('/private/currencies/table', 'CurrenciesController@tableCurrencies');
        Route::get('/private/currencies/{currency}/delete', ['as' => 'entity.destroy', 'uses' => 'CurrenciesController@delete']);
        Route::resource('/private/currencies', 'CurrenciesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Currencies Controller ---- */


        /* Group Types Controller */

        Route::get('/private/groupTypes/{groupTypeKey}/delete', 'GroupTypesController@delete');
        Route::get('/private/groupTypes/table', 'GroupTypesController@tableGroupTypes');
        Route::resource('/private/groupTypes', 'GroupTypesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Group Types Controller ---- */

        /* Entity Groups Controller */
        Route::post('/private/entityGroups/updateOrder', 'EntityGroupsController@updateOrder');
        Route::get('/private/entityGroups/{entityGroupKey}/delete', 'EntityGroupsController@delete');
        Route::get('/private/entityGroups/table', 'EntityGroupsController@tableEntityGroups');
        Route::get('/private/entityGroups/showEntityGroups', ['as' => 'entityGroups.showGroups', 'uses' => 'EntityGroupsController@showGroups']);

        /* Entity Groups Permissions */
        Route::get('/private/entityGroups/{entityGroupKey}/showPermissions', ['as' => 'entityGroups.permissions', 'uses' => 'EntityGroupsController@showPermissions']);
        Route::Post('/private/entityGroups/{entityGroupKey}/createPermissions', ['as' => 'entityGroups.storePermissions', 'uses' => 'EntityGroupsController@storePermissions']);

        /* END Entity Groups Permissions */


        /* Emails Controller */
        Route::get('/private/createEmailTemplates/{siteKey}', 'EmailTemplatesController@createEmailsFromTemplates');

        Route::group(['middleware' => ['privateAuthOne'], 'name' => 'emails'], function () {
            Route::get('/private/emails/{emailKey}/delete', 'EmailsController@delete');
            Route::get('/private/emails/table', 'EmailsController@tableEmails');
            Route::get('/private/emails/summary', 'EmailsController@showSummary');
            Route::get('private/emails/stats', 'EmailsController@showStats');
            Route::resource('/private/emails', 'EmailsController', ['only' => ['show', 'index', 'create', 'store']]);
        });
        /* ---- END Emails Controller ---- */

        /* Newsletters Controller */
        Route::get('/private/newsletters/getTinyMCE', 'PrivateNewslettersController@getTinyMCE');
        Route::get('/private/newsletters/getTinyMCEView', 'PrivateNewslettersController@getTinyMCEView');
        Route::get('/private/newsletters/table', 'PrivateNewslettersController@getIndexTable');
        Route::get('/private/newsletters/{newsletterKey}/delete', 'PrivateNewslettersController@delete');
        Route::match(['PUT', 'PATCH'],'/private/newsletters/{newsletterKey}/update', ['as' => 'newsletters.update', 'uses' => 'PrivateNewslettersController@update']);
        Route::get('/private/newsletters/sendNewsletter/{newsletterKey}/{flag}', ['as' => 'newsletters.test', 'uses' => 'PrivateNewslettersController@sendNewsletter']);
        Route::resource('/private/newsletters', 'PrivateNewslettersController', ['only' => ['show', 'index', 'create', 'store', 'edit', 'destroy']]);

        Route::get('/private/newsletterSubscription/table', 'NewsletterSubscriptionsController@getIndexTable');
        Route::get('/private/newsletterSubscription/exportAsCsv', 'NewsletterSubscriptionsController@exportAsCsv');
        Route::resource('/private/newsletterSubscription', 'NewsletterSubscriptionsController');
        /* ---- END Emails Controller ---- */

        /* Sms Controller */
//        Route::get('/private/sms/resume', ['as' => 'sms.showResume', 'uses' => 'SmsController@showResume']);
        Route::get('/private/sms/sendedSms', ['as' => 'sms.sendedSms', 'uses' => 'SmsController@showSendedSms']);
        Route::get('/private/sms/receivedSms', ['as' => 'sms.receivedSms', 'uses' => 'SmsController@showReceivedSms']);
        Route::get('/private/sms/analyticsSms', 'SmsController@showAnalyticsSms');
        Route::get('/private/sms/table', 'SmsController@tableSendedSms');
        Route::get('/private/sms/receivedTable', 'SmsController@tableReceivedSms');
        Route::get('/private/sms/getSendedDatatableFilter', 'SmsController@getSendedDatatableFilter');
        Route::get('/private/sms/getReceivedDatatableFilter', 'SmsController@getReceivedDatatableFilter');

        Route::get('private/sms/statsResume30D', ['as' => 'sms.showResume30D', 'uses' => 'SmsController@showResume30D']);
        Route::get('private/sms/statsResume', ['as' => 'sms.showResume48H', 'uses' => 'SmsController@showResume48H']);
        Route::get('private/sms/stats', ['as' => 'sms.showAnalyticsSmsFiltered24H', 'uses' => 'SmsController@showAnalyticsSmsFiltered24H']);
        Route::get('private/sms/stats30D', ['as' => 'sms.showAnalyticsSmsFiltered30D', 'uses' => 'SmsController@showAnalyticsSmsFiltered30D']);

        Route::get('/private/sms/showReceivedDetails/{receivedSmsKey}', 'SmsController@showReceivedDetails');
        Route::resource('/private/sms', 'SmsController', ['only' => ['show','index', 'create', 'store']]);
        /* ---- END Sms Controller ---- */

        /* Entity Groups Users */

        Route::get('/private/entityGroups/{entityGroupKey}/addUser/{userKey}', ['as' => 'entityGroups.addUser', 'uses' => 'EntityGroupsController@addUser']);
        Route::get('/private/entityGroups/{entityGroupKey}/removeUser/{userKey}', ['as' => 'entityGroups.removeUser', 'uses' => 'EntityGroupsController@removeUser']);
        Route::get('/private/entityGroups/{entityGroupKey}/list', ['as' => 'entityGroups.listUsers', 'uses' => 'EntityGroupsController@getUsersByEntityGroupKey']);
        Route::get('/private/entityGroups/{entityGroupKey}/users', ['as' => 'entityGroups.users', 'uses' => 'EntityGroupsController@showUsers']);
        Route::get('private/entityGroups/tableGroupUsers', 'EntityGroupsController@tableGroupUsers');
        Route::get('private/entityGroups/tableEntityUsers', 'EntityGroupsController@tableEntityUsers');
        /* End Entity Groups Users */
        Route::resource('/private/entityGroups', 'EntityGroupsController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Entity Groups Controller ---- */

        /* ParameterUserTypes Controller */
        Route::group(['middleware' => ['privateAuthOne'], 'name' => 'parameterUserTypes'], function () {
            Route::get('private/parameterUserTypes/getIndexTable', 'ParameterUserTypesController@getIndexTable');
            Route::get('private/parameterUserTypes/{parameterUserType}/delete', ['as' => 'parameterUserTypes.destroy', 'uses' => 'ParameterUserTypesController@delete']);
            Route::resource('private/parameterUserTypes', 'ParameterUserTypesController');
        });
        /* ---- END ParameterUserTypes Controller ---- */

        /* Countries Controller */
        Route::get('/private/countries/table', 'CountriesController@tableCountries');
        Route::get('/private/countries/{country}/delete', ['as' => 'country.destroy', 'uses' => 'CountriesController@delete']);
        Route::resource('/private/countries', 'CountriesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Countries Controller ---- */

        /* Languages Controller */
        Route::get('/private/languages/table', 'LanguagesController@tableLanguages');
        Route::get('/private/languages/{language}/delete', ['as' => 'language.destroy', 'uses' => 'LanguagesController@delete']);
        Route::resource('/private/languages', 'LanguagesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Languages Controller ---- */

        /* Roles Controller */
        Route::get('/private/roles/getIndexTable', 'RolesController@getIndexTable');
        Route::get('/private/roles/{roleKey}/delete', ['as' => 'language.destroy', 'uses' => 'RolesController@delete']);
        Route::post('/private/roles/setPermissionRole', 'RolesController@setPermissionRole');
        Route::get('/private/roles/{roleKey}/showPermissions', ['as' => 'roles.showPermissions', 'uses' => 'RolesController@showPermissions']);

        Route::resource('/private/roles', 'RolesController');

        /* ---- END Roles Controller ---- */




        /* Timezones Controller */
        Route::get('/private/timezones/table', 'TimezonesController@tableTimezones');
        Route::get('/private/timezones/{timezone}/delete', ['as' => 'timezone.destroy', 'uses' => 'TimezonesController@delete']);
        Route::resource('/private/timezones', 'TimezonesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Timezones Controller ---- */

        /* Authentication Methods Controller */
        Route::get('/private/authMethods/table', 'AuthMethodsController@tableAuthMethods');
        Route::get('/private/authMethods/{authMethod}/delete', ['as' => 'authMethod.destroy', 'uses' => 'AuthMethodsController@delete']);
        Route::resource('/private/authMethods', 'AuthMethodsController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Authentication Methods Controller ---- */

        /* Votes Methods Controller */
        Route::get('/private/votes/methods/table', 'VoteMethodsController@tableVoteMethods');
        Route::get('/private/votes/methods/{voteMethod}/delete', ['as' => 'voteMethod.destroy', 'uses' => 'VoteMethodsController@delete']);
        Route::get('/private/votes/methods/{voteMehod}/showConfigurations', ['as' => 'voteMethod.showConfigurations', 'uses' => 'VoteMethodsController@showConfigurations']);
        Route::resource('/private/votes/methods', 'VoteMethodsController');
        /* ---- END Votes Methods Controller ---- */


        /* Votes Method Config Controller */
        Route::get('/private/votes/methods/{methodId}/configTable', 'VoteMethodConfigController@tableConfigs');
        Route::get('/private/votes/methods/{methodId}/config/{configId}/delete', ['as' => 'voteMethod.destroy', 'uses' => 'VoteMethodConfigController@delete']);
        Route::resource('/private/votes/methods/{methodId}/config', 'VoteMethodConfigController');
        /* ---- END Votes Method Config Controller ---- */

        /* Votes Configurations Controller */
        Route::get('/private/votes/voteConfig/table', 'VotesConfigsController@getIndexTable');
        Route::get('/private/votes/voteConfig/{configKey}/delete', ['as' => 'voteConfiguration.destroy', 'uses' => 'VotesConfigsController@delete']);
        Route::resource('/private/votes/voteConfig', 'VotesConfigsController');
        /* ---- END Votes Configurations Controller ---- */


        /* Geographic Areas Controller */
        Route::get('/private/geoareas/table', 'GeoAreasController@tableGeoAreas');
        Route::get('/private/geoareas/{geoarea}/delete', ['as' => 'geoarea.destroy', 'uses' => 'GeoAreasController@delete']);
        Route::resource('/private/geoareas', 'GeoAreasController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Geographic Areas Controller ---- */

        /* ---- Abuse Controller ---- */
        Route::get('/private/abuse/table', 'AbuseController@getIndexTable');
        Route::get('/private/abuse/getAbusesByCBTable/{cbId}', 'AbuseController@getAbusesByCBTable');
        Route::get('/private/abuse/acceptAllForumAbuses/{cbId}', 'AbuseController@acceptAllForumAbuses');
        Route::get('/private/abuse/declineAllForumAbuses/{cbId}', 'AbuseController@declineAllForumAbuses');
        Route::get('/private/abuse/getAbusesByTopicTable/{topicId}', 'AbuseController@getAbusesByTopicTable');
        Route::get('/private/abuse/acceptAllTopicAbuses/{topicId}', 'AbuseController@acceptAllTopicAbuses');
        Route::get('/private/abuse/declineAllTopicAbuses/{topicId}', 'AbuseController@declineAllTopicAbuses');
        Route::get('/private/abuse/acceptPostAbuses/{postId}', 'AbuseController@acceptPostAbuses');
        Route::get('/private/abuse/declinePostAbuses/{postId}', 'AbuseController@declinePostAbuses');
        Route::resource('/private/abuse', 'AbuseController');
        /* ---- END Abuse Controller ---- */

        /* Texts Controller */
        Route::get('/private/texts/table', 'TextsController@tableTexts');
        Route::get('/private/texts/{text}/delete', ['as' => 'text.destroy', 'uses' => 'TextsController@delete']);
        Route::resource('/private/texts', 'TextsController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Texts Controller ---- */

        /* Categories Controller */
        Route::get('/private/categories/table', 'CategoriesController@tableCategories');
        Route::get('/private/categories/{category}/delete', ['as' => 'category.destroy', 'uses' => 'CategoriesController@delete']);
        Route::resource('/private/categories', 'CategoriesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Categories Controller ---- */

        /* Mails Controller */
        Route::get('/private/mails/table', 'MailsController@tableMails');
        Route::get('/private/mails/{mail}/delete', ['as' => 'mail.destroy', 'uses' => 'MailsController@delete']);
        Route::resource('/private/mails', 'MailsController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Mails Controller ---- */


        /* Content Type Types Controller */

        Route::get('/private/contentTypeTypes/{contentTypeTypeKey}/delete', 'ContentTypeTypesController@delete');
        Route::get('/private/contentTypeTypes/table', 'ContentTypeTypesController@tableContentTypeTypes');
        Route::resource('/private/contentTypeTypes', 'ContentTypeTypesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Content Type Types Controller ---- */


        /* Contents Controller */

        Route::get('/private/content/{type}', ['as' => 'private.contents.index', 'uses' => 'ContentsController@index']);
        Route::get('/private/content/table/{type}', 'ContentsController@contentsDataTable');
        Route::get('/private/content/{id}/edit/version/{version?}', ['as' => 'private.contents.edit', 'uses' => 'ContentsController@edit']);
        Route::get('/private/content/{id?}/version/{version?}', ['as' => 'private.contents.show', 'uses' => 'ContentsController@show']);






        Route::get('/private/contents/tableP', 'ContentsController@tablePages');
        Route::get('/private/contents/tableN', 'ContentsController@tableNews');
        Route::get('/private/contents/tableE', 'ContentsController@tableEvents');
        Route::post('/private/contents/download', 'ContentsController@download');
        Route::get('/private/content/publish/{id}', 'ContentsController@publish');
        Route::get('/private/content/unpublish/{id}', 'ContentsController@unpublish');
        Route::get('/private/content/newslist/{key}', ['as' => 'private.contents.newslist', 'uses' =>'ContentsController@getNewsList']);
        Route::get('/private/content/newsids/{key}', ['as' => 'private.contents.newsids', 'uses' =>'ContentsController@getNewsIds']);
        Route::get('/private/content/presentnews/{key}', ['as' => 'private.contents.presentnews', 'uses' =>'ContentsController@getPresentNews']);
        Route::get('/private/content/lastnews/{key}', ['as' => 'private.contents.lastnews', 'uses' =>'ContentsController@getLastNews']);
        Route::get('/private/content/eventslist/{key}', ['as' => 'private.contents.eventslist', 'uses' =>'ContentsController@getEventsList']);
        Route::get('/private/content/eventsids/{key}', ['as' => 'private.contents.eventsids', 'uses' =>'ContentsController@getEventsIds']);
        Route::get('/private/content/lastevents/{key}', ['as' => 'private.contents.lastevents', 'uses' =>'ContentsController@getLastEvents']);
        Route::get('/private/content/activateVersion/{id}/{currVersion}', 'ContentsController@activateVersion');

        Route::get('/private/content/preview/{id?}/{currVersion?}', ['as' => 'private.contents.preview', 'uses' => 'ContentsController@previewPage']);


        Route::get('/private/content/{type}/create', ['as' => 'private.contents.create', 'uses' => 'ContentsController@create']);
        Route::get('/private/content/{id}/delete', ['as' => 'private.contents.destroy', 'uses' => 'ContentsController@delete']);
        Route::post('/private/content/addFile', ['as' => 'private.contents.addFile', 'uses' =>'ContentsController@addFile']);
        Route::post('/private/content/orderFile', ['as' => 'private.contents.orderFile', 'uses' =>'ContentsController@orderFile']);
        Route::get('/private/content/getFiles/{contentId?}/{typeId?}', ['as' => 'private.contents.getFiles', 'uses' => 'ContentsController@getFiles']);
        Route::post('/private/content/delFile', ['as' => 'private.files.deleteFile', 'uses' => 'ContentsController@deleteFile']);
        Route::get('/private/content/getTinyMCE', 'ContentsController@getTinyMCE');
        Route::get('/private/content/getTinyMCEView/{type?}', 'ContentsController@getTinyMCEView');
        Route::get('/private/content/tinyMCETable', 'ContentsController@tinyMCETable');
        Route::match(['PUT', 'PATCH'],'/private/content/{contentId?}/file/{id?}', ['as' => 'files.updateFileDetails', 'uses' => 'ContentsController@updateFileDetails']);
        Route::get('/private/content/{contentId?}/file/{id?}/edit', ['as' => 'files.editFileDetails', 'uses' => 'ContentsController@editFileDetails']);
        Route::get('/private/content/{contentId?}/file/{id?}', ['as' => 'files.getFileDetails', 'uses' => 'ContentsController@getFileDetails']);

        Route::resource('/private/content', 'ContentsController', ['only' => ['update', 'store', 'destroy']]);

        /* ---- END Contents Controller ---- */

        /* Access Pages Controller */
        Route::get('/private/accessPages/table', 'AccessPagesController@tableAccessPages');
        Route::get('/private/accessPages/{accessPage}/delete', ['as' => 'accessPage.destroy', 'uses' => 'AccessPagesController@delete']);
        Route::resource('/private/accessPages', 'AccessPagesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Access Pages Controller ---- */

        /* Tags Controller */
        Route::get('/private/tags/table', 'TagsController@tableTags');
        Route::get('/private/tags/{tag}/delete', ['as' => 'tag.destroy', 'uses' => 'TagsController@delete']);
        Route::resource('/private/tags', 'TagsController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Tags Controller ---- */

        /* Phases Controller */
        Route::get('/private/phases/table', 'PhasesController@tablePhases');
        Route::get('/private/phases/{phase}/delete', ['as' => 'phase.destroy', 'uses' => 'PhasesController@delete']);
        Route::resource('/private/phases', 'PhasesController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Phases Controller ---- */

        /* Budgets Controller */
        Route::get('/private/budgets/table', 'BudgetsController@tableBudgets');
        Route::get('/private/budgets/{budget}/delete', ['as' => 'budget.destroy', 'uses' => 'BudgetsController@delete']);
        Route::resource('/private/budgets', 'BudgetsController', ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'index', 'create']]);
        /* ---- END Budgets Controller ---- */



        Route::group(['middleware' => ['privateAuthOne'], 'name' => 'questionnaire'], function () {
            /* Questionnaires Controller */
            Route::post('/private/questionnaire/{key}/downloadPdfAnswerByForm', 'QuestionnairesController@downloadPdfAnswerByForm');
            Route::get('/private/questionnaire/{key}/downloadPdfAnswer/{formReplyKey}', 'QuestionnairesController@downloadPdfAnswer');
            Route::get('/private/questionnaire/{key}/downloadPdf', 'QuestionnairesController@downloadPdf');
            Route::get('/private/questionnaire/{key}/getTableUserAnswers', 'QuestionnairesController@getTableUserAnswers');
            Route::get('/private/questionnaire/table', 'QuestionnairesController@getIndexTable');
            Route::get('/private/questionnaire/{key}/delete', ['as' => 'questionnaire.delete', 'uses' => 'QuestionnairesController@delete']);
            Route::get('/private/questionnaire/{key}/showStatistics', ['as' => 'questionnaire.showStatistics', 'uses' => 'QuestionnairesController@showStatistics']);
            Route::resource('/private/questionnaire', 'QuestionnairesController');

            Route::get('/private/questionnaire/{key}/answers/excel/', ['as' => 'questionnaire.answers', 'uses' => 'QuestionnaireAnswersController@excel']);
            Route::post('/private/questionnaire/getListOfAnswers', 'QuestionnaireAnswersController@getListOfAnswers');
            Route::get('/private/questionnaire/{key}/statisticsPdf/', ['as' => 'questionnaire.answers', 'uses' => 'QuestionnaireAnswersController@statisticsPdf']);
            Route::get('/private/questionnaire/{key}/statistics/', ['as' => 'questionnaire.answers', 'uses' => 'QuestionnaireAnswersController@statistics']);
            Route::get('/private/questionnaire/{key}/answer/{formReplyKey}', ['as' => 'questionnaire.answers', 'uses' => 'QuestionnaireAnswersController@show']);

            Route::post('/private/questionnaire/questiongroup/updateOrder', 'QuestionGroupsController@updateOrder');
            Route::get('/private/questionnaire/questiongroup/getQuestionGroups/{key}', 'QuestionGroupsController@getQuestionGroups');
            Route::get('/private/questionnaire/questiongroup/{key}/delete', ['as' => 'questiongroup.delete', 'uses' => 'QuestionGroupsController@delete']);
            Route::get('/private/questionnaire/{key}/questiongroup/create', ['as' => 'private.questionnaire.questiongroup.create', 'uses' => 'QuestionGroupsController@create']);
            Route::resource('/private/questionnaire/questiongroup', 'QuestionGroupsController', ['except' => ['create']]);

            Route::post('/private/questionnaire/question/updateOrder', 'QuestionsController@updateOrder');
            Route::get('/private/questionnaire/question/{key}/reuseOptions', 'QuestionsController@reuseOptions');
            Route::get('/private/questionnaire/question/getQuestions/{key}', 'QuestionsController@getQuestions');
            Route::get('/private/questionnaire/{key}/question/create', ['as' => 'private.questionnaire.question.create', 'uses' => 'QuestionsController@create']);
            Route::get('/private/questionnaire/question/{key}/delete', ['as' => 'question.delete', 'uses' => 'QuestionsController@delete']);
            Route::resource('/private/questionnaire/question', 'QuestionsController', ['except' => ['create']]);

            Route::post('/private/questionnaire/questionoption/addQuestionGroupOption/{key}', 'QuestionOptionsController@addQuestionGroupOption');
            Route::post('/private/questionnaire/questionoption/addQuestionOption/{key?}', 'QuestionOptionsController@addQuestionOption');
            Route::get('/private/questionnaire/questionoption/getQuestionOptions/{key}', 'QuestionOptionsController@getQuestionOptions');
            Route::get('/private/questionnaire/questionoption/getOptions', 'QuestionOptionsController@getOptions');
            Route::get('/private/questionnaire/questionoption/{key}/delete', ['as' => 'questionoption.delete', 'uses' => 'QuestionOptionsController@delete']);
            Route::get('/private/questionnaire/{key}/questionoption/create', ['as' => 'private.questionnaire.questionoption.create', 'uses' => 'QuestionOptionsController@create']);
            Route::post('/private/questionnaire/questionOptions/useOptions', 'QuestionOptionsController@useOptions');

            Route::post('/private/questionnaire/questionOption/updateOrder', 'QuestionOptionsController@updateOrder');
            Route::post('/private/ideas/parameters/addOptionImage', 'QuestionOptionsController@addOptionImage');
            Route::resource('/private/questionnaire/questionoption', 'QuestionOptionsController', ['except' => ['create']]);
        });

        /* ---- END Questionnaires Controller ---- */


        /* Empaville presentation Controller */

        Route::get('/private/presentation/{cbKey}/next/{id}', 'EmpavillePresentationController@next');
        Route::get('/private/presentation/{cbKey}/next/{id}/showProposal/{count}', 'EmpavillePresentationController@showProposal');
        Route::post('/private/presentation/closeVotes', 'EmpavillePresentationController@closeVotes');
        Route::post('/private/presentation/closeProposals', 'EmpavillePresentationController@closeProposals');
        Route::post('/private/presentation/openVotes', 'EmpavillePresentationController@openVotes');
        Route::post('/private/presentation/{cbKey}/index', 'EmpavillePresentationController@index');
        /* END Empaville presentation Controller */



        /* Vote Methods Controller */
        Route::get('/private/layouts/table', 'LayoutsController@getIndexTable');
        Route::get('/private/layouts/{voteMethod}/delete', 'LayoutsController@delete');
        Route::resource('/private/layout', 'LayoutsController');
        /* ---- END Vote Methods Controller ---- */

        /* Vote Methods Controller */
        Route::get('/private/layouts/table', 'LayoutsController@getIndexTable');
        Route::get('/private/layouts/{voteMethod}/delete', 'LayoutsController@delete');
        Route::resource('/private/layout', 'LayoutsController');
        /* ---- END Vote Methods Controller ---- */


        /* Parameter Types Controller */
        Route::get('/private/parameterTypes/table', 'ParameterTypesController@getIndexTable');
        Route::get('/private/parameterTypes/{voteMethod}/delete', 'ParameterTypesController@delete');
        Route::resource('/private/parameterType', 'ParameterTypesController');
        /* ---- END Parameter Types Controller ---- */

        /* Site Config Group Controller */
        Route::get('/private/siteConfGroup/table', 'SiteConfGroupController@getIndexTable');
        Route::get('/private/siteConfGroup/{siteConfGroup}/delete', 'SiteConfGroupController@delete');
        Route::get('/private/siteConfGroup/{siteConfGroup}/createConf', ['as' => 'siteConf.create', 'uses' => 'SiteConfGroupController@createConf']);
        Route::post('/private/siteConfGroup/{siteConfGroup}/storeConf', ['as' => 'siteConf.create', 'uses' => 'SiteConfGroupController@storeConf']);
        Route::get('/private/siteConfGroup/{siteConfGroup}/showConf/{siteConf}', ['as' => 'siteConf.show', 'uses' => 'SiteConfGroupController@showConf']);
        Route::get('/private/siteConfGroup/{siteConfGroup}/editConf/{siteConf}', ['as' => 'siteConf.show', 'uses' => 'SiteConfGroupController@editConf']);
        Route::get('/private/siteConfGroup/{siteConfGroup}/deleteConf/{siteConf}', ['as' => 'siteConf.default', 'uses' => 'SiteConfGroupController@deleteConf']);
        Route::delete('/private/siteConfGroup/{siteConfGroup}/destroyConf/{siteConf}', ['as' => 'siteConf.default', 'uses' => 'SiteConfGroupController@destroyConf']);
        Route::get('/private/siteConfGroup/{siteConfGroup}/table/siteConfs', 'SiteConfGroupController@getConfsOfGroup');
        Route::get('/private/siteConfGroup/{siteConfGroup}/showSiteConfGroupConfigurations',['as' => 'siteConf.showSiteConfGroupConfigurations', 'uses' => 'SiteConfGroupController@showSiteConfGroupConfigurations']);
        Route::resource('/private/siteConfGroup', 'SiteConfGroupController');
        /* ---- END Site Config Controller ---- */

        Route::get('/private/siteConfGroup/{siteConfGroup}/siteConfigurations/delete/{siteConf}', ['as' => 'siteConfigurations.default', 'uses' => 'SiteConfigurationsController@delete']);
        Route::resource('/private/siteConfGroup/{siteConfGroup}/siteConfigurations', 'SiteConfigurationsController');


        /* Site Site Config Controller */
        Route::get('/private/SiteSiteConfig/table', 'SiteSiteConfigController@getIndexTable');
        Route::get('/private/SiteSiteConfig/{voteMethod}/delete', 'SiteSiteConfigController@delete');
        Route::resource('/private/SiteSiteConfig', 'SiteSiteConfigController');
        /* ---- END Site Site Config Controller ---- */

        /* Site Conf Values Controller */
        Route::get('/private/siteConfValues/getSiteConfsFromGroup', ['as' => 'privateSiteConfValues.getSiteConfsFromGroup', 'uses' => 'SiteConfValuesController@getSiteConfsFromGroup']);
        Route::resource('/private/siteConfValues', 'SiteConfValuesController');
        /* ---- END Site Site Config Controller ---- */

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        /* Site Site Login Levels Controller */
        Route::get('/private/siteLoginLevels/table', 'LoginLevelsController@getIndexTable')->name('siteLoginLevels.table');
        Route::get('/private/siteLoginLevels/LoginLevelReorder', 'LoginLevelsController@showLevelReorder')->name('siteLoginLevels.showLevelReorder');
        Route::post('/private/siteLoginLevels/configurations/updateParameter', 'LoginLevelsController@updateParameter')->name('siteLoginLevels.updateParameters');
        Route::get('/private/siteLoginLevels/configurationsTable', 'LoginLevelsController@getIndexConfigurationsTable')->name('siteLoginLevels.configurationsTable');
        Route::get('/private/siteLoginLevels/showConfigurations', 'LoginLevelsController@showConfigurations')->name('siteLoginLevels.showConfigurations');
        Route::post('/private/siteLoginLevels/updateOrder', 'LoginLevelsController@updateOrder');

        Route::get('/private/siteLoginLevels/{levelParameterKey}/delete', 'LoginLevelsController@delete');
        Route::resource('/private/siteLoginLevels', 'LoginLevelsController');
        /* ---- END Site Site Config Controller ---- */

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

        /* Quest Icons Controller */
        Route::get('/private/questIcon/table', 'QuestIconsController@getIndexTable');
        Route::get('/private/questIcon/{key}/delete', 'QuestIconsController@delete');
        Route::post('/private/questIcon/addIconImage', 'QuestIconsController@addIconImage');
        Route::resource('/private/questIcon', 'QuestIconsController');
        /* ---- END Quest Icons Controller ---- */

        /* Home Page Types Controller */
        Route::get('/private/homePageTypes/table', 'HomePageTypesController@getIndexTable');
        Route::post('/private/homePageTypes/sons', 'HomePageTypesController@getGroupTypesTable');
        Route::get('/private/homePageTypes/{home_page_type_key}/createGroupType', ['as' => 'private.homePageTypes.createGroupType', 'uses' => 'HomePageTypesController@createGroupType']);
        Route::get('/private/homePageTypes/{home_page_type_key}/delete', 'HomePageTypesController@delete');
        Route::get('/private/homePageTypes/{home_page_type_key}/showHomePageTypesChildren', ['as' => 'private.homePageTypes.showHomePageTypesChildren', 'uses' => 'HomePageTypesController@showHomePageTypesChildren']);
        Route::get('/private/homePageTypes/{home_page_type_key}/showHomePageTypesChildren/{home_page_type_group_key}', ['as' => 'private.homePageTypes.showHomePageGroupType', 'uses' => 'HomePageTypesController@showHomePageGroupType']);
        Route::resource('/private/homePageTypes', 'HomePageTypesController');
        /* ---- END Home Page Types Controller ---- */

        /* Home Page Configurations Controller */
        Route::get('/private/site/{siteKey}/homePageConfigurations/table', 'HomePageConfigurationsController@getIndexTable');
        Route::get('/private/homePageConfigurations/{home_page_configuration_key}/delete', 'HomePageConfigurationsController@delete');
        Route::get('/private/site/{siteKey}/homePageType/{homePageTypeKey}/homePageConfigurations/create', ['as' => 'private.homePageConfigurations.create', 'uses' => 'HomePageConfigurationsController@create']);
        Route::post('/private/homePageConfiguration/addImage', 'HomePageConfigurationsController@addImage');
        Route::post('/private/homePageConfigurations/getUrlWithHomePageTypeKey', 'HomePageConfigurationsController@getUrlWithHomePageTypeKey');
        Route::resource('/private/homePageConfigurations', 'HomePageConfigurationsController', ['except' => ['create']]);
        /* ---- END Home Page Configurations Controller ---- */

        Route::group(['middleware' => ['privateAuthOne'], 'name' => 'mps'], function () {
            /* MPs Controller */
            Route::get('/private/mp/{mp_key}/updateState', 'MPsController@updateState');
            Route::get('/private/mp/table', 'MPsController@getIndexTable');
            Route::get('/private/mp/{mp_key}/delete', 'MPsController@delete');
            Route::get('/private/mp/{key}/showConfigurations', ['as' => 'private.mp.showConfigurations', 'uses' => 'MPsController@showConfigurations']);
            Route::resource('/private/mp', 'MPsController');
            /* ---- END MPs Controller ---- */
        });
        /* MP Operators Controller */
        Route::get('/private/mp/operator/{operator_key}/nodeDelete', 'MPOperatorsController@delete');
        Route::resource('/private/mp/operator/{operator_key}/node', 'MPOperatorsController');
        /* ---- END MP Operators Controller ---- */


        /* MP Operators CB Controller */
        Route::post('private/operatorCb/updateParameter', 'MPCbsController@updateParameter');
        Route::post('private/operatorCb/getParameter', 'MPCbsController@getParameter');
        Route::post('private/operatorCb/removeParameterCache', 'MPCbsController@removeParameterCache');
        Route::post('private/operatorCb/addModalParameter', 'MPCbsController@addModalParameter');
        Route::post('private/operatorCb/addParameterTemplateSelection', 'MPCbsController@addParameterTemplateSelection');
        Route::post('private/operatorCb/addParameterTemplate', 'MPCbsController@addParameterTemplate');
        Route::post('private/operatorCb/addParameter', 'MPCbsController@addParameter');
        Route::get('private/operatorCb/allUsers', 'MPCbsController@allUsers');
        Route::get('/private/operatorCb/{operator_key}/nodeDelete', 'MPCbsController@delete');
        Route::resource('/private/operatorCb/node', 'MPCbsController');
        /* ---- END MP Operators CB Controller ---- */


        /* MP Operators vote Controller */
        Route::get('/private/operatorVote/{operator_key}/nodeDelete', 'MPVotesController@delete');
        Route::resource('/private/operatorVote/node', 'MPVotesController');
        /* ---- END MP Operators vote Controller ---- */

        /* MP Operators Questionnaire Controller */
        Route::get('/private/operatorQuestionnaire/{operator_key}/nodeDelete', 'MPQuestionnairesController@delete');
        Route::resource('/private/operatorQuestionnaire/node', 'MPQuestionnairesController');
        /* ---- END MP Operators Questionnaire Controller ---- */


        Route::get('/private/flagTypes/getIndexTable', 'FlagTypesController@getIndexTable');
        Route::get('/private/flagTypes/{id}/delete', 'FlagTypesController@delete');
        Route::get('/private/flagTypes/{id}/destroy', 'FlagTypesController@destroy');
        Route::resource('/private/flagTypes', 'FlagTypesController');

        Route::post('/private/flags/getElementFlagHistory', 'FlagsController@getElementFlagHistory');
        Route::post('/private/flags/toggleActiveStatus', 'FlagsController@toggleActiveStatus');
        Route::post('/private/flags/getElementAttachFlag', 'FlagsController@getElementAttachFlag');
        Route::post('/private/flags/attachFlag', 'FlagsController@attachFlag');
        Route::get('/private/type/{type}/flags/{cbKey}/getIndexTable', ['as' =>  'private.cbs.flags.table', 'uses' =>'FlagsController@getIndexTable']);
        Route::get('/private/type/{type}/cbs/{cbKey}/flags', ['as' => 'private.cbs.flags.index', 'uses' =>'FlagsController@index']);
        Route::get('/private/type/{type}/cbs/{cbKey}/createFlag', ['as' => 'private.cbs.flags.create', 'uses' =>'FlagsController@create']);
        Route::get('/private/type/{type}/cbs/{cbKey}/showFlag/{flagId}', ['as' => 'private.cbs.flags.show', 'uses' =>'FlagsController@show']);
        Route::get('/private/flags/{id}/delete', 'FlagsController@delete');

        Route::resource('/private/flags', 'FlagsController', ['except' => ['index','create','show']]);


        Route::put('/private/dashBoardElements/reorderUserDashBoardElements', 'DashBoardElementsController@reorderUserDashBoardElements');
        Route::post('/private/dashBoardElements/makeRequestAccordingToDashBoardElement', 'DashBoardElementsController@makeRequestAccordingToDashBoardElement');
        Route::post('/private/dashBoardElements/loadConfigurationsView', 'DashBoardElementsController@loadConfigurationsView');
        Route::delete('/private/dashBoardElements/unsetUserDashBoardElement', 'DashBoardElementsController@unsetUserDashBoardElement');
        Route::post('/private/dashBoardElements/setUserDashBoardElement', 'DashBoardElementsController@setUserDashBoardElement');
        Route::get('/private/dashBoardElements/getIndexTable', 'DashBoardElementsController@getIndexTable');
        Route::get('/private/dashBoardElements/{id}/delete', 'DashBoardElementsController@delete');
        Route::get('/private/dashBoardElements/{id}/destroy', 'DashBoardElementsController@destroy');

        Route::resource('/private/dashBoardElements', 'DashBoardElementsController');



        Route::get('/private/dashBoardElementConfigurations/getIndexTable', 'DashBoardElementConfigurationsController@getIndexTable');
        Route::get('/private/dashBoardElementConfigurations/{id}/delete', 'DashBoardElementConfigurationsController@delete');
        Route::get('/private/dashBoardElementConfigurations/{id}/destroy', 'DashBoardElementConfigurationsController@destroy');
        
        Route::resource('/private/dashBoardElementConfigurations', 'DashBoardElementConfigurationsController');


        /* Modules Controller */
        Route::get('/private/module/table', 'ModulesController@getIndexTable');
        Route::get('/private/module/{key}/delete', 'ModulesController@delete');
        Route::resource('/private/module', 'ModulesController');
        /* ---- END Modules Controller ---- */

        /* Modules Types Controller */

        Route::get('/private/module/{key}/moduleType/table', 'ModuleTypesController@getIndexTable');
        Route::get('/private/moduleType/{key}/delete', 'ModuleTypesController@delete');
        Route::get('/private/module/{key}/showModuleType', ['as' => 'private.modulesTypes.showModuleType', 'uses' => 'ModuleTypesController@showModuleType']);
        Route::get('/private/module/{key}/moduleType/create',  ['as' => 'private.moduleType.create', 'uses' =>'ModuleTypesController@create']);
        Route::resource('/private/moduleType', 'ModuleTypesController', ['except' => ['create']]);
        /* ---- END Modules Types Controller ---- */


        Route::get('/private/wizard', 'PresentationController@show');

        /* ---- CMs Related Routes ---- */
        // Section Types
        Route::get('private/CMSectionTypes/getIndexTable', 'CMSectionTypesController@getIndexTable');
        Route::get('private/CMSectionTypes/{key}/edit', ['as' => 'CMSectionTypes.edit', 'uses' => 'CMSectionTypesController@edit']);
        Route::get('private/CMSectionTypes/{key}/delete', ['as' => 'CMSectionTypes.delete', 'uses' => 'CMSectionTypesController@delete']);
        Route::delete('private/CMSectionTypes/{key}/destroy', 'CMSectionTypesController@destroy');
        Route::resource('private/CMSectionTypes', "CMSectionTypesController",["except"=>["edit"]]);
        
        // Section Type Parameters
        Route::get('private/CMSectionTypeParameters/getIndexTable', 'CMSectionTypeParametersController@getIndexTable');
        Route::get('private/CMSectionTypeParameters/{key}/edit', ['as' => 'CMSectionTypeParameters.edit', 'uses' => 'CMSectionTypeParametersController@edit']);
        Route::get('private/CMSectionTypeParameters/{key}/delete', ['as' => 'CMSectionTypeParameters.delete', 'uses' => 'CMSectionTypeParametersController@delete']);
        Route::delete('private/CMSectionTypeParameters/{key}/destroy', 'CMSectionTypeParametersController@destroy');
        Route::resource('private/CMSectionTypeParameters', "CMSectionTypeParametersController",["except"=>["edit"]]);

        /* Dynamic BackOffice Menu Routes Administration */
        Route::get('private/BEMenuElements/getIndexTable', 'BEMenuElementsController@getIndexTable');
        Route::get('private/BEMenuElements/{key}/delete', ['as' => 'BEMenuElements.delete', 'uses' => 'BEMenuElementsController@delete']);
        Route::delete('private/BEMenuElements/{key}/destroy', 'BEMenuElementsController@destroy');
        Route::resource('private/BEMenuElements', "BEMenuElementsController");

        Route::get('private/BEMenuElementConfigurations/getIndexTable', 'BEMenuElementParametersController@getIndexTable');
        Route::get('private/BEMenuElementConfigurations/{key}/edit', ['as' => 'BEMenuElementConfigurations.edit', 'uses' => 'BEMenuElementParametersController@edit']);
        Route::get('private/BEMenuElementConfigurations/{key}/delete', ['as' => 'BEMenuElementConfigurations.delete', 'uses' => 'BEMenuElementParametersController@delete']);
        Route::delete('private/BEMenuElementConfigurations/{key}/destroy', 'BEMenuElementParametersController@destroy');
        Route::resource('private/BEMenuElementConfigurations', "BEMenuElementParametersController",["except"=>["edit"]]);

        /* Dynamic BackOffice Menu Routes */
        Route::get('private/BEMenu/import', ['as' => 'BEMenuElements.import', 'uses' => 'BEMenuController@import']);
        Route::post('private/BEMenu/getElementParameters', ['as' => 'BEMenuElements.create', 'uses' => 'BEMenuController@getElementParameters']);
        Route::post('/private/BEMenu/updateOrder', 'BEMenuController@updateOrder');
        Route::get('private/BEMenu/{key}/destroy', "BEMenuController@destroy");
        Route::get('private/BEMenu/{key}/delete', ['as' => 'BEMenuElements.delete', 'uses' => 'BEMenuController@delete']);
        Route::resource('private/BEMenu', "BEMenuController");

        /* Dynamic BackOffice User Menu Routes */
        Route::get('private/BEUserMenu', "UserBEMenuController@index")->name("BEUserMenu.index");
        Route::post('private/BEUserMenu', "UserBEMenuController@store")->name("BEUserMenu.create");
        Route::get('private/BEUserMenu/import', ['as' => 'BEUserMenu.import', 'uses' => 'UserBEMenuController@import']);
        Route::get('private/BEUserMenu/create', "UserBEMenuController@create")->name("BEUserMenu.create");
        Route::post('/private/BEUserMenu/updateOrder', 'UserBEMenuController@updateOrder');
        Route::get('private/BEUserMenu/{key}/delete', "UserBEMenuController@delete")->name("BEUserMenu.delete");
        Route::delete('private/BEUserMenu/{key}', "UserBEMenuController@destroy")->name("BEUserMenu.destroy");
        Route::get('private/BEUserMenu/{key}', "UserBEMenuController@show")->name("BEUserMenu.show");
        Route::patch('private/BEUserMenu/{key}', "UserBEMenuController@update")->name("BEUserMenu.update");
        Route::get('private/BEUserMenu/{key}/edit', "UserBEMenuController@edit")->name("BEUserMenu.edit");

        /* User Analysis Data Routes */
        Route::post('private/UserAnalysis/stats', ['as' => 'UserAnalysis.stats', 'uses' => 'UserAnalysisController@getAnalysisStats']);
        Route::resource('private/UserAnalysis', "UserAnalysisController");

        /* ---- START CMs Related Routes ---- */
        Route::get('/private/newContent/getTinyMCE', 'ContentManagerController@getTinyMCE');
        Route::get('/private/newContent/getTinyMCEView/{type?}', 'ContentManagerController@getTinyMCEView');
        Route::post('private/newContent/serveSection', ['as' => 'ContentManager.createSection', 'uses' => 'ContentManagerController@serveSection']);
        Route::get('private/newContent/{contentType}/{topicKey?}', ['as' => 'ContentManager.index', 'uses' => 'ContentManagerController@index'])->where("contentType", $contentTypes);
        Route::get('private/newContent/create/{contentType}/{topicKey?}', ['as' => 'ContentManager.create', 'uses' => 'ContentManagerController@create'])->where("contentType", $contentTypes);
        Route::get('private/newContent/table/{contentType}/{topicKey?}', 'ContentManagerController@getIndexTable')->where("contentType", $contentTypes);
        Route::post('private/newContent/{contentType}', ['as' => 'ContentManager.store', 'uses' => 'ContentManagerController@store'])->where("contentType", $contentTypes);

        Route::get('private/newContent/{contentType}/{contentId}/delete/{topicKey?}', 'ContentManagerController@delete')->where("contentType", $contentTypes);
        Route::delete('private/newContent/{contentType}/{contentId}/delete', 'ContentManagerController@destroy')->where("contentType", $contentTypes);

        Route::get('private/newContent/{contentType}/{contentId}/status/{versionNumber}/{newStatus}/{topicKey?}', 'ContentManagerController@changeVersionActiveStatus')->where(["contentType"=>$contentTypes,"newStatus"=>"0|1"]);
        Route::get('private/newContent/{contentType}/{contentId}/show/{versionNumber?}', ['as' => 'ContentManager.show', 'uses' => 'ContentManagerController@show'])->where("contentType", $contentTypes);
        Route::get('private/newContent/{contentType}/{contentId}/preview/{versionNumber}', ['as' => 'COntentManager.preview', 'uses'=>'ContentManagerController@previewVersion'])->where("contentType", $contentTypes);
        Route::patch('private/newContent/{contentType}/{contentId}/edit/{versionNumber?}', 'ContentManagerController@update')->where("contentType", $contentTypes);
        Route::get('private/newContent/{contentType}/{contentId}/edit/{versionNumber?}', ['as' => 'ContentManager.edit', 'uses' => 'ContentManagerController@edit'])->where("contentType", $contentTypes);
        Route::get('private/newContent/{contentType}/{contentId}/getFiles/{sectionNumber}/{versionNumber}', 'ContentManagerController@getFiles')->where("contentType", $contentTypes);

        Route::resource('private/newContent/pages', 'ContentManagerController', ["except" => ["show", "edit", "index", "store", "create", "destroy", "update"]]);
        Route::resource('private/newContent/news', 'ContentManagerController', ["except" => ["show", "edit", "index", "store", "create", "destroy", "update"]]);
        Route::resource('private/newContent/events', 'ContentManagerController', ["except" => ["show", "edit", "index", "store", "create", "destroy", "update"]]);
        Route::resource('private/newContent/highlights', 'ContentManagerController', ["except" => ["show", "edit", "index", "store", "create", "destroy", "update"]]);
        Route::resource('private/newContent/gatherings', 'ContentManagerController', ["except" => ["show", "edit", "index", "store", "create", "destroy", "update"]]);

        /* ---- END CMs Related Routes ---- */


        Route::get('private/type/{type}/cbs/{cbKey}/cbTranslation/showCbTranslation', ['as' => 'private.cbTranslation.showCbTranslation', 'uses' => 'CbTranslationController@showCbTranslation']);
        Route::post('private/type/{type}/cbs/{cbKey}/cbTranslation/showCbTranslation', ['as' => 'private.cbTranslation.showCbByTranslation', 'uses' => 'CbTranslationController@viewTranslation']);
        Route::post('private/type/{type}/cbs/{cbKey}/cbTranslation/deleteCbTranslation', ['as' => 'private.cbTranslation.deleteCbTranslation', 'uses' => 'CbTranslationController@delete']);
        Route::post('private/type/{type}/cbs/{cbKey}/cbTranslation/showCbCpyTranslation', ['as' => 'private.cbTranslation.showCbCopyTranslation', 'uses' => 'CbTranslationController@viewCpyTranslation']);
        Route::post('private/type/{type}/cbs/{cbKey}/cbTranslation/showCopyTranslations', ['as' => 'private.cbTranslation.viewCopyTranslations', 'uses' => 'CbTranslationController@viewCopyAll']);

        Route::post('private/cbs/{cbKey}/cbTranslation/storeOrUpdateCbTranslation', ['as' => 'private.cbTranslation.storeOrUpdateCbTranslation', 'uses' => 'CbTranslationController@storeOrUpdate']);
        Route::post('private/cbs/{cbKey}/cbTranslation/showCbCopyTranslation', ['as' => 'private.cbTranslation.viewConfirmTranslation', 'uses' => 'CbTranslationController@viewConfirmTranslation']);
        Route::get('private/cbs/{cbKey}/cbTranslation/getCode', ['as' => 'private.cbTranslation.getCode', 'uses' => 'CbTranslationController@getCode']);
        Route::get('private/cbs/{cbKey}/cbTranslation/getStatusTranslations', ['as' => 'private.cbTranslation.getStatusTranslations', 'uses' => 'CbTranslationController@getStatusTranslations']);


        /* ----- CB Menu Translations Routes ----- */
        Route::post('private/type/{type}/cbs/{cbKey}/cbMenuTranslation/getNewTranslationForm', ['as' => 'private.cbMenuTranslation.getNewTranslationForm', 'uses' => 'CbMenuTranslationController@getNewTranslationForm']);
        Route::post('private/type/{type}/cbs/{cbKey}/cbMenuTranslation/deleteCbTranslation', ['as' => 'private.cbMenuTranslation.delete', 'uses' => 'CbMenuTranslationController@delete']);
        Route::post('private/type/{type}/cbs/{cbKey}/cbMenuTranslation/getEntityCbsWithMenuTranslation', ['as' => 'private.cbMenuTranslation.getEntityCbsWithMenuTranslation', 'uses' => 'CbMenuTranslationController@getEntityCbsWithMenuTranslation']);
        Route::post('private/type/{type}/cbs/{cbKey}/cbMenuTranslation/storeOrUpdate', ['as' => 'private.cbMenuTranslation.storeOrUpdate', 'uses' => 'CbMenuTranslationController@storeOrUpdate']);
        Route::post('private/type/{type}/cbs/{cbKey}/cbMenuTranslation/copyMenuTranslationsFromCb', ['as' => 'private.cbMenuTranslation.copyMenuTranslationsFromCb', 'uses' => 'CbMenuTranslationController@copyMenuTranslationsFromCb']);
        Route::get('private/type/{type}/cbs/{cbKey}/cbMenuTranslation/isCodeUsed', ['as' => 'private.cbMenuTranslation.isCodeUsed', 'uses' => 'CbMenuTranslationController@isCodeUsed']);
        Route::get('private/type/{type}/cbs/{cbKey}/cbMenuTranslation', ['as' => 'private.cbMenuTranslation.index', 'uses' => 'CbMenuTranslationController@index']);

        /* ---- Technical Analysis Routes ---- */
        Route::get('/private/type/{type}/cbs/{cbKey}/publishTechnicalAnalysis',['as' => 'private.cbs.publishTechnicalAnalysis.create', 'uses' => 'CbsController@publishTechnicalAnalysisForm']);
        Route::post('/private/type/{type}/cbs/{cbKey}/publishTechnicalAnalysis',['as' => 'private.cbs.publishTechnicalAnalysis.confirm', 'uses' => 'CbsController@publishTechnicalAnalysisConfirmation']);
        Route::post('/private/type/{type}/cbs/{cbKey}/publishTechnicalAnalysisSubmit',['as' => 'private.cbs.publishTechnicalAnalysis.publish', 'uses' => 'CbsController@publishTechnicalAnalysisSubmit']);

        Route::get('/private/type/{type}/cbs/{cbKey}/showQuestions',['as' => 'private.cbs.showQuestions', 'uses' => 'TechnicalAnalysisProcessesController@showQuestions']);
        Route::get('/private/type/{type}/cbs/{cbKey}/question/{techAnalysisQuestionKey}/delete', ['as' => 'question.destroy', 'uses' => 'TechnicalAnalysisProcessesController@delete']);
        Route::get('private/type/{type}/cbs/{cbKey}/question/getIndexTable', 'TechnicalAnalysisProcessesController@getIndexTable');
        Route::resource('private/type/{type}/cbs/{cbKey}/question', 'TechnicalAnalysisProcessesController');

        Route::post('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/technicalAnalysis/{technicalAnalysisKey}/sendNotification', 'TechnicalAnalysisController@sendNotification');
        Route::get('private/topic/entityGroupsTable', 'TechnicalAnalysisController@entityGroupsTable');
        Route::get('private/topic/entityManagersTable', 'TechnicalAnalysisController@entityManagersTable');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/activateVersion/{version?}', 'TechnicalAnalysisController@activateVersion')->name('technicalAnalysis.activateVersion');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/technicalAnalysis/{version}/delete', ['as' => 'technicalAnalysis.destroy', 'uses' => 'TechnicalAnalysisController@delete']);
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/showTechnicalAnalysis/{version?}', 'TechnicalAnalysisController@show')->name('technicalAnalysis.show');
        Route::get('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/technicalAnalysisVerify', 'TechnicalAnalysisController@verifyIfExistsTechnicalAnalysis')->name('technicalAnalysis.verify');
        Route::resource('private/type/{type}/cbs/{cbKey}/topic/{topicKey}/technicalAnalysis', 'TechnicalAnalysisController', ['only' => ['create','edit', 'update', 'store', 'destroy']]);


        /** Entity Messages **/
        Route::get('private/entityMessages/getIndexTable', 'EntityMessagesController@getIndexTable');
        Route::get('private/entityMessages/show/{messageKey}', 'EntityMessagesController@show');
        Route::get("private/entityMessages","EntityMessagesController@index")->name("private.entityMessages.index");
        /* --- END Entity Messages ---- */

        Route::post('/private/operationSchedules/changeStatus', 'OperationSchedulesController@changeStatus') ;
        Route::get('/private/type/{type}/operationSchedules/{cbKey}/getIndexTable', 'OperationSchedulesController@getIndexTable');
        Route::get('/private/type/{type}/cbs/{cbKey}/operationSchedules/{operationScheduleKey}/delete', 'OperationSchedulesController@delete');
        Route::resource('/private/type/{type}/cbs/{cbKey}/operationSchedules', 'OperationSchedulesController');


        /** Short Links **/
        Route::get('private/shortLinks/getIndexTable', 'ShortLinksController@getIndexTable');
        Route::get('private/shortLinks//{shortLinkKey}/delete', 'ShortLinksController@delete');
        Route::resource('/private/shortLinks', 'ShortLinksController');
        
        /* OpenData */
        Route::get("private/openData/list","OpenDataController@index")->name("openData.list");
        Route::get("private/openData/getIndexTable","OpenDataController@getIndexTable");
        Route::get("private/openData/edit","OpenDataController@edit")->name("openData.edit");
        Route::get("private/openData/{entityKey?}","OpenDataController@show")->name("openData.show");
        Route::patch("private/openData/{entityKey?}","OpenDataController@update");

        //Permissions
        Route::post('/private/permissions/updateUserPermission', 'PermissionsController@updateUserPermission');
        Route::post('/private/groupsPermissions/updateGroupPermission', 'PermissionsController@updateGroupPermission');
        Route::get('/private/groupsPermissions/indexGroups', 'PermissionsController@indexGroups');
        Route::get('/private/groupsPermissions/indexUsers', 'PermissionsController@indexUsers');
        //end Permissions

    });

    Route::group(['middleware' => ['authOne']], function ()use($contentTypes) {
        /* Public Short Link Resolver */
        Route::get("/s/{shortCode}","ShortLinksController@resolveShortLink");

        Route::get('/auth/login', 'AuthController@login');



        /* Public CMs Routes */
        Route::get('/newContent/list/{contentType}', 'PublicContentManagerController@index')->where("contentType", $contentTypes);
        Route::get('/newContent/show/{contentType}/{contentKey}', 'PublicContentManagerController@show')->where("contentType", $contentTypes);
        Route::post('/newContent/last', 'PublicContentManagerController@getLastOf');
        /* END Public CMs Routes */

        /*Public C Routes */
        Route::get('/c/{contentKey}', 'PublicContentManagerController@showC');
        /* END Public C Routes */


        Route::post('cb/post/delFile', ['as' => 'private.files.deleteFile', 'uses' => 'PublicPostController@deleteFile']);
        Route::post('cb/post/editFile', 'PublicPostController@editFile');
        Route::post('cb/post/getFileDetails', 'PublicPostController@getFileDetails');
        Route::get('cb/post/getFiles/{postKey}', ['as' => 'public.cbs.getFiles', 'uses' =>'PublicPostController@getFiles']);
        Route::post('cb/post/addFile', ['as' => 'public.cbs.addFile', 'uses' =>'PublicPostController@addFile']);
        Route::post('cb/post/orderFile', ['as' => 'private.cbs.orderFile', 'uses' =>'PublicPostController@orderFile']);

        Route::delete('cb/{cbKey}/topic/{topicKey}/post/{postKey}/destroy', 'PublicPostController@destroy');
        Route::get('cb/{cbKey}/topic/{topicKey}/post/{postKey}/delete', 'PublicPostController@delete');
        Route::post('cb/post/likePost', 'PublicPostController@likePost');
        Route::post('cb/post/dislikePost', 'PublicPostController@dislikePost');
        Route::post('cb/post/deleteLike', 'PublicPostController@deleteLike');
        Route::post('cb/post/showHistory', 'PublicPostController@showHistory');
        Route::post('cb/post/reportAbuse', 'PublicPostController@reportAbuse');
        Route::put('topic/{topicKey}/post', ['as' => 'post.update', 'uses' => 'PublicPostController@update']);
        Route::resource('topic/{topicKey}/post', 'PublicPostController', ['except' => ['update','destroy']]);

        Route::post('cbQuestionnaire/ignoreQuestionnaire', 'PublicCbsController@ignoreQuestionnaire');

        /*
              Route::post('forum/post/likePost', 'PublicPostController@likePost');
              Route::post('forum/post/dislikePost', 'PublicPostController@dislikePost');
              Route::post('forum/post/deleteLike', 'PublicPostController@deleteLike');
              Route::post('forum/post/showHistory', 'PublicPostController@showHistory');
              Route::post('forum/post/reportAbuse', 'PublicPostController@reportAbuse');


              Route::get('forum/{cbId}/topic/{id}/delete', ['as' => 'topic.delete', 'uses' => 'PublicTopicController@delete']);
              Route::resource('forum/{cbId}/topic', 'PublicTopicController');


              Route::get('forum/{cbId}/deleteModerator/{id}', ['as' => 'forum.deleteModerator', 'uses' => 'PublicForumController@deleteModerator']);
              Route::get('forum/{cbId}/deleteModeratorConfirm/{id}', ['as' => 'forum.deleteModerator.confirm', 'uses' => 'PublicForumController@deleteModeratorConfirm']);
              Route::get('forum/{id}/delete', ['as' => 'forum.delete', 'uses' => 'PublicForumController@delete']);
              Route::get('forum/{id}/allUsers', ['as' => 'public.forum.allUsers', 'uses' => 'PublicForumController@allUsers']);
              Route::get('forum/{id}/allManagers', ['as' => 'public.forum.allManagers', 'uses' => 'PublicForumController@allManagers']);
              Route::post('forum/addModerator', 'PublicForumController@addModerator');
              Route::resource('forum', 'PublicForumController');




              Route::get('topic/post/{id}/delete', ['as' => 'post.delete', 'uses' => 'PublicPostController@delete']);
              Route::put('topic/{topicId}/post', ['as' => 'post.update', 'uses' => 'PublicPostController@update']);
              Route::resource('topic/{topicId}/post', 'PublicPostController', ['except' => 'update']);



              Route::get('discussion/{id}/delete', ['as' => 'discussion.delete', 'uses' => 'PublicDiscussionController@delete']);
              Route::get('discussion/{cbId}/deleteModerator/{id}', ['as' => 'discussion.deleteModerator', 'uses' => 'PublicDiscussionController@deleteModerator']);
              Route::get('discussion/{cbId}/deleteModeratorConfirm/{id}', ['as' => 'discussion.deleteModerator.confirm', 'uses' => 'PublicDiscussionController@deleteModeratorConfirm']);
              Route::get('discussion/{id}/allUsers', ['as' => 'public.discussion.allUsers', 'uses' => 'PublicDiscussionController@allUsers']);
              Route::get('discussion/{id}/allManagers', ['as' => 'public.discussion.allManagers', 'uses' => 'PublicDiscussionController@allManagers']);
              Route::get('discussion/{cbId}/topic/{id}/delete', ['as' => 'topic.delete', 'uses' => 'PublicDiscussionTopicController@delete']);
              Route::get('discussionTopic/post/{id}/delete', ['as' => 'post.delete', 'uses' => 'PublicDiscussionPostController@delete']);

              Route::put('discussionTopic/{topicId}/post', ['as' => 'post.update', 'uses' => 'PublicDiscussionPostController@update']);

              Route::post('discussion/addModerator', 'PublicDiscussionController@addModerator');
              Route::post('discussion/post/likePost', 'PublicDiscussionPostController@likePost');
              Route::post('discussion/post/dislikePost', 'PublicDiscussionPostController@dislikePost');
              Route::post('discussion/post/deleteLike', 'PublicDiscussionPostController@deleteLike');
              Route::post('discussion/post/showHistory', 'PublicDiscussionPostController@showHistory');

              Route::resource('discussion', 'PublicDiscussionController');
              Route::resource('discussion/{cbId}/topic', 'PublicDiscussionTopicController');
              Route::resource('discussionTopic/{topicId}/post', 'PublicDiscussionPostController', ['except' => 'update']);

              /* ---- END Public Discussion Controller ---- */



        /* Public Ideas Controller */
        /*
        Route::post('ideas/message/post/vote', 'PublicIdeasMessageController@vote');
        Route::get('ideas/message/{id}/delete', ['as' => 'idea.message.delete', 'uses' => 'PublicIdeasMessageController@delete']);
        Route::put('ideas/message/{ideaId}/post', ['as' => 'idea.message.update', 'uses' => 'PublicIdeasMessageController@update']);
        Route::resource('ideas/{ideaId}/message', 'PublicIdeasMessageController');

        Route::post('ideas/editFile', 'PublicIdeasController@editFile');

        Route::post('ideas/getFileDetails', 'PublicIdeasController@getFileDetails');
        Route::get('ideas/getFiles/{id}', ['as' => 'public.ideas.getFiles', 'uses' =>'PublicIdeasController@getFiles']);
        Route::post('ideas/addFile', ['as' => 'public.ideas.addFile', 'uses' =>'PublicIdeasController@addFile']);
        Route::get('ideas/{cbId}/idea/{id}/delete', ['as' => 'ideas.delete', 'uses' => 'PublicIdeasController@delete']);

        Route::get('ideas/{cbId}/idea/create', ['as' => 'public.ideas.create', 'uses' => 'PublicIdeasController@create']);
        Route::get('ideas/{cbId}/index', ['as' => 'public.ideas.index', 'uses' => 'PublicIdeasController@index']);
        Route::resource('ideas/{cbId}/idea', 'PublicIdeasController', ['only' => ['show', 'edit' ,'update', 'store', 'destroy']]);
        /* ---- END Public Ideas Controller ---- */

        /* ---- Topic Controller ---- */
        Route::get('topic/formSuccess', 'PublicTopicController@formSuccess')->name('topic.formSuccess');
        Route::get('{cbKey}/topic/{topicKey}/{code}/{voteKey}/getQuestionnaireModal', 'PublicTopicController@getQuestionnaireModalData');
        Route::get('topic/register',  'PublicTopicController@registerMessage')->name('topic.registerMessage');

        Route::get('topic/sessionStoreTopic',  'PublicTopicController@sessionStoreTopic')->name('topic.sessionStoreTopic');

        Route::get('topic/getComments', 'PublicTopicController@getTopicComments')->name('topic.getTopicComments');
        Route::post('topic/getVotes', 'PublicTopicController@getTopicVotes')->name('topic.getTopicVotes');
        Route::get('topic/success', 'PublicTopicController@successMessage')->name('topic.successMessage');
        Route::get('topic/{topicKey}/basicInformation', ['as' => 'topic.basicInformation', 'uses' => 'PublicTopicController@basicInformation']);

        Route::post('cb/getPadVotes', ['as' => 'cb.getPadVotes', 'uses' => 'PublicCbsController@getPadVotes']);
        Route::post('cb/voteInTopic', ['as' => 'cb.voteInTopic', 'uses' => 'PublicCbsController@voteInTopic']);
        Route::get('cb/{cbKey}/basicInformation', ['as' => 'cb.basicInformation', 'uses' => 'PublicCbsController@basicInformation']);
        Route::get('cb/{cbKey}/getUserAvailableActions', ['as' => 'cb.basicInformation', 'uses' => 'PublicCbsController@getUserAvailableActions']);
        Route::get('cb/{cbKey}/getPadTopics', ['as' => 'cb.getPadTopics', 'uses' => 'PublicCbsController@getPadTopics']);

        Route::get('cb/{cbKey}/topic/{topicKey}/download/{type?}', ['as' => 'topic.download', 'uses' => 'PublicTopicController@download']);
        Route::post('cb/{cbKey}/topic/vote', ['as' => 'topic.vote', 'uses' => 'PublicTopicController@vote']);
        Route::post('cb/topic/followTopic', 'PublicTopicController@followTopic');
        Route::post('cb/{cbKey}/topic/{topicKey}/revertVersionTopic', ['as' => 'topic.revertVersionTopic', 'uses' => 'PublicTopicController@revertVersionTopic']);
        Route::get('cb/{cbKey}/topic/{topicKey}/delete', ['as' => 'topic.delete', 'uses' => 'PublicTopicController@delete']);
        Route::get('cb/{cbKey}/topic/{topicKey}/publish', ['as' => 'topic.publish', 'uses' => 'PublicTopicController@publish']);
        Route::get('cb/{cbKey}/topic/{topicKey}/updateCooperationStatus',  'PublicTopicController@updateCooperationStatus');


        Route::get('cb/cb/{cbKey}/topic/{topicKey}/ally/create', ["as" => "alliance.create", "uses" => 'PublicTopicController@createAlly']);
        Route::post('cb/cb/{cbKey}/topic/{topicKey}/ally/create', 'PublicTopicController@storeAlly');
        Route::post('cb/cb/{cbKey}/topic/{topicKey}/ally/{allyKey}/respond', 'PublicTopicController@updateAlly');
        Route::post('cb/showTopicDetail','PublicTopicController@getTopicDetailAjax');

        // Route::get('/wizard','PublicController@wizard')->name('wizard.create');
        Route::post('/wizard','PublicController@storeWizard');

        Route::get('cb/type/{type}/cbs/{cbKey}/vote/{voteEventKey}/publicUserVotingRegistration', ['as' => 'public.cb.vote.publicUserVotingRegistration', 'uses' => 'PublicCbsController@publicUserVotingRegistration']);
        Route::post('cb/vote/publicUserVotingRegistrationStoreVotes', ['as' => 'public.cb.vote.publicUserVotingRegistrationStoreVotes', 'uses' => 'PublicCbsController@publicUserVotingRegistrationStoreVotes']);

        Route::resource('cb/{cbKey}/topic', 'PublicTopicController');
        /* ---- END Topic Controller ---- */

        /* ---- Cbs Controller ---- */
        Route::get('cb/{cbKey}/topicsVoted', ['as' => 'topic.map', 'uses' => 'PublicCbsController@showTopicsVoted']);
        Route::post('cb/{cbKey}/showTopicsCbsVoted', ['as' => 'topic.showTopicsCbsVoted', 'uses' => 'PublicCbsController@showTopicsCbsVoted']);
        Route::get('cb/{cbKey}/checkVoteCode', ['as' => 'cbs.voteCode', 'uses' => 'PublicCbsController@checkVoteCodeForm']);
        Route::post('cb/{cbKey}/checkVoteCode', ['as' => 'cbs.voteCode', 'uses' => 'PublicCbsController@checkVoteCode']);
        Route::get('cb/{cbKey}/map', ['as' => 'topic.map', 'uses' => 'PublicCbsController@generalMap']);
        Route::get('cb/list', ['as' => 'cbs.list', 'uses' => 'PublicCbsController@index']);
        Route::get('cb/{cbKey}', ['as' => 'cbs.show', 'uses' => 'PublicCbsController@show']);
        Route::post('cb/simpleSubmitVotes', ['as' => 'topic.vote', 'uses' => 'PublicCbsController@simpleSubmitVotes']);
        Route::post('cb/submitVotes', ['as' => 'topic.vote', 'uses' => 'PublicCbsController@submitVotes']);
        Route::post('cb/genericSubmitVotes',['as' => 'topic.vote','uses'=> 'PublicCbsController@genericSubmitVotes']);
        Route::get('cb/{cbKey}/votesSubmitted',['as' => 'topic.vote','uses'=> 'PublicCbsController@votesSubmittedSuccessfuly']);
        Route::get('cb/{cbKey}/unSubmitVotes',['as' => 'cbs.unSubmitVotes','uses'=> 'PublicCbsController@unSubmitUserVotes']);
        //Route::get('cb/exportToProjects/test',['as' => 'exportProposals','uses'=> 'PublicCbsController@exportProposalsToProjectsHARDCODED']);
        Route::get('cbsWithTopics/list', 'PublicCbsController@showCbsWithTopics');


        Route::post('cb/getCbTopicsList', 'PublicCbsController@getCbTopicsList');
        Route::post('cb/getCbTopicsListMap', 'PublicCbsController@getCbTopicsListMap');
        Route::post('cb/getPilotsForHomePage', 'PublicCbsController@getPilotsForHomePage');
        /* ---- END Cbs Controller ---- */

        /* Second Cycle Controller */
        Route::get('second_cycle/index/{cbKey}', 'SecondCycleController@index');
        Route::get('second_cycle/news/{cbKey}', 'SecondCycleController@news');
        Route::get('second_cycle/faqs/', 'SecondCycleController@showFaqs');
        Route::get('second_cycle/news/{cbKey}/{type}', 'SecondCycleController@showAll');
        Route::get('second_cycle/list_ajax/{cbKey}/{level}', 'SecondCycleController@list_ajax');
        Route::get('second_cycle/show/{cbKey}/{level}/{topicKey}', 'SecondCycleController@show');


        /* ---- END Second Cycle Controller ---- */

        Route::get('auth/confirmMail', ['as' => 'cbs.show', 'uses' => 'AuthController@sendConfirmEmail']);
        Route::post('auth/resetSentSms','AuthController@resetSentSms');
        Route::post('auth/resendConfirmMail','AuthController@resendConfirmEmail');
        Route::get('auth/manuallyConfirmUserEmail/{userKey?}', 'AuthController@manuallyConfirmUserEmail');
        Route::get('auth/manuallyConfirmUserSms/{userKey?}', 'AuthController@manuallyConfirmUserSms');


        /* ---- Public Page Controller ---- */

        Route::get('/public/page', ['as' => 'public.index', 'uses' => 'PublicController@getSubPage']);
        Route::get('/public', ['as' => 'public.index', 'uses' => 'PublicController@index']);
        Route::get('/', ['as' => 'public.index', 'uses' => 'PublicController@index']);
        Route::get('/public/{customViewName}/show/', 'PublicController@showCustomView');

        /* ---- Public User Controller ---- */
        Route::get('/user/sendNewSMSCode', 'AuthController@sendNewSMSCode');
        Route::get('/user/verificationCode', 'PublicUsersController@verificationCode');
        Route::get('/user/verificationLandLine', 'PublicUsersController@verificationLandLine');
        Route::post('user/saveInPersonRegistration', ['as' => 'public.cb.vote.saveInPersonRegistration', 'uses' => 'PublicUsersController@saveInPersonRegistration']);

        Route::post('/user/updatePassword', 'PublicUsersController@updatePassword');
        Route::post('/user/addPhoto', ['as' => 'public.user.addPhoto', 'uses' =>'PublicUsersController@addPhoto']);
        Route::get('/user/removePhoto', ['as' => 'public.user.removePhoto', 'uses' =>'PublicUsersController@removePhoto']);
        Route::post('/user/fileUpload', 'PublicUsersController@fileUpload');
        Route::match(['PUT', 'PATCH'],'/user/{user_key}/updateLevelInfo', 'PublicUsersController@updateLevelInfo');
        Route::get('/user/levelForm/edit', 'PublicUsersController@fillLevelInfo')->name('levelForm.edit');

        Route::get('/user/topics', 'PublicUsersController@userTopics');
        Route::get('/user/topicsUser', 'PublicUsersController@userTopicsNewMethod');
        /**BEGIN MESSAGES PUBLIC**/
        Route::get('/user/messages', 'PublicUsersController@showMessages');
        Route::get('/user/questionnaires', 'PublicUsersController@showQuestionnaires');
        Route::post('/user/markMessagesAsSeen', 'PublicUsersController@markMessagesAsSeen');
        Route::post('/user/sendMessage', 'PublicUsersController@sendMessage');
        Route::post('/user/deleteMessage', 'PublicUsersController@deleteMessage');
        /**END MESSAGES PUBLIC**/

        Route::get('/user/timeline/{type}', 'PublicUsersController@showTimeline')->where("type","votes|posts|topics");
        Route::post('/user/{userKey}/updatePrivacy', 'PublicUsersController@updatePrivacy');
        Route::get('/user/{userKey}/profile', 'PublicUsersController@publicProfile');
        Route::resource('/user', 'PublicUsersController', ['only' => ['index','edit', 'show','update']]);
        /* ---- END Public User Controller ---- */

        /* ---- Public Conf Events Controller ---- */
        Route::get('/confEvent/{eventKey}/register', ['as' => 'public.conferenceEvents.confEventRegistration', 'uses' => 'PublicConfEventsController@setRegistration']);

        Route::resource('/confEvent/{eventKey}/registration', 'RegistrationsController', ['only' => ['create','store']]);
        Route::get('/confEvent/{eventKey}', ['as' => 'public.conferenceEvents.confEvent', 'uses' => 'PublicConfEventsController@show']);
        /* ---- END Public Conf Events Controller ---- */


        /* Public Contents */
        Route::post('/content/showNewsListByType', 'PublicContentsController@showNewsListByType');
        Route::post('/content/showEventsListByType', 'PublicContentsController@showEventsListByType');


        Route::get('/content/preview/{id?}/{currVersion?}', ['as' => 'private.contents.preview', 'uses' => 'PublicContentsController@previewPage']);
        Route::get('/content/showContentsList', ['as' => 'contents.shownewslist', 'uses' =>'PublicContentsController@showContentsList']);

        //TODO: deprecated - remove ASAP
        Route::get('/content/shownewslist/{contentKey?}', ['as' => 'contents.shownewslist', 'uses' =>'PublicContentsController@showNewsList']);
        Route::get('/content/showeventslist', ['as' => 'contents.showeventslist', 'uses' =>'PublicContentsController@showEventsList']);
        //END TODO

        Route::get('/content/{key}', ['as' => 'contents.show', 'uses' => 'PublicContentsController@show']);
        /* END - Public Contents */


        /* Page - Static page engine controller */
        Route::get('/page/{folder}/{page}', 'SubPagesController@show');

        Route::get('/page/{type}', 'SiteEthicsController@showPublicSiteEthic');

        /* Questionnaires Controller */
        Route::get('/questionnaire/{key}/showAnswers/{answer_key}', 'PublicQController@showAnswers');
        Route::get('/questionnaire/{key}/showRepliesByGeoCode', 'PublicQController@showRepliesByGeoCode');
        Route::get('/questionnaire/{questionnaireKey}/getFormRepliesLocations', 'PublicQController@getFormRepliesLocations');
        Route::get('/questionnaire/{key}/showDetailsAnswers/{answer_key}', 'PublicQController@showDetailsAnswers');
        Route::get('/questionnaire/{key}/publicDownloadPdfAnswer/{answer_key}', 'PublicQController@downloadPdfAnswer');
        Route::post('/questionnaire/storeStep', 'PublicQController@storeStep');
        Route::post('/questionnaire/submitAnswer', 'PublicQController@submitAnswer');
        Route::get('/questionnaire/success', 'PublicQController@success');
        Route::get('/questionnaire/getQuestion', 'PublicQController@getQuestion');
        Route::get('/questionnaire/{id}/intro', ['as' => 'questionnaire.intro', 'uses' => 'PublicQController@intro']);
        Route::get('/questionnaire/{id}', ['as' => 'questionnaire.showQ', 'uses' => 'PublicQController@showQ']);

        Route::get('/questionnaire/{key}/{userKey}/{uniqueKey}', 'PublicQController@autoLoginQ')->name('questionnaire.uniqueLogin');

        Route::resource('/questionnaire', 'PublicQController', ['only' => ['store']]);
        /* END Questionnaires Controller */


        /* Public Contents Controller */


        /* Public Event Schedule Controller */
        Route::put('/eventSchedule/attendance/{key}', ['as' => 'public.eventSchedule.attendance', 'uses' => 'EventSchedulesController@publicUpdateAttendance']);
        Route::post('/eventSchedule/attendance/{key}', ['as' => 'public.eventSchedule.attendance', 'uses' => 'EventSchedulesController@publicStoreAttendance']);
        Route::get('/eventSchedule/deleteAttendance/{eventKey}/{key?}', ['as' => 'public.eventSchedule.attendance', 'uses' => 'EventSchedulesController@publicDeleteAttendance']);
        Route::delete('/eventSchedule/deleteAttendance/{eventKey}/{key}', ['as' => 'public.ventSchedule.attendance', 'uses' => 'EventSchedulesController@publicDestroyAttendance']);
        Route::get('/eventSchedule/attendance/{key}',  ['as' => 'public.eventSchedule.attendance', 'uses' => 'EventSchedulesController@publicAttendance']);
        /* END Event Schedule Controller */

        /* Auth Controller */
        Route::post('/auth/verifyLoginCode', ['as' => 'auth.verifyLoginCode', 'uses' => 'AuthController@verifyLoginCode']);
        Route::get('/auth/verifyLoginCode/{code}', ['as' => 'auth.verifyLoginCodeLink', 'uses' => 'AuthController@verifyLoginCodeLink']);
        Route::post('/auth/verifyRegister', ['as' => 'auth.verifyRegister', 'uses' => 'AuthController@verifyRegister']);
        Route::post('/auth/verifyRegisterAndLogin', 'AuthController@verifyRegisterAndLogin');
        Route::post('/auth/questionnaireRegisterAndLogin', 'AuthController@questionnaireRegisterAndLogin');
        Route::post('/auth/questionnaireVerifyAndLogin', 'AuthController@questionnaireVerifyAndLogin');

        Route::get('/auth/confirmEmail/{confirmationCode}', 'AuthController@confirmEmail');
        Route::get('/auth/{userKey}/confirmEmailUserList/{confirmationCode}', 'AuthController@confirmEmailUserList');

        Route::get('/auth/user/{userKey}/token/{recoverToken}', 'AuthController@editPassword');
        Route::get('/auth/recovery', 'AuthController@recovery');
        Route::get('/auth/register', 'AuthController@register');
        Route::get('/auth/privacyPolicy', 'AuthController@privacyPolicy');
        Route::get('/auth/useTerms', 'AuthController@useTerms');
        Route::post('/auth/user/updatePassword', 'AuthController@updatePassword');
        Route::post('/auth/passwordRecovery', 'AuthController@passwordRecovery');
        Route::post('/auth/registerAndReedirect', 'AuthController@registerAndReedirect');

        Route::get('/auth/accountRecovery', 'AuthController@accountRecovery');
        Route::post('/auth/accountRecovery/{step}', 'AuthController@accountRecoverySteps');

        /** REGISTER STEPPER BEGIN */
        Route::get('/auth/{step}/stepperManager', 'AuthController@stepperManager');
        Route::get('/auth/registerStepper', 'AuthController@stepperRegister');
        Route::get('/auth/showSuccess', 'AuthController@showSuccess');
        Route::get('/auth/deleteUserParameters', 'AuthController@deleteUserParameters');
        Route::get('/auth/registerStepperAdditionalFields', 'AuthController@stepperRegisterAdditionalFields');
        Route::post('/auth/validateVatNumber', 'AuthController@validateVatNumber');
        Route::post('/auth/validateDomainName', 'AuthController@validateDomainName');
        Route::post('/auth/validateMobileNumber', 'AuthController@validateMobileNumber');
        /** REGISTER STEPPER END */


        Route::resource('/auth', 'AuthController@index');
        /* ---- END Auth Controller ---- */

        /* ---- Newsletter Controller ---- */
        Route::post('newsletter/register', 'NewsletterController@register');
        /* ---- END One Controller ---- */

        /* ---- One Controller ---- */
        Route::post('one/setLanguage', 'OneController@setLanguage');

        /* ---- END One Controller ---- */

        Route::get('kioskSite/voteAnalysis', 'PublicKioskController@showResults');
        Route::post('kioskSite/login', 'PublicKioskController@login');
    });
    /* ---- Files Controller ---- */
    Route::post('uploadFiles', 'FilesController@upload');
    Route::get('file/download', 'FilesController@downloadFile');
    Route::get('files/{id}/{code}/{inline?}', 'FilesController@download');
    /* ---- END Files Controller ---- */
});

Route::group(['middleware' => ['web', 'kioskSite']], function () {

    Route::get('kioskSite/listIdeas', 'PublicKioskController@listVote');
    Route::post('kioskSite/verifyVote', 'PublicKioskController@verifyVote');
    Route::post('kioskSite/submitVotes', 'PublicKioskController@submitVotes');
    Route::post('kioskSite/verifyVoteMade', 'PublicKioskController@verifyVoteMade');
    Route::get('kioskSite/participate', 'PublicKioskController@participate');
});



/* ---- KioskHandler Controller ---- */
//authentication/logout
Route::post('kioskHandler/authenticateRFID', 'KioskHandlerController@authenticateRFID');
Route::get('kioskHandler/logout', 'KioskHandlerController@logout');
//userinformation
Route::get('kioskHandler/getUserParameters/{userKey}', 'KioskHandlerController@getUserParameters');
//kiosk information
Route::get('kioskHandler/orchestratorKiosk/{kioskKey}', 'KioskHandlerController@getOrchestratorKiosk');
//kiosk information to mobile
Route::post('kioskHandler/afterLoginGetKioskInformationToMobile', 'KioskHandlerController@afterLoginGetKioskInformationToMobile');
Route::post('kioskHandler/getKioskInformationToMobile', 'KioskHandlerController@getKioskInformationToMobile');
Route::post('kioskHandler/loginWithQRCode', 'KioskHandlerController@loginWithQRCode');
Route::post('kioskHandler/loginWithCredentials', 'KioskHandlerController@loginWithCredentials');

//topic information
Route::get('kioskHandler/topicListWithFirst/{cbKey}', 'KioskHandlerController@topicListWithFirst');
Route::get('kioskHandler/getTopicParameters/{topicKey}', 'KioskHandlerController@getTopicParameters');
//to get vote information
Route::get('kioskHandler/getCbVotes/{cbKey}', 'KioskHandlerController@getCbVotes');
Route::get('kioskHandler/getVoteStatus/{eventKey}', 'KioskHandlerController@getVoteStatus');
Route::post('kioskHandler/eventVotes/{eventKey}', 'KioskHandlerController@eventVotes');
Route::get('kioskHandler/voteEventOpen/{eventKey}', 'KioskHandlerController@voteEventOpen');
Route::get('kioskHandler/voteEvent/{eventKey}', 'KioskHandlerController@voteEvent');
//get vote configuration
Route::post('kioskHandler/getAllShowEvents/{eventKey}', 'KioskHandlerController@getAllShowEvents');
//to vote
Route::post('kioskHandler/vote', 'KioskHandlerController@vote');
/* ---- END KioskHandler Controller ---- */

Route::get('performance/clear', 'PerformanceController@delete');
Route::get('performance', 'PerformanceController@index');
/* ---- END KioskHandler Controller ---- */

/* Performance Controller */
Route::get('PerformanceController/saveDataToDB', 'PerformanceController@saveDataToDB');
Route::get('PerformanceController/getPerformanceFromDB/{filter}', 'PerformanceController@getPerformanceFromDB');
Route::get('PerformanceController/getDataPerformanceAndSave', 'PerformanceController@getDataPerformanceAndSave');
Route::post('PerformanceController/loadDataPerformance', 'PerformanceController@loadDataPerformance');
Route::post('PerformanceController/loadDataPerformanceBars', 'PerformanceController@loadDataPerformanceBars');
Route::post('PerformanceController/loadAllServers', 'PerformanceController@loadAllServers');
/* ---- END Performance Controller ---- */



/* "Direct" Access APIs */
Route::group(['middleware' => ['authOne']], function() {
    Route::post("/api/sms/receive",function(Illuminate\Http\Request $request){
        try {
            $data = array(
                /* Provider Data */
                "sms_id"    => ($request->has("smsid"))? $request->get("smsid"):"0",        //MOM_ID
                "sms_date" => $request->get("received_at"),  //TIMESTAMP

                /* SMS Data */
                "content"  => preg_replace('/[^A-Za-z0-9\. -]/', '', $request->get("content")),      //ALL_WORDS
                "sender"   => $request->get("sender"),       //FROM_IMSISDN
                "receiver" => $request->get("receiver"),      //TO_MSISDN

                /* Headers Data */
                "event"    => $request->header("event")
            );

            \Log::info("SMS [Id:" . $data["sms_id"] . "] " . $data["sender"] . "####" . preg_replace('/[^A-Za-z0-9\. -]/', '', $request->get("content")));
            
            \App\ComModules\Notify::storeReceivedSMS($data);
            return response("",200);
        } catch(\Exception $e) {
            \Log::info($e);
            return response("",500);
        }
    });
    Route::get("/api/openData/{token}","OpenDataController@export");
});



