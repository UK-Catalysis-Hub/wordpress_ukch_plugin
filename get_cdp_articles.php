<?php
   /*
   Plugin Name: Get CDP articles bibliographic list
   GitHub Plugin URI: https://github.com/UK-Catalysis-Hub/wordpress_ukch_plugin
   description: Plugin to get data from comunity data portal. Call it as [articles_list theme="Transformations" year="2018" title="Transformations"]
   Version: 0.31
   Author: Abraham Nieva de la Hidalga
   Author URI: https://github.com/UK-Catalysis-Hub
   License: CC0
   */
   
defined('ABSPATH') || die('unauthorised access');

// Action when user logs into admin panel
add_shortcode('articles_list', 'get_articles');

function get_articles($atts) {
    // Set default attributes
    $defaults = array(
		'title' => '',
		'action' => 'get_pubs',
		'year' => '',
		'theme' => '',
        );

    $atts = shortcode_atts($defaults, $atts);

    $params = array();

    if (!empty($atts['theme'])){
        $params['theme'] =  $atts['theme'];
    }

	if (!empty($atts['year'])){
		$params['year'] =  $atts['year'];
    }

    // call api and get articles data
    $results = get_articles_data ($atts['action'] . '.json', $params);

	$html = "";

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
		if ( $result["doi"]!="" ) {
			$html .=  "DOI: <a href=\"https://doi.org/". $result["doi"] ."\">" . $result["doi"] . " </a>";
		}
		$html .= "</p>";
	}

    // Build the output
    return $html;
}

function get_articles_data( $action, $params ) {
 
    $api_endpoint = "http://188.166.149.246/";
 
    if ( null == $params ) {
        $params = array();
    }
 
    // Create URL with params
    $url = $api_endpoint . $action . '?' . http_build_query($params);

    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
        return 'Error retrieving data.';
    }

    // get body from response
    $body = wp_remote_retrieve_body($response);
    // Decode output into an array
    $json_data = json_decode($body, true);

    // Check for JSON errors
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        return 'Invalid JSON response.';
    }
    return $json_data;
}