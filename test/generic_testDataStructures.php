<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference class.
//
use Milko\wrapper\Container;

//
// Instantiate container.
//
echo( "Instantiate container:\n" );
echo( '$container = new Container();' . "\n\n" );
$container = new Container();
echo( '$container[ "id" ] = [ "ns" => "namespace", "gid" => "ns:id", "lid" => "id" ];' . "\n\n" );
$container[ "id" ] = [ "ns" => "namespace", "gid" => "ns:id", "lid" => "id" ];
print_r( $container );

echo( "\n====================================================================================\n\n" );

//
// Instantiate map.
//
echo( "Instantiate map:\n" );
echo( '$map = new Ds\Map( $container );' . "\n" );
$map = new Ds\Map( $container );
print_r( $map );
var_dump( $map->capacity() );


?>

