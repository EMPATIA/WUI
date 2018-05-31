<?php

namespace App\ComModules;

use App\One\One;
use Illuminate\Support\Collection;


use Illuminate\Http\Request;

use Datatables;
use Session;
use View;
use Breadcrumbs;
use Exception;



class Events {

    /*** EVENTS ***/
    public static function setNewEvent($request,$contentTranslation){
        $response = ONE::post([
            'component' => 'events',
            'api' => 'event',
            'params' => [
                'start_date' => $request->input("startDate"),
                'end_date' => $request->input("endDate"),
                'file_id' => $request->input("fileId"),
                'translations' => $contentTranslation
            ]
        ]);
        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToSetNewEvent"));
        }
        return $response->json()->event_key;
    }

    public static function getEvent($eventKey){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'event',
            'attribute' => $eventKey
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToGetEvent"));
        }

        return $response()->json();
    }

    public static function updateEvent($eventKey, $request, $contentTranslation){
        $response = ONE::put([
            'component' => 'events',
            'api' => 'event',
            'attribute' => $eventKey,
            'params' => [
                'start_date' => $request->input("startDate"),
                'end_date' => $request->input("endDate"),
                'file_id' => $request->input("fileId"),
                'translations' => $contentTranslation
            ]
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToUpdateEvent"));
        }
        return $response->json();
    }

    public static function deleteEvent($eventKey){
        $response = ONE::delete([
            'component' => 'events',
            'api' => 'event',
            'attribute' => $eventKey
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToDeleteEvent"));
        }
        return $response->json();
    }

    public static function listEvents($langCode){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'event',
            'method' => 'list',
            'params'    => ["lang" => $langCode]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToListEvents"));
        }
        return $response->json()->data;
    }
    /*** END EVENTS ***/

    /*** SESSION ***/
    public static function getSession($sessionKey){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'session',
            'attribute' => $sessionKey
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToGetSession"));
        }
        return $response->json();
    }

    public static function storeSession($eventKey, $contentTranslation, $schedules){
        $response = ONE::post([
            'component' => 'events',
            'api' => 'session',
            'params' => [
                'event_key' => $eventKey,
                'translations' => $contentTranslation,
                'schedules' => $schedules
            ]
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToSetSession"));
        }
        return $response->json();
    }

    public static function updateSession($sessionKey, $contentTranslation, $schedules){
        $response = ONE::put([
            'component' => 'events',
            'api' => 'session',
            'attribute' => $sessionKey,
            'params' => [
                'translations' => $contentTranslation,
                'schedules' => $schedules
            ]
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToUpdateSession"));
        }
        return $response->json();
    }

    public static function deleteSession($sessionKey){
        $response = ONE::delete([
            'component' => 'events',
            'api' => 'session',
            'attribute' => $sessionKey
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToDeleteSession"));
        }
        return $response->json();
    }

    public static function listSessions($langCode, $eventKey){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'event',
            'method' => 'showSessions',
            'attribute' => $eventKey,
            'params'    => ["lang" => $langCode]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToListSessions"));
        }
        return $response->json()->sessions;
    }
    /*** END SESSION ***/

    /*** SPEAKER ***/
    public static function getSpeaker($speakerKey){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'speaker',
            'attribute' => $speakerKey,
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToGetSpeaker"));
        }
        return $response->json();
    }

    public static function setSpeaker($request){
        $response = ONE::post([
            'component' => 'events',
            'api' => 'speaker',
            'params' => $request->all()
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToSetSpeaker"));
        }
        return $response->json();
    }

    public static function updateSpeaker($request, $speakerKey){
        $response = ONE::put([
            'component' => 'events',
            'api' => 'speaker',
            'attribute' => $speakerKey,
            'params' => $request->all()
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToUpdateSpeaker"));
        }
        return $response->json();
    }

    public static function deleteSpeaker($speakerKey){
        $response = ONE::delete([
            'component' => 'events',
            'api' => 'speaker',
            'attribute' => $speakerKey
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToDeleteSpeaker"));
        }
        return $response->json();
    }

    public static function listSpeakers($sessionKey){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'session',
            'method' => 'showSpeakers',
            'attribute' => $sessionKey,
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToListSpeaker"));
        }
        return $response->json()->speakers;
    }
    /*** END SPEAKER ***/

    /*** SPONSOR ***/
    public static function getSponsor($sponsorKey){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'sponsor',
            'attribute' => $sponsorKey,
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToGetSponsor"));
        }
        return $response->json();
    }

    public static function setSponsor($request){
        $response = ONE::post([
            'component' => 'events',
            'api' => 'sponsor',
            'params' => $request->all()
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToSetSponsor"));
        }
        return $response->json();
    }

    public static function updateSponsor($request, $sponsorKey){
        $response = ONE::put([
            'component' => 'events',
            'api' => 'sponsor',
            'attribute' => $sponsorKey,
            'params' => $request->all()
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToUpdateSponsor"));
        }
        return $response->json();
    }

    public static function deleteSponsor($sponsorKey){
        $response = ONE::delete([
            'component' => 'events',
            'api' => 'sponsor',
            'attribute' => $sponsorKey
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToDeleteSponsor"));
        }
        return $response->json();
    }

    public static function listSponsors($eventKey){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'event',
            'method' => 'showSponsors',
            'attribute' => $eventKey,
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToListSponsors"));
        }
        return $response->json()->sponsors;
    }
    /*** END SPONSOR ***/

    public static function getConstructor($eventKey){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'event',
            'method' => 'constructor',
            'attribute' => $eventKey
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToGetConstructor"));
        }
        return $response->json()->sponsors;
    }

    public static function verifyRegistration($eventKey){
        $response = ONE::get([
            'component' => 'events',
            'api' => 'event',
            'api_attribute' => $eventKey,
            'method' => 'verifyRegistration'
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToVerifyRegistration"));
        }
        return $response->json();
    }

    public static function setRegistration($eventKey){
        $response = ONE::post([
            'component' => 'events',
            'api' => 'registration',
            'params' => [
                'event_key'=> $eventKey
            ]
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToSetRegistration"));
        }
        return $response->json();
    }

    public static function getRegistrations($eventKey){
        $response = ONE::get([
            'component' => 'events',
            'api'       => 'event',
            'method'    => 'showRegistrations',
            'attribute' => $eventKey,
        ]);

        if($response->statusCode()!= 200){
            throw new Exception(trans("comModulesEvents.errorFailedToGetRegistrations"));
        }
        return $response->json();
    }

    public static function storeRegistration($params){
        $response = ONE::post([
            'component' => 'events',
            'api'       => 'registration',
            'params'    => $params
        ]);

        if($response->statusCode()!= 201){
            throw new Exception(trans("comModulesEvents.errorFailedToStoreRegistration"));
        }
        return $response->json();
    }
}
