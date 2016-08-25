<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

$file = "/Users/milkoskofic/Documents/Development/Git/iso-codes/iso_3166-1/it.po";
$data = file_get_contents( $file );
//$count = preg_match_all( '/^msgid\s+("(.+)"$)+/', $data, $match );
$count = preg_match_all( '/^(msgid)+/', $data, $match );
print_r( $match );
exit;
$count = preg_match_all( '/msgstr ("(.+)")+/', $data, $match );
print_r( $match );
exit;

//
// Reference classes.
//
//use Milko\utils\ISOCodes;

//
// Set directories.
//
$j = "/Users/milkoskofic/Documents/Development/Git/iso-codes/data";
$p = "/Users/milkoskofic/Documents/Development/Git/iso-codes";
$d = "/Users/milkoskofic/Documents/Development/Data/iso-codes";

//
// Instantiate class.
//
$iso = new ISOCodes( $j, $p );
//print_r( $iso->Standards() );
//print_r( $iso->Schema() );
//print_r( $iso->Types() );
//print_r( $iso->Languages() );

//
// Dump.
//
$iso->Dump( $d );

?>