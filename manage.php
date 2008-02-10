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

// Get all possible roles
$groupRoles = $gContent->getRoles();
$gBitSmarty->assign('groupRoles', $groupRoles );

if( !empty( $_REQUEST["save_roles"] ) ) {
	// if that went ok store roles for the group
	foreach( array_keys( $groupRoles ) as $roleId ) {
		// don't store role 3 members since everyone is a member
		if ( $roleId != 3 ){
			foreach( $groupMembers as $user ) {
				$userId = $user['user_id'];
				if( !empty( $_REQUEST['users'][$roleId][$userId] )) {
					$gContent->assignUserRoleToGroup( $roleId, $userId, $gContent->mContentId );
				} else {
					$gContent->removeUserRoleFromGroup( $roleId, $userId, $gContent->mContentId );
				}
			}
		}
	}
	// refresh our groupMembers list to get their new roles
	$groupMembers = $gContent->getMembers();
}

$gBitSmarty->assign_by_ref( 'groupMembers', $groupMembers );

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Members' ) );
$gBitSystem->display( "bitpackage:group/edit_members_roles.tpl" );
?>
