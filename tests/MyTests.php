<?php
/**
 Created by PhpStorm.
 User: milko
 Date: 19/10/2017
 Time: 13:35
 */

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference classes.
//
use Milko\utils\ISOCodes;

//
// Set directories.
//
$j = "/Users/milko/Local/Git/Resources/ISO-Codes/data";
$p = "/Users/milko/Local/Git/Resources/ISO-Codes";

//
// Instantiate class.
//
$iso = new ISOCodes( $j, $p );

//
// Show summary.
//
//echo( "Standards: " );
//print_r( $iso->Standards() );

//echo( "Types: " );
//print_r( $iso->Types() );

//echo( "Languages: " );
//print_r( $iso->Languages() );

//echo( "Locales: " );
//print_r( $iso->Locales() );

//exit;

//
// Dump schemas.
//
//$iterator = $iso->getIterator();
//foreach( $iterator as $standard => $schema )
//{
//	var_dump( $standard );
//	print_r( $schema );
//}
//exit;

//
// Dump data.
//
//$iterator = $iso->getIterator( ISOCodes::k3166_1 );
//echo( "Title: " );
//var_dump( $iterator->Title() );
//echo( "\nDescription: " );
//var_dump( $iterator->Description() );
//echo( "\nCode: " );
//var_dump( $iterator->DefaultCode() );
//echo( "\nRequired: " );
//print_r( $iterator->Required() );
//echo( "\nTranslatable: " );
//print_r( $iterator->Translated() );
//echo( "\nProperties: " );
//print_r( $iterator->Properties() );
//echo( "\nCount: " . $iterator->count() . "\n" );
//echo( "\nCodes: " );
//foreach( $iterator as $key => $value )
//{
//	echo( "\nKey: $key\n" );
//	echo( "Data: " );
//	print_r( $value );
//}

?>

