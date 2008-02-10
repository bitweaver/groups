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
	$gBitSystem->fatalError( tra( 'The Group, whose membership you are attempting to administrate, does not exist' ));
}

// Get all the groups members
// @TODO use pagination
$groupMembers = $gContent->getMembers();
$gBitSmarty->assign_by_ref( 'groupMembers', $groupMembers );

// Get all possible roles
$groupRoles = $gContent->getRoles();
$gBitSmarty->assign('groupRoles', $groupRoles );

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Members' ) );
$gBitSystem->display( "bitpackage:group/edit_members_roles.tpl" );
?>
