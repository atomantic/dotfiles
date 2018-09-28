<?php
/*
 * Concept 			Design and such
 * Developper		Thijs de Jong
 * url 				www.designandsuch.com
 * contact 			info@designandsuch.com
 */

function units($query = null)
{
	$postfields = array(
			// 'q' => json_encode($query),
			'q' => $query,
			'v' => '3.4'
		);

	$post_string_count = count($postfields);

	$url = 'http://units.dnsu.ch/units/server/alfred';

	$defaults = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_URL => $url,
		CURLOPT_FRESH_CONNECT => true,
		CURLOPT_POST => $post_string_count,
		CURLOPT_POSTFIELDS => $postfields,
	);

	$ch  = curl_init();
	curl_setopt_array($ch, $defaults);
	$out = curl_exec($ch);
	$err = curl_error($ch);
	curl_close($ch);

	$json = json_decode($out);
	if($json->items){ return $out; }

	$error = array(
		'title' => 'Units is unavailable',
		'subtitle' => 'sorry..',
		'icon' => array(
			'path' => 'Icons/error.png'
			)
		);
	return json_encode(['items'=>[$error]]);
}