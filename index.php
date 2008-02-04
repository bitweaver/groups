<?php
// $Header: /cvsroot/bitweaver/_bit_groups/index.php,v 1.2 2008/02/04 19:09:10 nickpalmer Exp $
// Copyright (c) 2004 bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// Now check permissions to access this page
/* DEPRECATED I THINK -wjames
$gBitSystem->verifyPermission( 'p_group_read' );
*/

if( !isset( $_REQUEST['group_id'] ) ) {
	$_REQUEST['group_id'] = $gBitSystem->getConfig( "home_group" );
}

require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

if( !$gContent->isValid() ) {
	$gBitSystem->setHttpStatus( 404 );
	$gBitSystem->fatalError( "The group you requested could not be found." );
}

$gContent->verifyViewPermission();
$gContent->addHit();

// Display the template
$gBitSystem->display( 'bitpackage:group/group_display.tpl', tra( 'Group' ) );
?>
