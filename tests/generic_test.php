<?php

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
$j = "/Users/milkoskofic/Documents/Development/Git/iso-codes/data";
$p = "/Users/milkoskofic/Documents/Development/Git/iso-codes";

//
// Instantiate class.
//
$iso = new ISOCodes( $j, $p );
//print_r( $iso->Standards() );
//print_r( $iso->Types() );
//print_r( $iso->Languages() );

//exit;

//
// Dump.
//
//$iterator = $iso->getIterator();
$iterator = $iso->getIterator( ISOCodes::k15924 );
echo( "Title: " );
var_dump( $iterator->Title() );
echo( "Description: " );
var_dump( $iterator->Description() );
echo( "Code: " );
var_dump( $iterator->DefaultCode() );
echo( "Required: " );
print_r( $iterator->Required() );
echo( "Translatable: " );
print_r( $iterator->Translated() );
echo( "Properties: " );
print_r( $iterator->Properties() );
echo( "Count: " . $iterator->count() . "\n" );
foreach( $iterator as $key => $value )
{
	echo( "Key: $key\n" );
	print_r( $value );
//	break;
}

?>

