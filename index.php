<?php
require_once("vendor/autoload.php");

use \PageSpeed\Insights;
use \Tinify;

define('ABSPATH', dirname(__FILE__) . '/');

ini_set("memory_limit",-1);
set_time_limit(0);
error_reporting(E_ALL);

if ( ! file_exists( ABSPATH . 'config.php' ) ):
 	echo "missing file config.php\n";
 	exit(1);
endif;

$params = include ABSPATH . 'config.php';

Tinify\setKey($params['TINY_KEY']);

$current_host = preg_replace(array('#https?://#' ,'#/$#'), '', $params['DOMAIN']);

$pageSpeed = new Insights\Service();
$result = $pageSpeed->getResults($params['DOMAIN']);

$args = $result['formattedResults']['ruleResults']['OptimizeImages']['urlBlocks'][0]['urls'];

foreach ( $args as $key => $value ):
	
	$image_url    = $value['result']['args'][0]['value'];
	$parsed_image = parse_url( $image_url );
	if( $current_host != $parsed_image['host'] ) continue;

	$source = \Tinify\fromUrl($image_url);

	$path_name = dirname($parsed_image['path']);
	$file_name = basename($parsed_image['path']);

	if (! file_exists($params['WEBSITE_ROOT_PATH'] . $path_name . '/') ):
	    mkdir($params['WEBSITE_ROOT_PATH'] . $path_name . '/', 0755, true);
	endif;

	if( true ==  $params['KEEP_BACKUP'] ):
		copy( $params['WEBSITE_ROOT_PATH'] . $path_name . '/' . $file_name, $params['WEBSITE_ROOT_PATH'] . $path_name . '/' . $file_name . '-BKP');
	endif;
	$source->toFile( $params['WEBSITE_ROOT_PATH'] . $path_name . '/' . $file_name );
endforeach;