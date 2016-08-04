<?php

//
// Test references.
//
$list = [ 1 => [ 2 => [ 3 => "tre" ] ] ];
$idx = [ 1, 2, 3 ];
print_r( $list );
print_r( $idx );
echo( "\n" );

while( count( $idx ) )
{
	$ref = & $list;
	for( $i = 0; $i < (count( $idx ) - 1); $i++ )
		$ref = & $ref[ $i ];
	$key = $idx[ $i ];
	echo( "$key\n" );

	array_pop( $idx );
}

?>