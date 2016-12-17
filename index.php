<?
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	require_once('map.class.php');
	$input = json_decode(file_get_contents('php://input'), true);
	$map = new Map($input);
	$map->validateInput();
	$map->run();
?>