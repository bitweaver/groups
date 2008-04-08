<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin to edit an existing group 
if( $gContent->isValid() ) {
	if( !( $gContent->hasEditPermission() || $gContent->hasUserPermission('p_group_group_members_invite') ) ){
		$gBitSystem->fatalError( tra( 'You do not have permission to invite people to this group' ) );
	}

	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
}else{
	$gBitSystem->fatalError( tra( 'The Group you are trying to invite people to does not exist' ));
}

if( !empty( $_REQUEST["send_invite"] ) ) {
	// @TODO check all the email addresses are well formed
		// spit an error back if not
	if( FALSE ){
		$msg = tra( "There was a problem with the format of your email addresses. Please check they are well formed and separated by commas." );
		$gBitSmarty->assign_by_ref( 'errorMsg', $msg );
	}
	// @TODO foreach send invites
		// store the email address in the invite table
		// get an invite code
		// format the message and subject and send to switchboard
	$msg = tra( "Invitations sent!" );
	$gBitSmarty->assign_by_ref( 'successMsg', $msg );
}

// @TODO get a list of open invitations

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Invite Members' ) );
$gBitSystem->display( "bitpackage:group/edit_members_invites.tpl" );
?>
