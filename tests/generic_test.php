<?php

//
// Include local definitions.
//
require_once(dirname(__DIR__) . "/includes.local.php");

//
// Reference class.
//
use Milko\wrapper\Datasource;

// Instantiate datasource.
$dsn = new Datasource( 'protocol://user:pass@host:9090/dir/file?arg=val#frag' );

// Get protocol.
$result = $dsn->Protocol();
$result = $dsn[ Datasource::PROT ];
// string(8) "protocol"

// Get host.
$result = $dsn->Host();
$result = $dsn[ Datasource::HOST ];
// string(4) "host"

// Get port.
$result = $dsn->Port();
$result = $dsn[ Datasource::PORT ];
// int(9090)

// Get user.
$result = $dsn->User();
$result = $dsn[ Datasource::USER ];
// string(4) "user"

// Get password.
$result = $dsn->Password();
$result = $dsn[ Datasource::PASS ];
// string(4) "pass"

// Get path.
$result = $dsn->Path();
$result = $dsn[ Datasource::PATH ];
// string(9) "/dir/file"

// Get fragment.
$result = $dsn->Password();
$result = $dsn[ Datasource::FRAG ];
// string(4) "frag"

// Get query.
$result = $dsn->Query();
$result = $dsn[ Datasource::QUERY ];
// Array
// (
//	[arg] => val
// )

// Get URL.
$result = $dsn->URL();
// string(52) "protocol://user:pass@host:9090/dir/file?arg=val#frag"

// Change protocol.
$dsn->Protocol( "MySQL" );
$dsn[ Datasource::PROT ] = "MySQL";

// Remove user.
$dsn->User( FALSE );
$dsn[ Datasource::USER ] = FALSE;

// Remove port.
$dsn->Port( FALSE );
$dsn[ Datasource::PORT ] = NULL;

// Get URL.
$result = $dsn->URL();
// string(35) "MySQL://@host/dir/file?arg=val#frag"

// Instantiate datasource.
$dsn = new Datasource( 'protocol://user:pass@host1:9090,host2,host3:8080/dir/file?arg=val#frag' );

// Get host.
$result = $dsn->Host();
$result = $dsn[ Datasource::HOST ];
print_r( $result );
// Array
// (
//	[0] => host1
//	[1] => host2
//	[2] => host3
// )

// Get port.
$result = $dsn->Port();
$result = $dsn[ Datasource::PORT ];
print_r( $result );
// int(9090)

?>