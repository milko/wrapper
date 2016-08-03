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
	function & test( array & $theOffset )
	{
		return $this->nestedPropertyReference( $theOffset );
	}
}

//
// Instantiate object.
//
echo( '$test = new test_Container();' . "\n\n" );
$test = new test_Container();
print_r( $test );

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
// Test nested properties.
//
echo( "Build nested object:\n" );
echo( '$test = new test_Container();' . "\n" );
$test = new test_Container();
echo( '$test[ "array" ] = [ 1, 2, 3 ];' . "\n" );
$test[ "array" ] = [ 1, 2, 3, "obj" => new ArrayObject( [ 1, 2, 3, "obj" => new ArrayObject( [9,8,7] ) ] ) ];
print_r( $test );

echo( "\n" );

echo( "Check nested offsetExists():\n" );
echo( '$result = $test->offsetExists( ["array", "obj", "obj", 0] );' . "\n" );
$result = $test->offsetExists( ["array", "obj", "obj", 0] );
var_dump( $result );
echo( '$result = $test->offsetExists( ["array", "obj", 9, 0] );' . "\n" );
$result = $test->offsetExists( ["array", "obj", 9, 0] );
var_dump( $result );

echo( "\n" );

echo( "Check nested offsetGet():\n" );
echo( '$result = $test->offsetGet( ["array", "obj", "obj", 0] );' . "\n" );
$result = $test->offsetGet( ["array", "obj", "obj", 0] );
var_dump( $result );
echo( '$result = $test->offsetGet( ["array", "obj", 9, 0] );' . "\n" );
$result = $test->offsetGet( ["array", "obj", 9, 0] );
var_dump( $result );

echo( "\n" );

echo( "Check nested offsetSet():\n" );
echo( '$test->offsetSet( ["array", "obj", "obj", 0], "NEW VALUE" );' . "\n" );
$test->offsetSet( ["array", "obj", "obj", 0], "NEW VALUE" );
print_r( $test );
echo( '$test->offsetSet( ["array", "obj", "NEW", NULL], "INSERTED VALUE" );' . "\n" );
$test->offsetSet( ["array", "obj", "NEW", NULL], "INSERTED VALUE" );
print_r( $test );
exit;

echo( "\n" );

echo( "Get nested property:\n" );
echo( '$result = $test[ "array" ][ "obj" ][ "obj" ][ 0 ];' . "\n" );
$result = $test[ "array" ][ "obj" ][ "obj" ][ 0 ];
var_dump( $result );

echo( "\n" );

echo( "Get root properties:\n" );
echo( '$result = & $test->ReferenceProperty();' . "\n" );
$result = & $test->ReferenceProperty();
print_r( $result );

echo( "\n" );

echo( "Get top level property:\n" );
echo( '$result = & $test->ReferenceProperty( "array" );' . "\n" );
$result = & $test->ReferenceProperty( "array" );
print_r( $result );

echo( "\n" );

echo( "Get second level property:\n" );
echo( '$result = & $test->ReferenceProperty( [ "array", 0 ] );' . "\n" );
$result = & $test->ReferenceProperty( [ "array", 0 ] );
print_r( $result );
echo( "\n" );

echo( "\n" );

echo( "Get third level property:\n" );
echo( '$result = & $test->ReferenceProperty( [ "array", "obj", 1 ] );' . "\n" );
$result = & $test->ReferenceProperty( [ "array", "obj", 1 ] );
print_r( $result );
echo( "\n" );

echo( "\n" );

echo( "Set third level property:\n" );
echo( '$result = & $test->ReferenceProperty( [ "array", "obj", 1 ] );' . "\n" );
$result = & $test->ReferenceProperty( [ "array", "obj", 1 ] );
echo( '$result = "MODIFIED";' . "\n" );
$result = "MODIFIED";
print_r( $test );

echo( "\n" );

echo( "Set last level property:\n" );
echo( '$result = & $test->ReferenceProperty( [ "array", "obj", "obj", 2 ] );' . "\n" );
$result = & $test->ReferenceProperty( [ "array", "obj", "obj", 2 ] );
echo( '$result = "ALSO MODIFIED";' . "\n" );
$result = "ALSO MODIFIED";
print_r( $test );

echo( "\n" );

echo( "Set last level property:\n" );
echo( '$test->NestedPropertySet( [ "array", "obj", "new", 1 ], "YET ANOTHER VALUE" );' . "\n" );
$test->NestedPropertySet( [ "array", "obj", "new", 1 ], "YET ANOTHER VALUE" );
print_r( $test );

echo( "\n" );

echo( "Get unknown property:\n" );
echo( '$result = & $test->ReferenceProperty( [ "array", "obj", "obj", 9 ] );' . "\n" );
$result = & $test->ReferenceProperty( [ "array", "obj", "obj", 9 ] );
var_dump( $result );

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
echo( 'test_Container::convertToArray( $converted );' . "\n" );
test_Container::convertToArray( $converted );
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

