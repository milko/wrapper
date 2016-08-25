<?php

/**
 * ISOCodes.php
 *
 * This file contains the definition of the {@link ISOCodes} class.
 */

namespace Milko\utils;

/*=======================================================================================
 *																						*
 *									ISOCodes.php										*
 *																						*
 *======================================================================================*/

/**
 * <h4>ISO codes loader.</h4><p />
 *
 * This <em>utility</em> class can be used to compile a collection of Json files containing
 * various ISO standards from the {@link https://pkg-isocodes.alioth.debian.org} repository.
 *
 * The standards supported by this class are:
 *
 * <ul>
 *  <li><b>639-2</b>: ISO 639-2 language codes.
 *  <li><b>639-3</b>: ISO 639-3 language codes.
 *  <li><b>639-5</b>: ISO 639-5 language family and groups codes.
 *  <li><b>3166-1</b>: ISO 3166-1 country codes.
 *  <li><b>3166-2</b>: ISO 3166-2 country and subdivision codes.
 *  <li><b>3166-3</b>: ISO 3166-3 formerly used country codes.
 *  <li><b>4217</b>: ISO 4217 currency codes.
 *  <li><b>15924</b>: ISO 15924 codes for the representation of names of scripts.
 * </ul>
 *
 * The class expects a directory containing Json files: file names prefixed with
 * <tt>iso_</tt> contain the codes, file names prefixed with <tt>schema-</tt> contain the
 * codes file schema.
 *
 * The language translations should be in a directory containing a set of directories whose
 * names are prefixed with <tt>iso_</tt> containing the <tt>.po</tt> files.
 *
 * The class will write two files to the output directory: <tt>schema.json</tt> containing
 * the combined schemas and a set of files, prefixed with the standard name, containing the
 * code and translations. All name fields will be an array whose index is the language code
 * and whose value is the translated name.
 *
 * We assume by default that English is the base language.
 *
 *	@package	Utils
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		25/08/2016
 */
class ISOCodes
{
	/**
	 * <h4>Default language.</h4><p />
	 *
	 * This constant identifies the default language code.
	 */
	const kDEFAULT_LANGUAGE = "en";

	/**
	 * <h4>ISO 639-2 base name.</h4><p />
	 *
	 * This constant identifies the ISO 639-2 standard.
	 */
	const k639_2 = "639-2";
	
	/**
	 * <h4>ISO 639-3 base name.</h4><p />
	 *
	 * This constant identifies the ISO 639-3 standard.
	 */
	const k639_3 = "639-3";
	
	/**
	 * <h4>ISO 639-5 base name.</h4><p />
	 *
	 * This constant identifies the ISO 639-5 standard.
	 */
	const k639_5 = "639-5";
	
	/**
	 * <h4>ISO 3166-1 base name.</h4><p />
	 *
	 * This constant identifies the ISO 3166-1 standard.
	 */
	const k3166_1 = "3166-1";
	
	/**
	 * <h4>ISO 3166-2 base name.</h4><p />
	 *
	 * This constant identifies the ISO 3166-2 standard.
	 */
	const k3166_2 = "3166-2";
	
	/**
	 * <h4>ISO 3166-3 base name.</h4><p />
	 *
	 * This constant identifies the ISO 3166-3 standard.
	 */
	const k3166_3 = "3166-3";
	
	/**
	 * <h4>ISO 4217 base name.</h4><p />
	 *
	 * This constant identifies the ISO 4217 standard.
	 */
	const k4217 = "4217";

	/**
	 * <h4>ISO 15924 base name.</h4><p />
	 *
	 * This constant identifies the ISO 15924 standard.
	 */
	const k15924 = "15924";

	/**
	 * <h4>Source Json files directory.</h4><p />
	 *
	 * This data member holds the path to the source Json files.
	 *
	 * @var \SplFileInfo
	 */
	protected $mJson = NULL;
	
	/**
	 * <h4>Source <tt>.po</tt> files directory.</h4><p />
	 *
	 * This data member holds the path to the source <tt>.po</tt> files.
	 *
	 * @var \SplFileInfo
	 */
	protected $mPo = NULL;
	
	/**
	 * <h4>Destination files directory.</h4><p />
	 *
	 * This data member holds the path to the destination directory.
	 *
	 * @var \SplFileInfo
	 */
	protected $mDest = NULL;
	
	/**
	 * <h4>Standards codes.</h4><p />
	 *
	 * This data member holds the list of standards codes.
	 *
	 * @var array
	 */
	protected $mStandards = [];

	/**
	 * <h4>Language codes.</h4><p />
	 *
	 * This data member holds the list of language codes found in the <tt>.po</tt>
	 * directory.
	 *
	 * @var array
	 */
	protected $mLanguages = [];

	/**
	 * <h4>Schema.</h4><p />
	 *
	 * This data member holds the list of schemas.
	 *
	 * @var array
	 */
	protected $mSchema = [];

	/**
	 * <h4>Types.</h4><p />
	 *
	 * This data member collects the list of property types.
	 *
	 * @var array
	 */
	protected $mTypes = [];




/*=======================================================================================
 *																						*
 *										MAGIC											*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	__construct																		*
	 *==================================================================================*/

	/**
	 * <h4>Instantiate class.</h4><p />
	 *
	 * The method will check the provided directory parameters and compile the list of
	 * languages.
	 *
	 * @param string			$theJson    		Json files path.
	 * @param string			$thePo      		PO files path.
	 * @throws \InvalidArgumentException
	 *
	 * @uses loadLanguages()
	 */
	public function __construct( string $theJson, string $thePo )
	{
		//
		// Check source directories.
		//
		$this->mJson = new \SplFileInfo( $theJson );
		if( ! $this->mJson->isDir() )
			throw new \InvalidArgumentException(
				"Json files source parameter is not a directory."
			);                                                                  // !@! ==>
		if( ! $this->mJson->isReadable() )
			throw new \InvalidArgumentException(
				"Json files source parameter is not readable."
			);                                                                  // !@! ==>
		$this->mPo = new \SplFileInfo( $thePo );
		if( ! $this->mPo->isDir() )
			throw new \InvalidArgumentException(
				"PO files source parameter is not a directory."
			);                                                                  // !@! ==>
		if( ! $this->mPo->isReadable() )
			throw new \InvalidArgumentException(
				"PO files source parameter is not readable."
			);                                                                  // !@! ==>
		
		//
		// Load standards.
		//
		$this->mStandards = $this->loadStandards();

		//
		// Load languages.
		//
		$this->mLanguages = $this->loadLanguages();

		//
		// Load schema.
		//
		$this->mSchema = $this->loadSchema();

	} // Constructor.



/*=======================================================================================
 *																						*
 *									PUBLIC INTERFACE	    							*
 *																						*
 *======================================================================================*/
	
	
	
	/*===================================================================================
	 *	Standards																		*
	 *==================================================================================*/
	
	/**
	 * <h4>Return standards list.</h4><p />
	 *
	 * This method can be used to retrieve the list of standards.
	 *
	 * @return array				List of standards codes.
	 */
	public function Standards()
	{
		return $this->mStandards;													// ==>
		
	} // Standards.


	/*===================================================================================
	 *	Languages																		*
	 *==================================================================================*/

	/**
	 * <h4>Return languages list.</h4><p />
	 *
	 * This method can be used to retrieve the list of language codes.
	 *
	 * @return array				List of language codes.
	 */
	public function Languages()
	{
		return $this->mLanguages;													// ==>

	} // Languages.


	/*===================================================================================
	 *	Schema																			*
	 *==================================================================================*/

	/**
	 * <h4>Return schema.</h4><p />
	 *
	 * This method can be used to retrieve the schemas.
	 *
	 * @return array				List of standard schemas.
	 */
	public function Schema()
	{
		return $this->mSchema;														// ==>

	} // Schema.


	/*===================================================================================
	 *	Types																			*
	 *==================================================================================*/

	/**
	 * <h4>Return schema types.</h4><p />
	 *
	 * This method can be used to retrieve the schema types.
	 *
	 * @return array				List of schema types.
	 */
	public function Types()
	{
		return $this->mTypes;														// ==>

	} // Types.


	/*===================================================================================
	 *	Dump																			*
	 *==================================================================================*/

	/**
	 * <h4>Dump files.</h4><p />
	 *
	 * This method can be used to dump the result files in the provided directory.
	 *
	 * @param string				$theDestination 	Destination directory path.
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	public function Dump( string $theDestination )
	{
		//
		// Check destination directory.
		//
		$this->mDest = new \SplFileInfo( $theDestination );
		if( ! $this->mDest->isDir() )
			throw new \InvalidArgumentException(
				"Destination parameter is not a directory."
			);                                                                  // !@! ==>
		if( ! $this->mDest->isWritable() )
			throw new \InvalidArgumentException(
				"Destination parameter is not writable."
			);                                                                  // !@! ==>

		$x = $this->getTranslationTable( "3166-1", "it" );
		print_r( $x );
		exit;
		//
		// Iterate standards.
		//
		foreach( $this->mStandards as $standard )
		{

		} // Iterating standards.

	} // Dump.



/*=======================================================================================
 *																						*
 *								PROTECTED LOADING INTERFACE								*
 *																						*
 *======================================================================================*/
	
	
	
	/*===================================================================================
	 *	loadStandards																	*
	 *==================================================================================*/
	
	/**
	 * <h4>Load standards codes.</h4><p />
	 *
	 * This method will load the standards in a data member for use by methods.
	 *
	 * @return array				List of standards.
	 */
	protected function loadStandards()
	{
		return [
			self::k639_2, self::k639_3, self::k639_5,
			self::k3166_1, self::k3166_2, self::k3166_3,
			self::k4217, self::k15924
		];																			// ==>
		
	} // loadStandards.


	/*===================================================================================
	 *	loadLanguages																	*
	 *==================================================================================*/

	/**
	 * <h4>Load language codes.</h4><p />
	 *
	 * This method will parse the PO files directory and return the prefix of all
	 * <tt>.po</tt> file names, which correspond to the language codes.
	 *
	 * @return array				List of languages.
	 */
	protected function loadLanguages()
	{
		//
		// Init local storage.
		//
		$langs = [];

		//
		// Iterate PO directory directory.
		//
		foreach( new \DirectoryIterator( $this->mPo ) as $file )
		{
			//
			// Skip dots.
			//
			if( $file->isDot() )
				continue;														// =>

			//
			// Handle PO directory.
			//
			if( $file->isDir()
			 && (substr( $file->getBasename(), 0, 4 ) == "iso_")
			 && in_array( substr( $file->getBasename(), 4 ), $this->mStandards ) )
			{
				//
				// Iterate directory.
				//
				foreach( new \DirectoryIterator( $file->getRealPath() ) as $sub )
				{
					//
					// Handle PO file.
					//
					if( $sub->isFile()
					 && (strtolower( $ext = $sub->getExtension() ) == "po") )
						$langs[] = $sub->getBasename( ".$ext" );

				} // Iterating PO files directory.

			} // Possible PO files directory.

		} // Iterating PO directory directory.

		//
		// Normalise list.
		//
		$langs = array_unique( $langs );
		asort( $langs );

		return array_values( $langs );												// ==>

	} // loadLanguages.


	/*===================================================================================
	 *	loadSchema																		*
	 *==================================================================================*/

	/**
	 * <h4>Load schema.</h4><p />
	 *
	 * This method will parse the schema files and load them into the object's member.
	 *
	 * @return array				List of schemas.
	 */
	protected function loadSchema()
	{
		//
		// Init local storage.
		//
		$schemas = [];

		//
		// Iterate Json files directory.
		//
		foreach( new \DirectoryIterator( $this->mJson ) as $file )
		{
			//
			// Skip dots.
			//
			if( $file->isDot() )
				continue;														// =>

			//
			// Select Json files.
			//
			if( $file->isFile()
			 && (strtolower( $ext = $file->getExtension() ) == "json") )
			{
				//
				// Select schema files.
				//
				if( (substr( $file->getBasename( ".$ext" ), 0, 7 ) == "schema-")
				 && in_array( substr( $file->getBasename( ".$ext" ), 7 ),
							  $this->mStandards ) )
				{
					//
					// Load schema.
					//
					$schema
						= json_decode(
							file_get_contents( $file->getRealPath() ),
							TRUE
					);

					//
					// Get schema code.
					//
					foreach( $schema[ "properties" ] as $code => $value ) break;

					//
					// Load schema attributes.
					//
					$schemas[ $code ] = [];
					if( array_key_exists( '$schema', $schema ) )
						$schemas[ $code ][ '$schema' ] = $schema[ '$schema' ];
					if( array_key_exists( "title", $schema ) )
						$schemas[ $code ][ "title" ] = $schema[ "title" ];
					if( array_key_exists( 'description', $schema ) )
						$schemas[ $code ][ "description" ] = $schema[ "description" ];
					if( array_key_exists( "required",
										  $schema[ "properties" ][ $code ][ "items" ] ) )
						$schemas[ $code ][ "required" ]
							= $schema[ "properties" ][ $code ][ "items" ][ "required" ];

					//
					// Load properties.
					//
					$schemas[ $code ][ "properties" ]
						= $schema[ "properties" ][ $code ][ "items" ][ "properties" ];

					//
					// Collect types.
					//
					foreach( $schemas[ $code ][ "properties" ] as $prop )
						$this->mTypes[] = $prop[ "type" ];

				} // Is schema file.

			} // Json file.

		} // Iterating Json files directory.

		//
		// Normalise types.
		//
		$this->mTypes = array_values( array_unique( $this->mTypes ) );

		return $schemas;															// ==>

	} // loadSchema.



/*=======================================================================================
 *																						*
 *								PROTECTED PARSING INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	getTranslationTable																*
	 *==================================================================================*/

	/**
	 * <h4>Get translation table.</h4><p />
	 *
	 * This method will return the translation table for the provided standard.
	 *
	 * @param string				$theStandard	 	Standard code.
	 * @param string				$theLanguage	 	Language code.
	 * @return array				Translation table.
	 * @throws \RuntimeException
	 */
	protected function getTranslationTable( string $theStandard, string $theLanguage )
	{
		//
		// Determine file.
		//
		$filename = $this->mPo->getRealPath()
					. DIRECTORY_SEPARATOR
					. "iso_"
					. $theStandard
					. DIRECTORY_SEPARATOR
					. $theLanguage
					. ".po";

		//
		// Read file.
		//
		$file = file_get_contents( $filename );
		if( $file !== FALSE )
		{
			//
			// Match english strings in file.
			//
			$count = preg_match_all( '/msgid ("(.+)"\n)+/', $file, $match );
			if( $count === FALSE )
				throw new \RuntimeException(
					"Error parsing file [$filename], " .
					"unable to parse english strings."
				);																// !@! ==>

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
					throw new \RuntimeException(
						"Error parsing file [$filename], " .
						"unable to normalise english strings."
					);															// !@! ==>

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
			print_r( $keys );

			//
			// Match translated strings in file.
			//
			$count = preg_match_all( '/msgstr ("(.+)"\n)+/', $file, $match );
			if( $count === FALSE )
				throw new \RuntimeException(
					"Error parsing file [$filename], " .
					"unable to match translated strings."
				);																// !@! ==>

			//
			// Normalise matches.
			//
			$match = $match[ 0 ];

			//
			// Normalise translated strings.
			//
			$values = Array();
			while( ($line = array_shift( $match )) !== NULL )
			{
				//
				// Get strings.
				//
				$count = preg_match_all( '/"(.*)"/', $line, $strings );
				if( $count === FALSE )
					throw new \RuntimeException(
						"Error parsing file [$filename], " .
						"unable to get english strings."
					);															// !@! ==>

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
			print_r( $values );
			exit;

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

		throw new \RuntimeException(
			"Unable to read file [$filename]."
		);																		// !@! ==>

	} // getTranslationTable.




} // class ISOCodes.


?>
