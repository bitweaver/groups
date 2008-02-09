<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
$gBitSystem->verifyPermission('p_group_view' );

// @todo verify user has perm to access member list

// get the groups members
$groupMembers = $gBitUser->get_group_users( $_REQUEST["group_id"] );
$gBitSmarty->assign_by_ref( 'groupMembers', $groupMembers );

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Members' ) );
$gBitSystem->display( "bitpackage:group/list_members.tpl" );
?>
