<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin to edit an existing group 
if( $gContent->isValid() ) {
	$gContent->verifyEditPermission();
}else{
	$gBitSystem->fatalError( tra( 'The Group, whose membership you are attempting to administratei, does not exist' ));
}

// Get all the groups members
// @TODO use pagination
// @TODO need a way to get the additional roles of members
$groupMembers = $gBitUser->get_group_users( $_REQUEST["group_id"] );
$gBitSmarty->assign_by_ref( 'groupMembers', $groupMembers );

// Get all possible roles
$groupRoles = $gContent->getRoles();
$gBitSmarty->assign('groupRoles', $groupRoles );

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Members' ) );
$gBitSystem->display( "bitpackage:group/list_members.tpl" );
?>
