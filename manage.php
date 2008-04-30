<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin to edit an existing group 
if( $gContent->isValid() ) {
	if( !( $gContent->hasEditPermission() || $gContent->hasUserPermission('p_group_group_members_admin') ) ){
		$gBitSystem->fatalError( tra( 'You do not have permission to administrate this groups members' ) );
	}

	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
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
					$gContent->assignUserRoleToGroup( $roleId, $userId );
				} else {
					$gContent->removeUserRoleFromGroup( $roleId, $userId );
				}
			}
		}
	}
	// refresh our groupMembers list to get their new roles
	$groupMembers = $gContent->getMembers();
}elseif( !empty( $_REQUEST["action"] ) && $_REQUEST['action'] == 'removeuser' ){
	$gBitUser->verifyTicket();
	if (!$gBitUser->userExists( array( 'user_id' => $_REQUEST["assign_user"] ) ) ) {
		$gBitSystem->fatalError( tra( "User doesnt exist" ));
	}

	$assignUser = new BitPermUser( $_REQUEST["assign_user"] );
	$assignUser->load( TRUE );

	if( $assignUser->isAdmin() && !$gBitUser->isAdmin() ) {
		$gBitSystem->fatalError( tra( 'You cannot modify a system administrator.' ));
	}
	// dump the users email prefs
	$gContent->deleteUserEmailPref( $assignUser ); 
	// remove the user from the group
	$gBitUser->removeUserFromGroup($_REQUEST["assign_user"], $_REQUEST["group_id"]);
	header( 'Location: '.$_SERVER['PHP_SELF'].'?group_id='.$gContent->mGroupId );
	die;
}

$gBitSmarty->assign_by_ref( 'groupMembers', $groupMembers );

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Members' ) );
$gBitSystem->display( "bitpackage:group/edit_members_roles.tpl" );
?>
