<?php
/**
 * @version $Header$
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * 
 * @package groups
 * @subpackage functions
 */
 
/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

if( !isset( $_REQUEST['group_id'] ) && !isset( $_REQUEST['group_name'] ) ) {
	// if a default group has been set we'll use that
	$_REQUEST['group_id'] = $gBitSystem->getConfig( "home_group" );
}

// if we have a group_id or group_name, we display the correct group - otherwise we simply display a list
if( @BitBase::verifyId( $_REQUEST['group_id'] ) || !empty( $_REQUEST['group_name'] ) ) {
	include_once( GROUP_PKG_PATH.'display_group_inc.php' );
} else {
	include_once( GROUP_PKG_PATH.'display_group_portal_inc.php' );
}
?>
