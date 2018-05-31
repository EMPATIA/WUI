<?php

namespace App\Unimi;

class SecondCycleParameters{

	private static $graph = array(
		"projects" => array("subprojects"),
		"subprojects" => array("documents", "expenditures", "phases","news")
	);

	private static $parameters = array(
		"projects" => array(
			array("code" => "details", "mandatory" => 0, "visible" => 1, "type" => "text_area","position" => 0,
	       			"name" => array("language" => array("it" => "Details","en" => "Dettagli")),
				"description" => array("language" => array("it" => "Dettagli", "en" => "Details")),
			),
			array("code" => "budget", "mandatory" => 1, "visible" => 1, "type" => "coin","min_value" => 0,"position" => 1,
	       			"name" => array("language" => array("it" => "Costo","en" => "Cost")),
				"description" => array("language" => array("it" => "Costo del progetto", "en" => "Budget for project")),
			),
			array("code" => "num_votes", "mandatory" => 1, "visible" => 1, "type" => "numeric","min_value" => 0,"position" => 2,
	       			"name" => array("language" => array("it" => "Numero voti","en" => "Number votes")),
				"description" => array("language" => array("it" => "Numero voti", "en" => "Vote Number")),
			),
			array("code" => "contacts", "mandatory" => 1, "visible" => 1, "type" => "text_area","position" => 3,
	       			"name" => array("language" => array("it" => "Contatti","en" => "Contacts")),
				"description" => array("language" => array("it" => "Contatti", "en" => "Contatti")),
			),
			array("code" => "location", "mandatory" => 0, "visible" => 1, "type" => "google_maps","position" => 5,
	       			"name" => array("language" => array("it" => "Posizione","en" => "Location")),
				"description" => array("language" => array("it" => "Posizione", "en" => "Position")),
			),
            array("code" => "category", "mandatory" => 1, "visible" => 1, "type" => "category","position" => 6,
                "name" => array("language" => array("it" => "Zona","en" => "Zone")),
                "description" => array("language" => array("it" => "Zona", "en" => "Zone")),
            ),
		),
		"subprojects" => array(
			array("code" => "details", "mandatory" => 0, "visible" => 1, "type" => "text_area","position" => 2,
	       			"name" => array("language" => array("it" => "Dettagli","en" => "Dettagli")),
				"description" => array("language" => array("it" => "Dettagli", "en" => "Details")),
			),
			array("code" => "budget", "mandatory" => 1, "visible" => 1, "type" => "coin","min_value" => 0,"position" => 3,
	       			"name" => array("language" => array("it" => "Costo","en" => "Cost")),
				"description" => array("language" => array("it" => "Costo dell'intevento", "en" => "Budget for subproject")),
			),
			array("code" => "contacts", "mandatory" => 1, "visible" => 1, "type" => "text_area","position" => 3,
	       			"name" => array("language" => array("it" => "Contatti","en" => "Contacts")),
				"description" => array("language" => array("it" => "Contatti", "en" => "Contatti")),
			),
			array("code" => "state", "mandatory" => 1, "visible" => 1, "type" => "dropdown","position" => 4,
	       			"name" => array("language" => array("it" => "Stato","en" => "State")),
				"description" => array("language" => array("it" => "Stato dei lavori", "en" => "State")),
				"options_select" => array(
					array("code" => "in_progress","name" => array("language" => array("it" => "In corso","en" => "In progress"))),
					array("code" => "not_started", "name" => array("language" => array("it" => "Da iniziare","en" => "Not started"))),
					array("code" => "closed", "name" => array("language" => array("it" => "Concluso","en" => "Closed"))),

				)
			),
			array("code" => "location", "mandatory" => 0, "visible" => 1, "type" => "google_maps","position" => 5,
	       			"name" => array("language" => array("it" => "Posizione","en" => "Location")),
				"description" => array("language" => array("it" => "Posizione", "en" => "Position")),
			),
		),
		"documents" => array(

		),
		"expenditures" => array(
			array("code" => "budget", "mandatory" => 1, "visible" => 1, "type" => "coin","min_value" => 0,
	       			"name" => array("language" => array("it" => "Costo","en" => "Cost")),
				"description" => array("language" => array("it" => "Spesa", "en" => "Expenditure")),
			),
		),
		"news" => array(
			array("code" => "type", "mandatory" => 1, "visible" => 1, "type" => "dropdown","position" => 0,
	       			"name" => array("language" => array("it" => "Tipo","en" => "Type")),
				"description" => array("language" => array("it" => "Tipo news", "en" => "Type news")),
				"options_select" => array(
					array("code" => "type1","name" => array("language" => array("it" => "Obiettivo raggiunto","en" => "Target completed"))),
					array("code" => "type2", "name" => array("language" => array("it" => "Criticità","en" => "Danger"))),
					array("code" => "type3", "name" => array("language" => array("it" => "Criticità superata","en" => "Danger is history"))),

				)
			),
		),
		"phases" => array(
			array("code" => "state", "mandatory" => 1, "visible" => 1, "type" => "dropdown","position" => 3,
	       			"name" => array("language" => array("it" => "Stato","en" => "State")),
				"description" => array("language" => array("it" => "Stato della fase", "en" => "Phase state")),
				"options_select" => array(
					array("code" => "deleted", "name" => array("language" => array("it" => "Cancellato","en" => "Deleted"))),
					array("code" => "in_progress","name" => array("language" => array("it" => "In corso","en" => "In progress"))),
					array("code" => "not_started", "name" => array("language" => array("it" => "Non iniziato","en" => "Not started"))),
					array("code" => "closed", "name" => array("language" => array("it" => "Chiuso","en" => "Closed"))),

				)
			),
			array("code" => "real_start_date", "mandatory" => 0, "visible" => 1, "type" => "date","position" => 0,
	       			"name" => array("language" => array("it" => "Data reale inizio","en" => "Real start date")),
				"description" => array("language" => array("it" => "Data reale inizio", "en" => "Real start date")),
			),
			array("code" => "real_end_date", "mandatory" => 0, "visible" => 1, "type" => "date","position" => 1,
	       			"name" => array("language" => array("it" => "Data reale fine","en" => "Real end date")),
				"description" => array("language" => array("it" => "Data reale fine", "en" => "Real end date")),
			),
			array("code" => "output", "mandatory" => 0, "visible" => 1, "type" => "text","position" => 4,
	       			"name" => array("language" => array("it" => "Descrizione output","en" => "Description output")),
				"description" => array("language" => array("it" => "Descrizione output", "en" => "Descrizione output")),
			),
		)

	);

	private static $configurations = array(
		"subprojects" => array("public_access","allow_files","allow_follow","allow_video_link","allow_share","allow_pictures"),
		"documents" => array("public_access","allow_files","allow_video_link","allow_share","allow_pictures"),
		"expenditures" => array("public_access","allow_files","allow_video_link","allow_share","allow_pictures"),
		"phases" => array("public_access","allow_files","allow_video_link","allow_share","allow_pictures"),
		"news" => array("public_access","allow_video_link","allow_share"),

	);
	
	private static $root_level = "projects";

	public static function getGraph(){
		return self::$graph;
	}

	public static function getParameters(){
		return self::$parameters;
	}

	public static function getConfigurations(){
		return self::$configurations;
	}

	public static function getRootLevel(){
		return self::$root_level;
	}

	public static function getJSON(){
		return json_encode(array(
			"root_level" => self::$root_level,
			"configurations" => self::$configurations,
			"parameters" => self::$parameters,
			"graph" => self::$graph
		));
	}
}
