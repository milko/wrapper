<?php

use \Phan\Config;

/**
 * This configuration will be read and overlayed on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 *
 * @see src/Phan/Config.php
 * See Config for all configurable options.
 *
 * A Note About Paths
 * ==================
 *
 * Files referenced from this file should be defined as
 *
 * ```
 *   Config::projectPath('relative_path/to/file')
 * ```
 *
 * where the relative path is relative to the root of the
 * project which is defined as either the working directory
 * of the phan executable or a path passed in via the CLI
 * '-d' flag.
 */
return [

	// A directory list that defines files that will be excluded
	// from static analysis, but whose class and method
	// information should be included.
	//
	// Generally, you'll want to include the directories for
	// third-party code (such as "vendor/") in this list.
	//
	// n.b.: If you'd like to parse but not analyze 3rd
	//       party code, directories containing that code
	//       should be added to the `directory_list` as
	//       to `excluce_analysis_directory_list`.
	"exclude_analysis_directory_list" => [
		'vendor/',
		'test/',
		'docs/',
		'old/'
	],

	// A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
		'src',
		'vendor/mongodb/mongodb/src',
		'vendor/mongodb/mongodb/src/Exception',
		'vendor/mongodb/mongodb/src/GridFS',
		'vendor/mongodb/mongodb/src/Model',
		'vendor/mongodb/mongodb/src/Operation',
        'vendor/triagens/arangodb/lib/triagens/ArangoDb'
    ],

	// If true, missing properties will be created when
	// they are first seen. If false, we'll report an
	// error message.
	"allow_missing_properties" => false,

	// Allow null to be cast as any type and for any
	// type to be cast to null.
	"null_casts_as_any_type" => false,

	// Backwards Compatibility Checking
	'backward_compatibility_checks' => false,

	// Run a quick version of checks that takes less
	// time
	"quick_mode" => false,

	// Only emit critical issues
	"minimum_severity" => 10,

	// A set of fully qualified class-names for which
	// a call to parent::__construct() is required
	'parent_constructor_required' => [
	]
];

?>