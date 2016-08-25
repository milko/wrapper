<?php

function PO2Array( $theFile )
{
    //
    // Read file.
    //
    $file = file_get_contents( $theFile );
    if( $file !== FALSE )
    {
        //
        // Match english strings in file.
        //
        $count = preg_match_all( '/msgid ("(.*)"\n)+/', $file, $match );
        if( $count === FALSE )
            throw new Exception
            ( "Error parsing the file [$theFile]",
                kERROR_STATE );										// !@! ==>

        //
        // Normalise matches.
        //
        $match = $match[ 0 ];

        //
        // Normalise english strings.
        //
        $keys = Array();
        while( ($line = array_shift( $match )) !== NULL )
        {
            //
            // Get strings.
            //
            $count = preg_match_all( '/"(.*)"/', $line, $strings );
            if( $count === FALSE )
                throw new Exception
                ( "Error parsing the file [$theFile]",
                    kERROR_STATE );									// !@! ==>

            //
            // Merge strings.
            //
            $strings = $strings[ 1 ];
            if( count( $strings ) > 1 )
            {
                $tmp = '';
                foreach( $strings as $item )
                    $tmp .= $item;
                $keys[] = $tmp;
            }
            else
                $keys[] = $strings[ 0 ];
        }

        //
        // Match translated strings in file.
        //
        $count = preg_match_all( '/msgstr ("(.*)"\n)+/', $file, $match );
        if( $count === FALSE )
            throw new Exception
            ( "Error parsing the file [$theFile]",
                kERROR_STATE );										// !@! ==>

        //
        // Normalise matches.
        //
        $match = $match[ 0 ];

        //
        // Normalise english strings.
        //
        $values = Array();
        while( ($line = array_shift( $match )) !== NULL )
        {
            //
            // Get strings.
            //
            $count = preg_match_all( '/"(.*)"/', $line, $strings );
            if( $count === FALSE )
                throw new Exception
                ( "Error parsing the file [$theFile]",
                    kERROR_STATE );									// !@! ==>

            //
            // Merge strings.
            //
            $strings = $strings[ 1 ];
            if( count( $strings ) > 1 )
            {
                $tmp = '';
                foreach( $strings as $item )
                    $tmp .= $item;
                $values[] = $tmp;
            }
            else
                $values[] = $strings[ 0 ];
        }

        //
        // Combine array.
        //
        $matches = array_combine( $keys, $values );

        //
        // Get rid of header.
        //
        array_shift( $matches );

        return $matches;														// ==>

    } // Read the file.

    throw new Exception
    ( "Unable to read the file [$theFile]",
        kERROR_STATE );												// !@! ==>

} // PO2Array.

//$x = PO2Array( "/Users/milkoskofic/Documents/Development/Git/iso-codes/iso_3166-1/fr.po" );
//print_r( $x );

$enums = $types = [];
$file = "/Users/milkoskofic/Documents/Development/Git/iso-codes/data/iso_3166-2.json";
foreach( json_decode( file_get_contents( $file ), TRUE )[ "3166-2" ] as $record )
{
    $record[ "type" ] = ucfirst( $record[ "type" ] );
    $enums[ $record[ "code" ] ] = array_diff_assoc( $record, [ "code" => $record[ "code" ] ] );
    $types[] = $record[ "type" ];
}
$types = array_values( array_unique( $types ) );
//asort( $types );
print_r( $types );
print_r( $enums );


?>
