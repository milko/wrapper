<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference class.
//
use Milko\wrapper\Url;

// Instantiate from URL.
$dsn = new Url( 'protocol://user:pass@host:9090/dir/file?arg=val#frag' );

// Return full URL.
$test = $dsn->URL();
// string(52) "protocol://user:pass@host:9090/dir/file?arg=val#frag"

// Return URL without path.
$test = $dsn->URL( [ Url::PATH ] );
// string(43) "protocol://user:pass@host:9090?arg=val#frag"

exit;


// Instantiate empty Object.
$dsn = new Url();

// Set protocol.
$dsn->Protocol( "MySQL" );
$dsn[ Url::PROT ] = "MySQL";

// Set host.
$dsn->Host( "localhost" );
$dsn[ Url::HOST ] = "localhost";

// Set port.
$dsn->Port( 3306 );
$dsn[ Url::PORT ] = 3306;

// Set database and table.
$dsn->Path( "Database/Table" );
$dsn[ Url::PATH ] = "Database/Table";

// Set user.
$dsn->User( "user" );
$dsn[ Url::USER ] = "user";

// Set password.
$dsn->Password( "password" );
$dsn[ Url::PASS ] = "password";

// Milko\wrapper\Url Object
// (
//     [mProperties:protected] => Array
//         (
//             [prot] => MySQL
//             [host] => localhost
//             [port] => 3306
//             [path] => Database/Table
//             [user] => user
//             [pass] => password
//         )
// )

$url = $dsn->URL();
$url = (string)$dsn;
// string(51) "MySQL://user:password@localhost:3306/Database/Table"

// Instantiate from URL.
$dsn = new Url( 'protocol://user:pass@host:9090/dir/file?arg=val#frag' );
// Milko\wrapper\Datasource Object
// (
//     [mProperties:protected] => Array
//         (
//             [prot] => protocol
//             [host] => host
//             [port] => 9090
//             [user] => user
//             [pass] => pass
//             [path] => /dir/file
//             [quer] => Array
//                 (
//                     [arg] => val
//                 )
//             [frag] => frag
//         )
// )

// Get protocol.
$result = $dsn->Protocol();
$result = $dsn[ Url::PROT ];
// string(8) "protocol"

// Get host.
$result = $dsn->Host();
$result = $dsn[ Url::HOST ];
// string(4) "host"

// Get port.
$result = $dsn->Port();
$result = $dsn[ Url::PORT ];
// int(9090)

// Get user.
$result = $dsn->User();
$result = $dsn[ Url::USER ];
// string(4) "user"

// Get password.
$result = $dsn->Password();
$result = $dsn[ Url::PASS ];
// string(4) "pass"

// Get path.
$result = $dsn->Path();
$result = $dsn[ Url::PATH ];
// string(9) "/dir/file"

// Get fragment.
$result = $dsn->Password();
$result = $dsn[ Url::FRAG ];
// string(4) "frag"

// Get query.
$result = $dsn->Query();
$result = $dsn[ Url::QUERY ];
// Array
// (
//	[arg] => val
// )

// Get URL.
$result = $dsn->URL();
$result = (string)$dsn;
// string(52) "protocol://user:pass@host:9090/dir/file?arg=val#frag"

// Change protocol.
$dsn->Protocol( "MySQL" );
$dsn[ Url::PROT ] = "MySQL";

// Remove user.
$dsn->User( FALSE );
$dsn[ Url::USER ] = FALSE;

// Remove port.
$dsn->Port( FALSE );
$dsn[ Url::PORT ] = NULL;

// Get URL.
$result = $dsn->URL();
// string(35) "MySQL://@host/dir/file?arg=val#frag"

$dsn = new Url( 'protocol://user:pass@host1:9090,host2,host3:8080/dir/file?arg=val#frag' );
// Milko\wrapper\Datasource Object
// (
//     [mProperties:protected] => Array
//         (
//             [prot] => protocol
//             [host] => Array
//                 (
//                     [0] => host1
//                     [1] => host2
//                     [2] => host3
//                 )
//             [port] => Array
//                 (
//                     [0] => 9090
//                     [1] =>
//                     [2] => 8080
//                 )
//             [user] => user
//             [pass] => pass
//             [path] => /dir/file
//             [quer] => Array
//                 (
//                     [arg] => val
//                 )
//             [frag] => frag
//         )
// )

// Get host.
$result = $dsn->Host();
$result = $dsn[ Url::HOST ];
// Array
// (
//	[0] => host1
//	[1] => host2
//	[2] => host3
// )

// Get port.
$result = $dsn->Port();
$result = $dsn[ Url::PORT ];
// Array
// (
//	[0] => 9090
//	[1] => NULL
//	[2] => 8080
// )

?>