<?php
// $Header: /cvsroot/bitweaver/_bit_groups/index.php,v 1.25 2008/04/07 20:00:00 wjames5 Exp $
// Copyright (c) 2008 bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );

//if the request is from the search field redirect us to the list - otherwise do what we do
if ( isset( $_REQUEST['find'] ) ){
	header ("location: ".GROUP_PKG_URI."list_groups.php?find=".$_REQUEST['find'] );
	die;
}

if( !isset( $_REQUEST['group_id'] ) ) {
	// if a default group has been set we'll use that
	$_REQUEST['group_id'] = $gBitSystem->getConfig( "home_group" );
}

// if we have a group_id, we display the correct group - otherwise we simply display a list
if( @BitBase::verifyId( $_REQUEST['group_id'] )) {
	include_once( GROUP_PKG_PATH.'display_group_inc.php' );
} else {
	include_once( GROUP_PKG_PATH.'display_group_portal_inc.php' );
}
?>
