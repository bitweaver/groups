<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
$gContent->verifyViewPermission();

// verify this group is public
if ( $gContent->mInfo['view_content_public'] != "y" ){
	$gBitSystem->fatalError( tra( 'This is not a public group, you must be invited to join.' ));	
}

// make sure the user is registered
if ( !$gBitUser->isRegistered() ){
	// fatal out ot login/register page
	$gBitSystem->fatalPermission( NULL );
}

// if the user is already in the group ignore them and send them back to the group home page
if ( $gBitUser->isInGroup( $gContent->mGroupId ) ){
	header( "Location: ".$gContent->getDisplayUrl() );
	die;
}

// if join is confirmed then go for it
if( !empty( $_REQUEST["join_group"] ) ) {
	// if group is free to join then do it
	if ( $gContent->mInfo['is_public'] == "y" ){
		if ( $gBitUser->addUserToGroup( $gBitUser->mUserId, $gContent->mGroupId ) ){
			header( "Location: ".$gContent->getDisplayUrl() );
			die;
		}
	} else if( $gBitSystem->isPackageActive('moderation') ){
		// otherwise send the request to moderation
		$gModerationSystem->requestModeration('group', 'join', NULL, $gContent->mGroupId, $gContent->mContentId);
		// @TOOD display some page letting user know their membership is awaiting moderation
	} else {
		$gBitSmarty->assign('errors', tra("This group is not public. You may not join."));
	}
}

// display
$gBitSystem->display( 'bitpackage:group/join_group.tpl', tra('Join')." ".$gContent->getTitle() );
?>
