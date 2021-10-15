<?php
   /*
   Plugin Name: UKCH CDI Client API
   GitHub Plugin URI: https://github.com/UK-Catalysis-Hub/wordpress_ukch_plugin
   description: >-
    Plugin to get data from UKCH CDI
   Version: 0.1
   Author: Abraham Nieva de la Hidalga
   Author URI: https://github.com/UK-Catalysis-Hub
   License: CC0
   */
   
defined('ABSPATH') || die('unauthorised access');

// Action when user logs into admin panel
add_shortcode( 'ukch_articles', 'get_articles_data' );

function get_articles_data($atts){
	
	$defaults = [
		'title' => 'Table Title',
		'action' => 'get_pubs',
		'year' => '2020',
		'theme' => 'BAG'
	];
	$atts = shortcode_atts($defaults, $atts, 'ukch_articles');
	
	$params = array(
		'theme' =>  $atts['theme'],
		'year' =>  $atts['year']
	);	
	
	$results = get_articles ($atts['action'] . '.json', $params);

	$html = "";
	$html = "<h2>" . $atts['title']." - "  . $atts['year'] . "</h2>";
	//parse the data and return a list of paragraphs
	foreach ($results as $result){
		$html .= "<p>";
		$html .= $result["authors"];
		$html .=  "(" . $result["year"] . "). ";
		$html .= "<b>" . $result["title"] . "</b>, ";
		$html .=  $result["publisher"] . ", ";
		if ( $result["volume"]!="" ) {
			$html .=  "vol. " . $result["volume"] . ", ";
		}
		if ( $result["issue"]!="" ) {
			$html .=  "issue " . $result["issue"] . ", ";
		}
		if ( $result["page"]!="" ) {
			$html .=  "page " . $result["issue"] . ". ";
		}
		$html .=  "DOI: <a href=\"https://doi.org/". $result["doi"] ."\">" . $result["doi"] . " </a>";
		$html .= "</p>";
	}
	return $html;
}

function get_articles( $action, $params ) {
 
    $api_endpoint = "http://188.166.149.246/";
 
    if ( null == $params ) {
        $params = array();
    }
 
    // Create URL with params
    $url = $api_endpoint . $action . '?' . http_build_query($params);
 
    // Use curl to make the query
    $ch = curl_init();
 
    curl_setopt_array(
        $ch, array( 
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        )
    );
 
    $output = curl_exec($ch);
 
    // Decode output into an array
    $json_data = json_decode( $output, true );
 
    curl_close( $ch );
 
    return $json_data;
}