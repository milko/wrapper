<?php

/**
 * ISOCodesIterator.php
 *
 * This file contains the definition of the {@link ISOCodesIterator} class.
 */

namespace Milko\utils;

/*=======================================================================================
 *																						*
 *								ISOCodesIterator.php									*
 *																						*
 *======================================================================================*/

/**
 * <h4>ISO codes iterator.</h4><p />
 *
 * This <em>abstract</em> class implements an iterator that can scan an ISO standard from
 * the {@link https://pkg-isocodes.alioth.debian.org} repository and return a record
 * containing the available translations.
 *
 * The current class is abstract, because it doesn't make any assumption on which standard
 * it will be iterating, derived concrete classes must implement the {@link DefaultCode()}
 * and {@link Translated()} methods to handle the specifics of the standard.
 *
 * The {@link key()} method should return the code that uniquely identifies the standard
 * element, it should represent the preferred code in the standard.
 *
 * The class features other methods that return information regardiing the standard:
 * {@link Title()} returns the standard title, {@link Description()} returns the standard
 * description, {@link Required()} returns the list of required properties and
 * {@link Properties()} returns the list of properties in the standard.
 *
 *	@package	Utils
 *
 *	@author		Milko A. Škofič <skofic@gmail.com>
 *	@version	1.00
 *	@since		26/08/2016
 *
 * @example
 * <code>
 * // Set directory paths.
 * $po = "/some/path/to/iso-codes";		// In general these are at the root level.
 * $json = "/some/path/to/iso-codes/data";	// In general these are in the data directory.
 *
 * // Instantiate ISO.
 * $iso = new ISOCodes( $json, $po );
 *
 * // Instantiate iterator.
 * // Note that you must derive the class to use it.
 * $iterator = $iso->getIterator( $standard );
 *
 * // Get title.
 * $result = $iterator->Title();
 * // Get description.
 * $result = $iterator->Description();
 * // Get default code property name.
 * $result = $iterator->DefaultCode();
 * // Get list of required properties.
 * $result = $iterator->Required();
 * // Get list of translatable properties.
 * $result = $iterator->Translated();
 * // Get list of properties.
 * $result = $iterator->Properties();
 *
 * // Use iterator.
 * foreach( $iterator as $code => $data )
 * 	...
 * </code>
 */
abstract class ISOCodesIterator implements \Iterator, \Countable
{
	/**
	 * <h4>Position.</h4><p />
	 *
	 * This data member holds the current iterator position.
	 *
	 * @var int
	 */
	protected $mPosition = 0;

	/**
	 * <h4>Codes.</h4><p />
	 *
	 * This data member holds the standards codes array.
	 *
	 * @var array
	 */
	protected $mCodes = [];

	/**
	 * <h4>Schema.</h4><p />
	 *
	 * This data member holds the standards schema array.
	 *
	 * @var array
	 */
	protected $mSchema = [];

	/**
	 * <h4>Translations.</h4><p />
	 *
	 * This data member holds the translation table, it is an array structured as follows:
	 *
	 * <ul>
	 * 	<li><i>index</i>: The language code.
	 * 	<li><i>value</i>: The translation table as an array:
	 * 	 <ul>
	 * 		<li><i>index</i>: The english string.
	 * 		<li><i>value</i>: The translated string.
	 * 	 </ul>
	 * </ul>
	 *
	 * @var array
	 */
	protected $mTranslations = [];

	/**
	 * <h4>Translations.</h4><p />
	 *
	 * This data member holds the directory iterator to the translation files.
	 *
	 * @var \DirectoryIterator
	 */
	protected $mTranslationFiles = NULL;




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
	 * The class is instantiated by providing the following parameters:
	 *
	 * <ul>
	 * 	<li><b>$theCodes</b>: The standards list of codes and english names.
	 * 	<li><b>$theTrans</b>: The directory reference containing the <tt>.po</tt> files.
	 * 	<li><b>$theSchema</b>: The standards schema array.
	 * </ul>
	 *
	 * The provided parameters must have been checked beforehand: we expect the second
	 * parameter to point to a directory, which has been checked by the {@link IsoCodes}
	 * instance that has called this constructor.
	 *
	 * The first parameter represets the collection that will be iterated, the second
	 * parameter represents the directory that will be iterated to return the translations.
	 *
	 * @param array				$theCodes    		Codes and english strings list.
	 * @param \SplFileInfo		$theTrans      		Translation files directory.
	 *
	 * @uses translationTable()
	 */
	public function __construct( array $theCodes, \SplFileInfo $theTrans, array $theSchema )
	{
		//
		// Save codes.
		//
		$this->mCodes = $theCodes;

		//
		// Save directory iterator.
		//
		$this->mTranslationFiles = new \DirectoryIterator( $theTrans );

		//
		// Save schema.
		//
		$this->mSchema = $theSchema;

		//
		// Load translation table.
		//
		$this->translationTable();

	} // Constructor.



/*=======================================================================================
 *																						*
 *								PUBLIC ITERATOR INTERFACE	    						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	rewind																			*
	 *==================================================================================*/

	/**
	 * <h4>Rewind the Iterator to the first element.</h4><p />
	 *
	 * We reset the codes pointer to 0.
	 */
	public function rewind()
	{
		$this->mPosition = 0;

	} // rewind.


	/*===================================================================================
	 *	current																			*
	 *==================================================================================*/

	/**
	 * <h4>Return the current element.</h4><p />
	 *
	 * We implement this method by reshaping all properties returned by the
	 * {@link Translated()} method to hold an array whose index is the language code and the
	 * value the translated string.
	 *
	 * @return mixed				The current code and translations.
	 *
	 * @uses Translated()
	 */
	public function current()
	{
		//
		// Get current element.
		//
		$record = $this->mCodes[ $this->mPosition ];

		//
		// Iterate translatables.
		//
		foreach( $this->Translated() as $property )
		{
			//
			// Check if there.
			//
			if( array_key_exists( $property, $record ) )
			{
				//
				// Save english string and reset property.
				//
				$string = $record[ $property ];
				$record[ $property ]
					= [ ISOCodes::kDEFAULT_LANGUAGE => $string ];

				//
				// Find translations.
				//
				foreach( $this->mTranslations as $language => $table )
				{
					//
					// Match english string.
					//
					if( array_key_exists( $string, $table )
					 && strlen( $table[ $string ] ) )
						$record[ $property ][ $language ]
							= $table[ $string ];

				} // Iterating translations.

			} // Has property.

		} // Iterating translatable properties.

		return $record;																// ==>

	} // current.


	/*===================================================================================
	 *	key																				*
	 *==================================================================================*/

	/**
	 * <h4>Return the key of the current element.</h4><p />
	 *
	 * This method will call the {@link DefaultCode()} method to get the default code for
	 * the current standard.
	 *
	 * @return mixed				The current element key.
	 *
	 * @uses DefaultCode()
	 */
	public function key()
	{
		return $this->mCodes[ $this->mPosition ][ $this->DefaultCode() ];			// ==>

	} // key.


	/*===================================================================================
	 *	next																			*
	 *==================================================================================*/

	/**
	 * <h4>Move forward to next element.</h4><p />
	 *
	 * This method will advance the codes pointer.
	 */
	public function next()
	{
		++$this->mPosition;

	} // next.


	/*===================================================================================
	 *	valid																			*
	 *==================================================================================*/

	/**
	 * <h4>Check if current position is valid.</h4><p />
	 *
	 * This method will check whether the current position corresponds to an entry in the
	 * codes array.
	 *
	 * @return bool					<tt>TRUE</tt> if valid.
	 */
	public function valid()
	{
		return array_key_exists( $this->mPosition, $this->mCodes );					// ==>

	} // valid.


	/*===================================================================================
	 *	count																			*
	 *==================================================================================*/

	/**
	 * <h4>Return element count.</h4><p />
	 *
	 * This method will return the number of elements.
	 *
	 * @return int					Number of codes.
	 */
	public function count()
	{
		return count( $this->mCodes );												// ==>

	} // count.



/*=======================================================================================
 *																						*
 *							PUBLIC MEMBER ACCESSOR INTERFACE	   						*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	Title																			*
	 *==================================================================================*/

	/**
	 * <h4>Return standards title.</h4><p />
	 *
	 * This method can be used to retrieve the title of the standard.
	 *
	 * @return string				Standards title.
	 */
	public function Title()
	{
		return ( array_key_exists( "title", $this->mSchema ) )
			? $this->mSchema[ "title" ]												// ==>
			: NULL;																	// ==>

	} // Title.


	/*===================================================================================
	 *	Description																		*
	 *==================================================================================*/

	/**
	 * <h4>Return standards description.</h4><p />
	 *
	 * This method can be used to retrieve the description of the standard.
	 *
	 * @return string				Standards description.
	 */
	public function Description()
	{
		return ( array_key_exists( "description", $this->mSchema ) )
			? $this->mSchema[ "description" ]										// ==>
			: NULL;																	// ==>

	} // Description.


	/*===================================================================================
	 *	DefaultCode																		*
	 *==================================================================================*/

	/**
	 * <h4>Return default code property.</h4><p />
	 *
	 * This method can be used to retrieve the property name of the default code, this
	 * method is abstract and must be implemented by derived concrete classes.
	 *
	 * @return string				Default code property name.
	 */
	abstract public function DefaultCode();


	/*===================================================================================
	 *	Required																		*
	 *==================================================================================*/

	/**
	 * <h4>Return required properties.</h4><p />
	 *
	 * This method can be used to retrieve the list of required property names, the method
	 * will probe the "required" property of the schema.
	 *
	 * @return array				List of required properties.
	 */
	public function Required()
	{
		return ( array_key_exists( "required", $this->mSchema ) )
			 ? $this->mSchema[ "required" ]											// ==>
			 : [];																	// ==>

	} // Required.


	/*===================================================================================
	 *	Translated																		*
	 *==================================================================================*/

	/**
	 * <h4>Return list of translated properties.</h4><p />
	 *
	 * This method can be used to retrieve the list of property names that can be
	 * translated, the method is abstract and must be implemented by derived concrete
	 * classes.
	 *
	 * @return array				List of translatable properties.
	 */
	abstract public function Translated();


	/*===================================================================================
	 *	Properties																		*
	 *==================================================================================*/

	/**
	 * <h4>Return list of properties.</h4><p />
	 *
	 * This method can be used to retrieve the list of properties, the property name will be
	 * in the key and the property record in the value.
	 *
	 * @return array				List of properties.
	 */
	public function Properties()
	{
		return ( array_key_exists( "properties", $this->mSchema ) )
			? $this->mSchema[ "properties" ]										// ==>
			: [];																	// ==>

	} // Properties.



/*=======================================================================================
 *																						*
 *								PROTECTED PARSING INTERFACE								*
 *																						*
 *======================================================================================*/



	/*===================================================================================
	 *	translationTable																*
	 *==================================================================================*/

	/**
	 * <h4>Get translation table.</h4><p />
	 *
	 * This method will parse the provided PO files directory and set the
	 * {@link $mTranslations} member with the following array:
	 *
	 * <ul>
	 * 	<li><i>index</i>: The language code.
	 * 	<li><i>value</i>: The translation table as an array:
	 * 	 <ul>
	 * 		<li><i>index</i>: The english string.
	 * 		<li><i>value</i>: The translated string.
	 * 	 </ul>
	 * </ul>
	 *
	 * @throws \RuntimeException
	 */
	protected function translationTable()
	{
		//
		// Iterate translation files directory.
		//
		foreach( $this->mTranslationFiles as $file )
		{
			//
			// Skip dots.
			//
			if( $file->isDot() )
				continue;														// =>

			//
			// Handle PO file.
			//
			if( $file->isFile()
			 && (strtolower( $ext = $file->getExtension() ) == "po") )
			{
				//
				// Get language code.
				//
				$language = $file->getBasename( ".$ext" );

				//
				// Read file.
				//
				$file = file_get_contents( $file->getRealPath() );
				if( $file !== FALSE )
				{
					//
					// Match english and translated entries.
					//
					$count =
						preg_match_all(
							'/(msgid\s+"(.+)"\nmsgstr\s+"(.*)"\n)+/',
							$file,
							$match
						);
					if( $count === FALSE )
						throw new \RuntimeException(
							"Unable to parse file [$file], "
						);														// !@! ==>

					//
					// Set translation table.
					//
					$this->mTranslations[ $language ]
						= array_combine(
							$match[ 2 ], $match[ 3 ] );

				} // Read the file.

				else
					throw new \RuntimeException(
						"Unable to read file [$file]."
					);															// !@! ==>

			} // Is PO file.

		} // Iterating PO files directory.

	} // translationTable.




} // class ISOCodesIterator.


?>
