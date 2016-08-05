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
	// Declare test attribute.
	//
	var $attribute;

	//
	// Declare attribute accessor method.
	//
	function Attribute( $theValue = NULL, $getOld = FALSE )
	{
		return $this->manageAttribute( $this->attribute, $theValue, $getOld );
	}

	//
	// Declare property accessor method.
	//
	function Property( $theOffset, $theValue = NULL, $getOld = FALSE )
	{
		return $this->manageProperty( $theOffset, $theValue, $getOld );
	}

	//
	// Declare indexed property accessor method.
	//
	function IndexedProperty( $theOffset, $theKey = NULL, $theValue = NULL, $getOld = FALSE )
	{
		return $this->manageIndexedProperty( $theOffset, $theKey, $theValue, $getOld );
	}

	//
	// Declare property reference accessor method.
	//
	function & ReferenceProperty( $theOffset = NULL )
	{
		return $this->nestedGet( $theOffset );
	}

	//
	// Declare nested property setter accessor method.
	//
	function NestedPropertySet( $theOffset, $theValue )
	{
		return $this->nestedSet( $theOffset, $theValue );
	}

	//
	// Declare nested property setter accessor method.
	//
	function & GetNestedPropertyReference( array & $theOffset, bool $getParent = FALSE )
	{
		return $this->nestedPropertyReference( $theOffset, $getParent );
	}
}

//
// Instantiate object.
//
echo( '$test = new test_Container( [ "uno", "due", "tre" ] );' . "\n" );
$test = new test_Container( [ "uno", "due", "tre" ] );
echo( '$test[ "array" ] = [ 1, 2, 3, "obj" => new ArrayObject( [ 3, 4, 5, "obj" => new ArrayObject( [ 9, 8, 7 ] ) ] ) ];' . "\n" );
$test = new test_Container( [ "uno", "due", "tre" ] );
$test[ "array" ] = [ 1, 2, 3, "obj" => new ArrayObject( [ 3, 4, 5, "obj" => new ArrayObject( [ 9, 8, 7 ] ) ] ) ];
$test[ "nested" ] = [ "one" => new ArrayObject( [ "two" => [ "three" => 3 ] ] ) ];
print_r( $test );

echo( "\n====================================================================================\n" );
echo(   "= Test ArrayAccess Interface                                                       =\n" );
echo(   "====================================================================================\n\n" );

echo( "Check offsetExists():\n" );
echo( '$result = $test->offsetExists( 1 );         ==> ' );
$result = $test->offsetExists( 1 );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetExists( "UNKNOWN" ); ==> ' );
$result = $test->offsetExists( "UNKNOWN" );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetExists( NULL );      ==> ' );
$result = $test->offsetExists( NULL );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );

echo( "\n" );

echo( "Check nested offsetExists():\n" );
echo( '$result = $test->offsetExists( [ "array", "obj", "obj", 1 ] );     ==> ' );
$result = $test->offsetExists( [ "array", "obj", "obj", 1 ] );
echo( ($result === TRUE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetExists( [ "array", "obj", "UNKNOWN", 1 ] ); ==> ' );
$result = $test->offsetExists( [ "array", "obj", "UNKNOWN", 1 ] );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetExists( [ "array", "obj", NULL, 1 ] );      ==> ' );
$result = $test->offsetExists( [ "array", "obj", NULL, 1 ] );
echo( ($result === FALSE) ? "OK\n" : "ERROR!\n" );

echo( "\n" );

echo( "Check offsetGet():\n" );
echo( '$result = $test->offsetGet( 1 );         ==> ' );
$result = $test->offsetGet( 1 );
echo( ($result === "due") ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetGet( "UNKNOWN" ); ==> ' );
$result = $test->offsetGet( "UNKNOWN" );
echo( ($result === NULL) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetGet( NULL );      ==> ' );
$result = $test->offsetGet( NULL );
echo( ($result === NULL) ? "OK\n" : "ERROR!\n" );

echo( "\n" );

echo( "Check nested offsetGet():\n" );
echo( '$result = $test->offsetGet( [ "array", "obj", "obj", 1 ] );     ==> ' );
$result = $test->offsetGet( [ "array", "obj", "obj", 1 ] );
echo( ($result === (int)8) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetGet( [ "array", "obj", "UNKNOWN", 1 ] ); ==> ' );
$result = $test->offsetGet( [ "array", "obj", "UNKNOWN", 1 ] );
echo( ($result === NULL) ? "OK\n" : "ERROR!\n" );
echo( '$result = $test->offsetGet( [ "array", "obj", NULL, 1 ] );      ==> ' );
$result = $test->offsetGet( [ "array", "obj", NULL, 1 ] );
echo( ($result === NULL) ? "OK\n" : "ERROR!\n" );

echo( "\n" );

echo( "Check offsetSet():\n" );
echo( '$test->offsetSet( NULL, "ADDED" );    ==> ' );
$test->offsetSet( NULL, "ADDED" );
echo( ($test[ 3 ] === "ADDED") ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetSet( 1, "CHANGED" );     ==> ' );
$test->offsetSet( 1, "CHANGED" );
echo( ($test[ 1 ] === "CHANGED") ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetSet( "UNKNOWN", "NEW" ); ==> ' );
$test->offsetSet( "UNKNOWN", "NEW" );
echo( ($test[ "UNKNOWN" ] === "NEW") ? "OK\n" : "ERROR!\n" );

echo( "\n" );

echo( "Check nested offsetSet():\n" );
echo( '$test->offsetSet( [ "array", "obj", NULL, "ADD" ], "ADDED NESTED" ); ==> ' );
$test->offsetSet( [ "array", "obj", NULL, "ADD" ], "ADDED NESTED" );
echo( ($test[ "array" ][ "obj" ][ 4 ][ "ADD" ] === "ADDED NESTED") ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetSet( [ "array", "obj", "obj", 1 ], "CHANGED NESTED" );  ==> ' );
$test->offsetSet( [ "array", "obj", "obj", 1 ], "CHANGED NESTED" );
echo( ($test[ "array" ][ "obj" ][ "obj" ][ 1 ] === "CHANGED NESTED") ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetSet( [ "array", "obj", "NEW OFFSET", 1 ], "NEW NESTED" );  ==> ' );
$test->offsetSet( [ "array", "obj", "NEW OFFSET", 1 ], "NEW NESTED" );
echo( ($test[ "array" ][ "obj" ][ "NEW OFFSET" ][ 1 ] === "NEW NESTED") ? "OK\n" : "ERROR!\n" );

echo( "\n" );

echo( "Check offsetUnset():\n" );
echo( '$test->offsetUnset( 1 );         ==> ' );
$test->offsetUnset( 1 );
echo( (! $test->offsetExists( 1 )) ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetUnset( "UNKNOWN" ); ==> ' );
$test->offsetUnset( "UNKNOWN" );
echo( (! $test->offsetExists( "UNKNOWN" )) ? "OK\n" : "ERROR!\n" );
echo( '$test->offsetUnset( NULL );      ==> ' );
$test->offsetUnset( NULL );
echo( (! $test->offsetExists( NULL )) ? "OK\n" : "ERROR!\n" );

echo( "\n" );

echo( "Check nested offsetUnset():\n" );
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
exit;

echo( "\n====================================================================================\n\n" );

//
// Manage attributes.
//
echo( "Retrieve non existing attribute:\n" );
echo( '$result = $test->Attribute();' . "\n" );
$result = $test->Attribute();
var_dump( $result );

echo( "\n" );

echo( "Set new attribute:\n" );
echo( '$result = $test->Attribute( "pippo" );' . "\n" );
$result = $test->Attribute( "pippo" );
var_dump( $result );
print_r( $test );

echo( "\n" );

echo( "Set new attribute and return old value:\n" );
echo( '$result = $test->Attribute( "pappa", TRUE );' . "\n" );
$result = $test->Attribute( "pappa", TRUE );
var_dump( $result );
print_r( $test );

echo( "\n" );

echo( "Retrieve attribute:\n" );
echo( '$result = $test->Attribute();' . "\n" );
$result = $test->Attribute();
var_dump( $result );

echo( "\n" );

echo( "Reset attribute:\n" );
echo( '$result = $test->Attribute( FALSE, TRUE );' . "\n" );
$result = $test->Attribute( FALSE, TRUE );
var_dump( $result );
print_r( $test );

echo( "\n====================================================================================\n\n" );

//
// Manage property.
//
echo( "Retrieve non existing property:\n" );
echo( '$result = $test->Property( "key" );' . "\n" );
$result = $test->Property( "key" );
var_dump( $result );

echo( "\n" );

echo( "Set new property:\n" );
echo( '$result = $test->Property( "key", "value" );' . "\n" );
$result = $test->Property( "key", "value" );
var_dump( $result );
print_r( $test );

echo( "\n" );

echo( "Set new property and return old value:\n" );
echo( '$result = $test->Property( "key", "new", TRUE );' . "\n" );
$result = $test->Property( "key", "new", TRUE );
var_dump( $result );
print_r( $test );

echo( "\n" );

echo( "Retrieve property:\n" );
echo( '$result = $test->Property( "key" );' . "\n" );
$result = $test->Property( "key" );
var_dump( $result );

echo( "\n" );

echo( "Reset property:\n" );
echo( '$result = $test->Property( "key", FALSE, TRUE );' . "\n" );
$result = $test->Property( "key", FALSE, TRUE );
var_dump( $result );
print_r( $test );

echo( "\n" );

echo( "Append property:\n" );
echo( '$test[] = "bubu";' . "\n" );
$test[] = "bubu";
print_r( $test );

echo( "\n" );

echo( "Get keys:\n" );
echo( '$result = $test->array_keys();' . "\n" );
$result = $test->array_keys();
print_r( $result );

echo( "\n" );

echo( "Get values:\n" );
echo( '$result = $test->array_values();' . "\n" );
$result = $test->array_values();
print_r( $result );

echo( "\n====================================================================================\n\n" );

//
// Manage indexed property.
//
echo( "Retrieve non existing indexed property:\n" );
echo( '$result = $test->IndexedProperty( "offset" );' . "\n" );
$result = $test->IndexedProperty( "offset" );
var_dump( $result );

echo( "\n" );

echo( "Set new indexed property:\n" );
echo( '$result = $test->IndexedProperty( "offset", "key", "value" );' . "\n" );
$result = $test->IndexedProperty( "offset", "key", "value" );
var_dump( $result );
print_r( $test );

echo( "\n" );

echo( "Set new indexed property and return old value:\n" );
echo( '$result = $test->IndexedProperty( "offset", "key", "new", TRUE );' . "\n" );
$result = $test->IndexedProperty( "offset", "key", "new", TRUE );
var_dump( $result );
print_r( $test );

echo( "\n" );

echo( "Retrieve indexed property:\n" );
echo( '$result = $test->IndexedProperty( "offset", "key" );' . "\n" );
$result = $test->IndexedProperty( "offset", "key" );
var_dump( $result );

echo( "\n" );

echo( "Retrieve indexed properties:\n" );
echo( '$result = $test->IndexedProperty( "offset" );' . "\n" );
$result = $test->IndexedProperty( "offset" );
print_r( $result );

echo( "\n" );

echo( "Reset indexed property:\n" );
echo( '$result = $test->IndexedProperty( "offset", "key", FALSE, TRUE );' . "\n" );
$result = $test->IndexedProperty( "offset", "key", FALSE, TRUE );
var_dump( $result );
print_r( $test );

echo( "\n====================================================================================\n\n" );

//
// Test nestedPropertyReference().
//
echo( "Test nestedPropertyReference(): build test object.\n" );
echo( '$test = new test_Container();' . "\n" );
$test = new test_Container();
echo( '$test[ "array" ] = [ 1, 2, 3 ];' . "\n" );
$test[ "array" ] = [ 1, 2, 3, "obj" => new ArrayObject( [ 3, 4, 5, "obj" => new ArrayObject( [ 9, 8, 7 ] ) ] ) ];
print_r( $test );

echo( "\n" );

echo( "Check empty offsets list:\n" );
echo( '$list = [];' . "\n" );
$list = [];
echo( '$result = $test->GetNestedPropertyReference( $list );' . "\n" );
$result = $test->GetNestedPropertyReference( $list );
echo( "Reference: " );
print_r( $result );
echo( "List: " );
var_dump( $list );

echo( "\n" );

echo( "Check all offsets match:\n" );
echo( '$list = [ "array", "obj", "obj", 0 ];' . "\n" );
$list = [ "array", "obj", "obj", 0 ];
echo( '$result = $test->GetNestedPropertyReference( $list );' . "\n" );
$result = $test->GetNestedPropertyReference( $list );
echo( "Reference: " );
var_dump( $result );
echo( "List: " );
print_r( $list );

echo( "\n" );

echo( "Check one offset unmatch:\n" );
echo( '$list = [ "array", "obj", "UNKNOWN", 0 ];' . "\n" );
$list = [ "array", "obj", "UNKNOWN", 0 ];
echo( '$result = $test->GetNestedPropertyReference( $list );' . "\n" );
$result = $test->GetNestedPropertyReference( $list );
echo( "Reference: " );
print_r( $result );
echo( "List: " );
print_r( $list );

echo( "\n" );

echo( "Check all offsets unmatch:\n" );
echo( '$list = [ "UNKNOWN", "obj", "obj", 0 ];' . "\n" );
$list = [ "UNKNOWN", "obj", "obj", 0 ];
echo( '$result = $test->GetNestedPropertyReference( $list );' . "\n" );
$result = $test->GetNestedPropertyReference( $list );
echo( "Reference: " );
print_r( $result );
echo( "List: " );
print_r( $list );

echo( "\n====================================================================================\n\n" );

//
// Test nestedPropertyReference() with parent flag.
//
echo( "Test nestedPropertyReference() with parent flag ON: build test object.\n" );
echo( '$test = new test_Container();' . "\n" );
$test = new test_Container();
echo( '$test[ "array" ] = [ 1, 2, 3 ];' . "\n" );
$test[ "array" ] = [ 1, 2, 3, "obj" => new ArrayObject( [ 3, 4, 5, "obj" => new ArrayObject( [ 9, 8, 7 ] ) ] ) ];
print_r( $test );

echo( "\n" );

echo( "Check empty offsets list:\n" );
echo( '$list = [];' . "\n" );
$list = [];
echo( '$result = $test->GetNestedPropertyReference( $list, TRUE );' . "\n" );
$result = $test->GetNestedPropertyReference( $list, TRUE );
echo( "Reference: " );
print_r( $result );
echo( "List: " );
var_dump( $list );

echo( "\n" );

echo( "Check all offsets match:\n" );
echo( '$list = [ "array", "obj", "obj", 0 ];' . "\n" );
$list = [ "array", "obj", "obj", 0 ];
echo( '$result = $test->GetNestedPropertyReference( $list, TRUE );' . "\n" );
$result = $test->GetNestedPropertyReference( $list, TRUE );
echo( "Reference: " );
print_r( $result );
echo( "List: " );
print_r( $list );
echo( "Value: " );
echo( '$result[ $list[ 0 ] ];' . "\n" );
var_dump( $result[ $list[ 0 ] ] );

echo( "\n" );

echo( "Check one offset unmatch:\n" );
echo( '$list = [ "array", "obj", "UNKNOWN", 0 ];' . "\n" );
$list = [ "array", "obj", "UNKNOWN", 0 ];
echo( '$result = $test->GetNestedPropertyReference( $list, TRUE );' . "\n" );
$result = $test->GetNestedPropertyReference( $list, TRUE );
echo( "Reference: " );
print_r( $result );
echo( "List: " );
print_r( $list );

echo( "\n" );

echo( "Check all offsets unmatch:\n" );
echo( '$list = [ "UNKNOWN", "obj", "obj", 0 ];' . "\n" );
$list = [ "UNKNOWN", "obj", "obj", 0 ];
echo( '$result = $test->GetNestedPropertyReference( $list, TRUE );' . "\n" );
$result = $test->GetNestedPropertyReference( $list, TRUE );
echo( "Reference: " );
print_r( $result );
echo( "List: " );
print_r( $list );

echo( "\n====================================================================================\n\n" );

//
// Test conversion.
//
echo( "Build nested object:\n" );
echo( '$test = new test_Container();' . "\n" );
$test = new test_Container();
echo( '$test[ "array" ] = [ 1, 2, 3 ];' . "\n" );
$test[ "array" ] = [ 1, 2, 3, "obj" => new ArrayObject( [ 1, 2, 3, "obj" => new ArrayObject( [9,8,7] ) ] ) ];
print_r( $test );

echo( "\n" );

echo( "Get array copy:\n" );
echo( '$converted = $copy = $test->getArrayCopy();' . "\n" );
$converted = $copy = $test->getArrayCopy();
echo( 'test_Container::ConvertToArray( $converted );' . "\n" );
test_Container::ConvertToArray( $converted );
print_r( $converted );

echo( "\n" );

echo( "Get converted copy:\n" );
echo( '$converted = $test->asArray();' . "\n" );
$converted = $test->asArray();
print_r( $converted );

echo( "\n" );

echo( "Convert to array:\n" );
echo( '$test->toArray();' . "\n" );
$test->toArray();
print_r( $test );

echo( "\n====================================================================================\n\n" );

//
// Instantiate with array.
//
echo( "Instantiate with array:\n" );
echo( '$test = new test_Container( $copy );' . "\n" );
$test = new test_Container( $copy );
print_r( $test );

echo( "\n" );

echo( "Instantiate with object and convert:\n" );
echo( '$new = new ArrayObject( $copy );' . "\n" );
$new = new ArrayObject( $copy );
echo( '$test = new test_Container( $new, TRUE );' . "\n" );
$test = new test_Container( $new, TRUE );
print_r( $test );


?>

