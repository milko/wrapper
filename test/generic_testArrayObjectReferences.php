<?php

//
// Classes.
//
class test extends ArrayObject
{
	function & getReference( $theOffset ) { return $this[ $theOffset ]; }
	function setValue( $theOffset, $theValue ){ $this[ $theOffset ] = $theValue; }
	function delValue( $theOffset ){ unset( $this[ $theOffset ] ); }
}

//
// Test setting values.
//
echo( "\n=====================================\nTest setting values.\n" );
$x = new test();
$x[ 0 ] = [];
$x[ 0 ][ 1 ][ 3 ] = "pippo";
print_r( $x );

$y = & $x->getReference( 0 );
exit;

$y = & $x[ 0 ][ 1 ];
print_r( $y );

$y[ 4 ] = "pappa";
print_r( $y );
print_r( $x );

//
// Test resetting values.
//
echo( "\n=====================================\nTest resetting values.\n" );
unset( $y[ 4 ] );
print_r( $y );
print_r( $x );

unset( $x[ 0 ][ 1 ] );
print_r( $x );

?>