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
endforeach;