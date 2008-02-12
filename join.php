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
	$gBitSystem->fatalError( tra( 'You must be registed to join groups.' ));	
}

// if join is confirmed then go for it
if( !empty( $_REQUEST["join_group"] ) ) {
	// @TODO join process
	// if group is free to join then do it
	if ( $gContent->mInfo['is_public'] == "y" ){
		if ( $gBitUser->addUserToGroup( $gBitUser->mUserId, $gContent->mGroupId ) ){
			header( "Location: ".$gContent->getDisplayUrl() );
			die;
		}
	}else{
		// otherwise send the request to moderation
		$gMderationSystem->requestModeration('groups', 'join', NULL, $gContent->mGroupId, $gContent->mContentId);
		// @TOOD display some page letting user know their membership is awaiting moderation
	}
}

// display
$gBitSystem->display( 'bitpackage:group/join_group.tpl', tra('Join')." ".$gContent->getTitle() );
?>
