<?php
// $Header: /cvsroot/bitweaver/_bit_groups/index.php,v 1.7 2008/02/11 02:15:05 wjames5 Exp $
// Copyright (c) 2008 bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

//if the request is from the search field redirect us to the list - otherwise do what we do
if ( isset( $_REQUEST['find'] ) ){
	header ("location: ".GROUP_PKG_URI."list_groups.php?find=".$_REQUEST['find'] );
	die;
}

if( !isset( $_REQUEST['group_id'] ) ) {
	// if a default group has been set we'll use that
	$_REQUEST['group_id'] = $gBitSystem->getConfig( "home_group" );
}

require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
if( $gContent->isValid() ) {
	$gContent->verifyViewPermission();
} else {
	$gBitSystem->verifyPermission( 'p_group_view' );
}

if( !isset( $_REQUEST['group_id'] ) || !$gContent->isValid() ) {
	// if no group is requested, if no default is set, or the group requested is not valid we deliver a splash page about groups
	// get a list of groups the user is a member of
	if ( $gBitUser->isRegistered() ){
		$listHash = array( 'sort_mode' => 'group_name_asc' );
		$groupList = $gBitUser->getAllUserGroups();
		$gBitSmarty->assign('groups', $groupList);
	}
	$gBitSystem->display( 'bitpackage:group/group_home.tpl', tra( 'Groups' ) );
}else{
	$gContent->addHit();
	// Display the template
	$gBitSystem->display( 'bitpackage:group/group_display.tpl', tra( 'Group' ) );
}
?>
