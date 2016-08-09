<?php

/**
 * Container object test suite.
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		02/08/2016
 */

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference class.
//
use Milko\wrapper\Container;

//
// Test class.
//
class test_Container extends Container
{
	//
	// Declare test attributes.
	//
	var $attribute = NULL;
	var $flag = NULL;

	//
	// Declare constructor.
	//
	public function __construct( $props = NULL, bool $array = FALSE ) {
		parent::__construct( $props, $array );
		$this->flag = hex2bin( '00000000' );
	}

	//
	// Declare attribute accessor method.
	//
	function Attribute( $theValue = NULL, $getOld = FALSE )
	{
		return $this->manageAttribute( $this->attribute, $theValue, $getOld );
	}

	//
	// Declare bitfield attribute accessor method.
	//
	function BitfieldAttribute( string  $theMask = NULL,
								bool	$theValue = NULL,
								bool	$doOld = FALSE )
	{
		return $this->manageBitfieldAttribute(
			$this->flag, $theMask, $theValue, $doOld );
	}

	//
	// Declare property accessor method.
	//
	function Property( $theOffset, $theValue = NULL, $getOld = FALSE )
	{
		return $this->manageProperty( $theOffset, $theValue, $getOld );
	}

	//
	// Declare bitfield property accessor method.
	//
	function BitfieldProperty( $theOffset,
								string  $theMask = NULL,
								bool	$theValue = NULL,
								bool	$doOld = FALSE )
	{
		return $this->manageBitfieldProperty(
			$theOffset, $theMask, $theValue, $doOld );
	}

	//
	// Declare nested property accessor method.
	//
	function & NestedProperty( array & $theOffsets, bool $getParent = FALSE )
	{
		return $this->nestedPropertyReference( $theOffsets, $getParent );
	}
}

echo( "\n====================================================================================\n" );
echo(   "= Test nestedPropertyReference()                                                   =\n" );
echo(   "====================================================================================\n\n" );

//
// Instantiate object.
//
echo( '$test = new test_Container();' . "\n" );
$test = new test_Container([
	"nested" => new ArrayObject([
		"container" => new Container([
			"array" => [
				"string" => "a string"
			]
		])
	]),
	"one" => new test_Container([
		"two" => 3
	]),
	1 => "uno"
]);
print_r( $test );
echo( "\n" );

echo( '$offsets = [ 1 ];' . "\n" );
echo( '$result = $test->NestedProperty( $offsets ); ==> ' );
$offsets = [ 1 ];
$result = & $test->NestedProperty( $offsets );
echo( (($result === "uno") && ($result === $test[ 1 ]) && ($offsets === [])) ? "OK\n" : "ERROR!\n" );
echo( '$offsets = [ "nested", "container", "array", "string" ];' . "\n" );
echo( '$result = $test->NestedProperty( $offsets ); ==> ' );
$offsets = [ "nested", "container", "array", "string" ];
$result = & $test->NestedProperty( $offsets );
echo( (($result === "a string") && ($result === $test[ "nested" ][ "container" ][ "array" ][ "string" ]) && ($offsets === [])) ? "OK\n" : "ERROR!\n" );
echo( '$offsets = [ "nested", "container", "array", "UNKNOWN" ];' . "\n" );
echo( '$result = $test->NestedProperty( $offsets ); ==> ' );
$offsets = [ "nested", "container", "array", "UNKNOWN" ];
$result = & $test->NestedProperty( $offsets );
echo( (($result === [ "string" => "a string" ]) && ($result === $test[ "nested" ][ "container" ][ "array" ]) && ($offsets === [ "UNKNOWN" ])) ? "OK\n" : "ERROR!\n" );
echo( '$offsets = [ "nested", "container", "array", "UNKNOWN" ];' . "\n" );
echo( '$result = $test->NestedProperty( $offsets ); ==> ' );
$offsets = [ "UNKNOWN" ];
$result = & $test->NestedProperty( $offsets );
echo( (($result === $test->propertyReference()) && ($offsets === [ "UNKNOWN" ])) ? "OK\n" : "ERROR!\n" );

echo( "\n====================================================================================\n" );
echo(   "= Test ArrayAccess Interface                                                       =\n" );
echo(   "====================================================================================\n\n" );

//
// Instantiate object.
//
echo( '$test = new test_Container( [ "uno", "due", "tre" ] );' . "\n" );
$test = new test_Container( [ "uno", "due", "tre" ] );
echo( '$test[ "array" ] = [ 1, 2, 3, "obj" => new ArrayObject( [ 3, 4, 5, "obj" => new ArrayObject( [ 9, 8, 7 ] ) ] ) ];' . "\n" );
$test[ "array" ] = [ 1, 2, 3, "obj" => new ArrayObject( [ 3, 4, 5, "obj" => new test_Container( [ 9, 8, 7 ] ) ] ) ];
$test[ "nested" ] = [ "one" => new ArrayObject( [ "two" => [ "three" => 3 ] ] ) ];
print_r( $test );

echo( "\nCheck offsetExists():\n" );
echo( '$result = $test->offsetExists( 1 );         ==> ' );
$result = $test->offsetExists( 1 );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetExists( "UNKNOWN" ); ==> ' );
$result = $test->offsetExists( "UNKNOWN" );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetExists( NULL );      ==> ' );
$result = $test->offsetExists( NULL );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck nested offsetExists():\n" );
echo( '$result = $test->offsetExists( [ "array", "obj", "obj", 1 ] );     ==> ' );
$result = $test->offsetExists( [ "array", "obj", "obj", 1 ] );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetExists( [ "array", "obj", "UNKNOWN", 1 ] ); ==> ' );
$result = $test->offsetExists( [ "array", "obj", "UNKNOWN", 1 ] );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetExists( [ "array", "obj", NULL, 1 ] );      ==> ' );
$result = $test->offsetExists( [ "array", "obj", NULL, 1 ] );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck offsetGet():\n" );
echo( '$result = $test->offsetGet( 1 );         ==> ' );
$result = $test->offsetGet( 1 );
echo( ($result === "due") ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetGet( "UNKNOWN" ); ==> ' );
$result = $test->offsetGet( "UNKNOWN" );
echo( ($result === NULL) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetGet( NULL );      ==> ' );
$result = $test->offsetGet( NULL );
echo( ($result === NULL) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck nested offsetGet():\n" );
echo( '$result = $test->offsetGet( [ "array", "obj", "obj", 1 ] );     ==> ' );
$result = $test->offsetGet( [ "array", "obj", "obj", 1 ] );
echo( ($result === (int)8) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetGet( [ "array", "obj", "UNKNOWN", 1 ] ); ==> ' );
$result = $test->offsetGet( [ "array", "obj", "UNKNOWN", 1 ] );
echo( ($result === NULL) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetGet( [ "array", "obj", NULL, 1 ] );      ==> ' );
$result = $test->offsetGet( [ "array", "obj", NULL, 1 ] );
echo( ($result === NULL) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck offsetSet():\n" );
echo( '$test->offsetSet( NULL, "ADDED" );    ==> ' );
$test->offsetSet( NULL, "ADDED" );
echo( ($test[ 3 ] === "ADDED") ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetSet( 1, "CHANGED" );     ==> ' );
$test->offsetSet( 1, "CHANGED" );
echo( ($test[ 1 ] === "CHANGED") ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetSet( "UNKNOWN", "NEW" ); ==> ' );
$test->offsetSet( "UNKNOWN", "NEW" );
echo( ($test[ "UNKNOWN" ] === "NEW") ? "OK\n" : "ERROR!\n" );

echo( "\nCheck nested offsetSet():\n" );
echo( '$test->offsetSet( [ "array", "obj", NULL, "ADD" ], "ADDED NESTED" );         ==> ' );
$test->offsetSet( [ "array", "obj", NULL, "ADD" ], "ADDED NESTED" );
echo( ($test[ "array" ][ "obj" ][ 3 ][ "ADD" ] === "ADDED NESTED") ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetSet( [ "array", "obj", "obj", 1 ], "CHANGED NESTED" );          ==> ' );
$test->offsetSet( [ "array", "obj", "obj", 1 ], "CHANGED NESTED" );
echo( ($test[ "array" ][ "obj" ][ "obj" ][ 1 ] === "CHANGED NESTED") ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetSet( [ "array", "obj", "NEW OFFSET", 1 ], "NEW NESTED" );       ==> ' );
$test->offsetSet( [ "array", "obj", "NEW OFFSET", 1 ], "NEW NESTED" );
echo( ($test[ "array" ][ "obj" ][ "NEW OFFSET" ][ 1 ] === "NEW NESTED") ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetSet( [ "array", "obj", "obj", 1, "nested" ], "OTHER NESTED" );  ==> ' );
$test->offsetSet( [ "array", "obj", "NEW OFFSET", 1, "nested" ], "OTHER NESTED" );
echo( ($test[ "array" ][ "obj" ][ "NEW OFFSET" ][ 1 ][ "nested" ] === "OTHER NESTED") ? "OK\n" : "ERROR!\n" );

echo( "\nCheck offsetUnset():\n" );
echo( '$test->offsetUnset( 1 );         ==> ' );
$test->offsetUnset( 1 );
echo( (! $test->offsetExists( 1 )) ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetUnset( "UNKNOWN" ); ==> ' );
$test->offsetUnset( "UNKNOWN" );
echo( (! $test->offsetExists( "UNKNOWN" )) ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetUnset( NULL );      ==> ' );
$test->offsetUnset( NULL );
echo( (! $test->offsetExists( NULL )) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck nested offsetUnset():\n" );
echo( '$test->offsetUnset( [ "array", "obj", "obj", "UNKNOWN" ] ); ==> Should not raise an alert' . "\n" );
$test->offsetUnset( [ "array", "obj", "obj", "UNKNOWN" ] );
echo( '$test->offsetUnset( [ "array", "obj", "obj", 1 ] );         ==> ' );
$test->offsetUnset( [ "array", "obj", "obj", 1 ] );
echo( (! $test->offsetExists( [ "array", "obj", "obj", 1 ] )) ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetUnset( [ "array", "obj", "NEW OFFSET", 1 ] );  ==> ' );
$test->offsetUnset( [ "array", "obj", "NEW OFFSET", 1 ] );
$ok = ! ( $test->offsetExists( [ "array", "obj", "NEW OFFSET", 1 ] ) && $test->offsetExists( [ "array", "obj", "NEW OFFSET" ] ) );
echo( ($ok) ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetUnset( [ "array", "obj", NULL, 1 ] );          ==> ' );
$test->offsetUnset( [ "array", "obj", NULL, 1 ] );
echo( (! $test->offsetExists( [ "array", "obj", NULL, 1 ] )) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck getIterator()():\n" );
echo( '$result = $test->getIterator(); ==> ' );
$result = $test->getIterator();
$i = 0;
$ok = TRUE;
$msg = "";
foreach( $result as $key => $value )
{
	switch( $i++ )
	{
		case 0:
			if( $key != 0 )
			{
				$ok = FALSE;
				$msg = "Key should be 0 [$key]";
			}
			elseif( $value != "uno" )
			{
				$ok = FALSE;
				$msg = "Value should be 'uno' [$value]";
			}
			break;
		case 1:
			if( $key != 2 )
			{
				$ok = FALSE;
				$msg = "Key should be 2 [$key]";
			}
			elseif( $value != "tre" )
			{
				$ok = FALSE;
				$msg = "Value should be 'tre' [$value]";
			}
			break;
		case 2:
			if( $key != "array" )
			{
				$ok = FALSE;
				$msg = "Key should be 'array' [$key]";
			}
			elseif( ! is_array( $value ) )
			{
				$ok = FALSE;
				$msg = "Value should be array [" . gettype( $value ) . "]";
			}
			break;
		case 3:
			if( $key != "nested" )
			{
				$ok = FALSE;
				$msg = "Key should be 'nested' [$key]";
			}
			elseif( ! is_array( $value ) )
			{
				$ok = FALSE;
				$msg = "Value should be array [" . gettype( $value ) . "]";
			}
			break;
		case 4:
			if( $key != 3 )
			{
				$ok = FALSE;
				$msg = "Key should be 3 [$key]";
			}
			elseif( $value != "ADDED" )
			{
				$ok = FALSE;
				$msg = "Value should be 'ADDED' [$value]";
			}
			break;
		default:
			$ok = FALSE;
			$msg = "Too many elements";
			break;
	}
}
echo( ($ok) ? "OK\n" : "ERROR! $msg\n" );

echo( "\n====================================================================================\n" );
echo(   "= Test Countable Interface                                                         =\n" );
echo(   "====================================================================================\n\n" );

echo( "Check count():\n" );
echo( '$result = $test->count(); ==> ' );
$result = $test->count();
echo( ($result == 5) ? "OK\n" : "ERROR!\n" );

echo( "\n====================================================================================\n" );
echo(   "= Test custom array Interface                                                      =\n" );
echo(   "====================================================================================\n\n" );

echo( "Check getArrayCopy():\n" );
echo( '$result = $test->getArrayCopy(); ==> ' );
$result = $test->getArrayCopy();
echo( ($result == [
		0 => "uno",
		2 => "tre",
		"array" => [
			0 => 1,
			1 => 2,
			2 => 3,
			"obj" => new ArrayObject([
				0 => 3,
				1 => 4,
				2 => 5,
				"obj" => new test_Container([
					0 => 9,
					2 => 7
				]),
				3 => [ "ADD" => "ADDED NESTED" ]
			])
		],
		"nested" => [
			"one" => new ArrayObject([
				"two" => [ "three" => 3]
			])
		],
		3 => "ADDED"
	]) ? "OK\n" : "ERROR!\n" );

echo( "\n" );

echo( "Check propertyReference():\n" );
echo( '$result1 = & $test->propertyReference();' . "\n" );
$result1 = & $test->propertyReference();
echo( '$result2 = & $test->propertyReference( NULL );' . "\n" );
$result2 = & $test->propertyReference( NULL );
echo( '$result3 = & $test->propertyReference( [] );                                         ==> ' );
$result3 = & $test->propertyReference( [] );
echo( (($result1 === $result2) && ($result1 === $result3)) ? "OK\n" : "ERROR!\n" );
unset($result1); unset($result2); unset($result3);
echo( '$result = & $test->propertyReference( 0 ); $result = 3;                              ==> ' );
$result = & $test->propertyReference( 0 ); $result = 3;
echo( ($test[ 0 ] == 3) ? "OK\n" : "ERROR!\n" );
//unset($result);
echo( '$result = & $test->propertyReference( [ "array", "obj", "obj", 0 ] ); $result = "X"; ==> ' );
$result = & $test->propertyReference( [ "array", "obj", "obj", 0 ] ); $result = "X";
echo( ($test[ "array" ][ "obj" ][ "obj" ][ 0 ] == "X") ? "OK\n" : "ERROR!\n" );
unset($result);

echo( "\n" );

echo( "Check asArray() and toArray():\n" );
echo( '$result = $test->asArray(); ==> ' );
$result = $test->asArray();
echo( ($result == [
		0 => 3,
		2 => "tre",
		"array" => [
			0 => 1,
			1 => 2,
			2 => 3,
			"obj" => [
				0 => 3,
				1 => 4,
				2 => 5,
				"obj" => [
					0 => "X",
					2 => 7
				],
				3 => [ "ADD" => "ADDED NESTED" ]
			]
		],
		"nested" => [
			"one" => [
				"two" => [ "three" => 3]
			]
		],
		3 => "ADDED"
	]) ? "OK\n" : "ERROR!\n" );
echo( '$test->toArray();           ==> ' );
$test->toArray();
echo( ($test->getArrayCopy() == [
		0 => 3,
		2 => "tre",
		"array" => [
			0 => 1,
			1 => 2,
			2 => 3,
			"obj" => [
				0 => 3,
				1 => 4,
				2 => 5,
				"obj" => [
					0 => "X",
					2 => 7
				],
				3 => [ "ADD" => "ADDED NESTED" ]
			]
		],
		"nested" => [
			"one" => [
				"two" => [ "three" => 3]
			]
		],
		3 => "ADDED"
	]) ? "OK\n" : "ERROR!\n" );

echo( "\n====================================================================================\n" );
echo(   "= Test Static Interface                                                            =\n" );
echo(   "====================================================================================\n\n" );

//
// Instantiate object.
//
$object = new Container([
	"ArrayObject" => new ArrayObject([
		"array" => [
			"container" => new Container([
				"string" => "a string"
			])
		]
	])
]);
print_r( $object );

echo( "\nCheck ConvertToArray():\n" );
echo( 'Container::ConvertToArray( $struct ); ==> ' );
Container::ConvertToArray( $object );
echo( ($object == [
		"ArrayObject" => [
			"array" => [
				"container" => [
					"string" => "a string"
				]
			]
		]
	]) ? "OK\n" : "ERROR!\n" );

echo( "\n====================================================================================\n" );
echo(   "= Test Manage Attribute                                                            =\n" );
echo(   "====================================================================================\n\n" );

//
// Instantiate object.
//
echo( '$test = new test_Container();' . "\n" );
$test = new test_Container();
print_r( $test );

echo( "\nCheck manageAttribute():\n" );
echo( '$result = $test->Attribute( "NEW" );         ==> ' );
$result = $test->Attribute( "NEW" );
echo( (($test->attribute === "NEW") && ($result == "NEW")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Attribute( "OTHER", TRUE ); ==> ' );
$result = $test->Attribute( "OTHER", TRUE );
echo( (($test->attribute === "OTHER") && ($result == "NEW")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Attribute();                ==> ' );
$result = $test->Attribute();
echo( ($result == "OTHER") ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Attribute( FALSE, TRUE );   ==> ' );
$result = $test->Attribute( FALSE, TRUE );
echo( (($test->attribute === NULL) && ($result == "OTHER")) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck manageFlagAttribute():\n" );
echo( '$result = $test->BitfieldAttribute();                                    ==> ' );
$result = $test->BitfieldAttribute();
echo( ((bin2hex($result) == "00000000") && ($result === $test->flag)) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE );        ==> ' );
$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE );
echo( ((bin2hex($result) == "ff000000") && ($result === $test->flag)) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldAttribute( hex2bin("ff0f" ), TRUE, TRUE );      ==> ' );
$result = $test->BitfieldAttribute( hex2bin("ff0f" ), TRUE, TRUE );
echo( ((bin2hex($result) == "ff000000") && (bin2hex($test->flag) == "ff0f0000")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldAttribute( hex2bin("f0000000" ) );              ==> ' );
$result = $test->BitfieldAttribute( hex2bin("f0000000" ) );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldAttribute( hex2bin("0f" ) );                    ==> ' );
$result = $test->BitfieldAttribute( hex2bin("0f" ) );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldAttribute( hex2bin("000000ff" ) );              ==> ' );
$result = $test->BitfieldAttribute( hex2bin("000000ff" ) );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldAttribute( hex2bin("00f0" ) );                  ==> ' );
$result = $test->BitfieldAttribute( hex2bin("00f0" ) );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldAttribute( hex2bin("f0f00000" ), FALSE, TRUE ); ==> ' );
$result = $test->BitfieldAttribute( hex2bin("f0f00000" ), FALSE, TRUE );
echo( ((bin2hex($result) == "ff0f0000") && (bin2hex($test->flag) == "0f0f0000")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldAttribute( hex2bin("0ff0" ), FALSE, TRUE );     ==> ' );
$result = $test->BitfieldAttribute( hex2bin("0ff0" ), FALSE, TRUE );
echo( ((bin2hex($result) == "0f0f0000") && (bin2hex($test->flag) == "000f")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE, TRUE );  ==> ' );
$result = $test->BitfieldAttribute( hex2bin("ff000000" ), TRUE, TRUE );
echo( ((bin2hex($result) == "000f") && (bin2hex($test->flag) == "ff0f0000")) ? "OK\n" : "ERROR!\n" );

echo( "\n====================================================================================\n" );
echo(   "= Test Manage Property                                                             =\n" );
echo(   "====================================================================================\n\n" );

//
// Instantiate object.
//
echo( '$test = new test_Container();' . "\n" );
$test = new test_Container([
	"nested" => new ArrayObject([
		"container" => new Container([
			"array" => [
				"string" => "a string"
			]
		])
	]),
	"one" => new test_Container([
		"two" => 3
	])
]);
print_r( $test );

echo( "\nCheck manageProperty():\n" );
echo( '$result = $test->Property( "prop", "NEW" );         ==> ' );
$result = $test->Property( "prop", "NEW" );
echo( (($test[ "prop" ] === "NEW") && ($result == "NEW")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Property( "prop", "OTHER", TRUE ); ==> ' );
$result = $test->Property( "prop", "OTHER", TRUE );
echo( (($test[ "prop" ] === "OTHER") && ($result == "NEW")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Property( "prop" );                ==> ' );
$result = $test->Property( "prop" );
echo( ($result == "OTHER") ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Property( "prop", FALSE, TRUE );   ==> ' );
$result = $test->Property( "prop", FALSE, TRUE );
echo( (($test->offsetGet( "prop" ) === NULL) && ($result == "OTHER")) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck nested manageProperty():\n" );
echo( '$result = $test->Property( [ "nested", "container", "array", "string" ] );                       ==> ' );
$result = $test->Property( [ "nested", "container", "array", "string" ] );
echo( ($result == "a string") ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Property( [ "nested", "container", "UNKNOWN", "string" ] );                     ==> ' );
$result = $test->Property( [ "nested", "container", "UNKNOWN", "string" ] );
echo( ($result === NULL) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Property( [ "nested", "container", "array", "string" ], "NEW STRING" );         ==> ' );
$result = $test->Property( [ "nested", "container", "array", "string" ], "NEW STRING" );
echo( ($result == "NEW STRING") ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Property( [ "nested", "container", "array", "string" ], "OTHER STRING", TRUE ); ==> ' );
$result = $test->Property( [ "nested", "container", "array", "string" ], "OTHER STRING", TRUE );
echo( (($result == "NEW STRING") && ($test[ "nested" ][ "container" ][ "array" ][ "string" ] == "OTHER STRING")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Property( [ "branch", "array", "array", "string" ], "INSERTED STRING", TRUE );  ==> ' );
$result = $test->Property( [ "branch", "array", "array", "string" ], "INSERTED STRING", TRUE );
echo( (($result === NULL) && ($test[ "branch" ][ "array" ][ "array" ][ "string" ] == "INSERTED STRING")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->Property( [ "nested", "container", "array", "string" ], FALSE, TRUE );          ==> ' );
$result = $test->Property( [ "nested", "container", "array", "string" ], FALSE, TRUE );
echo( (($result == "OTHER STRING") && ($test->offsetExists("nested") === FALSE)) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck manageFlagProperty():\n" );
echo( '$test[ "flag" ] = hex2bin( "00000000" );' . "\n" );
$test[ "flag" ] = hex2bin( "00000000" );
echo( '$result = $test->BitfieldProperty( "flag" );                                    ==> ' );
$result = $test->BitfieldProperty( "flag" );
echo( ((bin2hex($result) == "00000000") && ($result === $test[ "flag" ])) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE );        ==> ' );
$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE );
echo( ((bin2hex($result) == "ff000000") && ($result === $test[ "flag" ])) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty( "flag", hex2bin("ff0f" ), TRUE, TRUE );      ==> ' );
$result = $test->BitfieldProperty( "flag", hex2bin("ff0f" ), TRUE, TRUE );
echo( ((bin2hex($result) == "ff000000") && (bin2hex($test[ "flag" ]) == "ff0f0000")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty( "flag", hex2bin("f0000000" ) );              ==> ' );
$result = $test->BitfieldProperty( "flag", hex2bin("f0000000" ) );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty( "flag", hex2bin("0f" ) );                    ==> ' );
$result = $test->BitfieldProperty( "flag", hex2bin("0f" ) );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty( "flag", hex2bin("000000ff" ) );              ==> ' );
$result = $test->BitfieldProperty( "flag", hex2bin("000000ff" ) );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty( "flag", hex2bin("00f0" ) );                  ==> ' );
$result = $test->BitfieldProperty( "flag", hex2bin("00f0" ) );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty( "flag", hex2bin("f0f00000" ), FALSE, TRUE ); ==> ' );
$result = $test->BitfieldProperty( "flag", hex2bin("f0f00000" ), FALSE, TRUE );
echo( ((bin2hex($result) == "ff0f0000") && (bin2hex($test[ "flag" ]) == "0f0f0000")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty( "flag", hex2bin("0ff0" ), FALSE, TRUE );     ==> ' );
$result = $test->BitfieldProperty( "flag", hex2bin("0ff0" ), FALSE, TRUE );
echo( ((bin2hex($result) == "0f0f0000") && (bin2hex($test[ "flag" ]) == "000f")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE, TRUE );  ==> ' );
$result = $test->BitfieldProperty( "flag", hex2bin("ff000000" ), TRUE, TRUE );
echo( ((bin2hex($result) == "000f") && (bin2hex($test[ "flag" ]) == "ff0f0000")) ? "OK\n" : "ERROR!\n" );

echo( "\nCheck nested manageFlagProperty():\n" );
echo( '$test[  [ "nested", "switch", "flag" ]  ] = hex2bin( "00000000" );' . "\n" );
$test[  [ "nested", "switch", "flag" ]  ] = hex2bin( "00000000" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ]  ); ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ]  );
echo( ((bin2hex($result) == "00000000") && ($result === $test[  [ "nested", "switch", "flag" ]  ])) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("ff000000" ), TRUE );        ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("ff000000" ), TRUE );
echo( ((bin2hex($result) == "ff000000") && ($result === $test[  [ "nested", "switch", "flag" ]  ])) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("ff0f" ), TRUE, TRUE );      ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("ff0f" ), TRUE, TRUE );
echo( ((bin2hex($result) == "ff000000") && (bin2hex($test[  [ "nested", "switch", "flag" ]  ]) == "ff0f0000")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("f0000000" ) );              ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("f0000000" ) );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("0f" ) );                    ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("0f" ) );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("000000ff" ) );              ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("000000ff" ) );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("00f0" ) );                  ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("00f0" ) );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("f0f00000" ), FALSE, TRUE ); ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("f0f00000" ), FALSE, TRUE );
echo( ((bin2hex($result) == "ff0f0000") && (bin2hex($test[  [ "nested", "switch", "flag" ]  ]) == "0f0f0000")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("0ff0" ), FALSE, TRUE );     ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("0ff0" ), FALSE, TRUE );
echo( ((bin2hex($result) == "0f0f0000") && (bin2hex($test[  [ "nested", "switch", "flag" ]  ]) == "000f")) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("ff000000" ), TRUE, TRUE );  ==> ' );
$result = $test->BitfieldProperty(  [ "nested", "switch", "flag" ] , hex2bin("ff000000" ), TRUE, TRUE );
echo( ((bin2hex($result) == "000f") && (bin2hex($test[  [ "nested", "switch", "flag" ]  ]) == "ff0f0000")) ? "OK\n" : "ERROR!\n" );


?>

