<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
$gContent->verifyViewPermission();

// verify this group is public or the user is a member
if ( $gContent->mInfo['view_content_public'] != "y" && !$gBitUser->isInGroup( $gContent->mGroupId ) ){
	$gBitSystem->fatalError( tra( 'This is not a public group, you must be invited to join.' ));	
}

// make sure the user is registered
if ( !$gBitUser->isRegistered() ){
	// fatal out ot login/register page
	$gBitSystem->fatalPermission( NULL );
}

// if it has a custom theme lets theme it
$gContent->setGroupStyle();

// Load a users pending moderation if it exists
$pendingModeration = NULL;
if ($gBitSystem->isPackageActive('moderation')) {
	global $gModerationSystem;
	$listHash = array('content_id' => $gContent->mContentId,
					  'source_user_id' => $gBitUser->mUserId,
					  'package' => 'group',
					  'type' => 'join',
					  'status' => MODERATION_PENDING );
	$pendingModeration = $gModerationSystem->getList($listHash);
	$gBitSmarty->assign('joinModeration', $pendingModeration);
}

// if the user is already in the group
if( $gBitUser->isInGroup( $gContent->mGroupId ) ){
	// user is changing their preferences
   	if( !empty( $_REQUEST['save_prefs'] ) ){
		// @TODO store them
	} elseif( !empty( $_REQUEST['leave_group'] ) ){
		// remove the user from the group
		$gBitUser->removeUserFromGroup( $gBitUser->mUserId, $gContent->mGroupId );
		// @TODO remove their preferences for the group
		header( "Location: ".$gContent->getDisplayUrl() );
		die;
	}
} elseif( !empty( $_REQUEST["join_group"] ) ) {
// if join is confirmed then go for it
	// if group is free to join then do it
	if ( $gContent->mInfo['is_public'] == "y" ){
		if ( $gBitUser->addUserToGroup( $gBitUser->mUserId, $gContent->mGroupId ) ){
			if ( $gBitSystem->isPackageActive('switchboard') && !empty($_REQUEST['notice']) ) {
				global $gSwitchboardSystem;
				$gSwitchboardSystem->storeUserPref($gBitUser->mUserId, 'group', 'message', $gContent->mContentId,  $_REQUEST['notice']);
			}
			header( "Location: ".$gContent->getDisplayUrl() );
			die;
		}
	} else if( $gBitSystem->isPackageActive('moderation') ){
		// otherwise send the request to moderation if they aren't reloading
		if ( empty($pendingModeration) ) {
			$pendingModeration = $gModerationSystem->requestModeration('group', 'join', NULL, $gContent->mGroupId, $gContent->mContentId, empty($_REQUEST['join_message']) ? NULL : $_REQUEST['join_message'], MODERATION_PENDING, empty($_REQUEST['notice']) ? NULL : array('notice' => $_REQUEST['notice']));
			$gBitSmarty->assign('joinModeration', $pendingModeration);
		}
		else {
			$gBitSmarty->assign('errors', tra('You have already requested to join this group.'));
		}
	} else {
		$gBitSmarty->assign('errors', tra("This group is not public. You may not join."));
	}
}

// display
$gBitSystem->display( 'bitpackage:group/user_prefs.tpl', tra('Join')." ".$gContent->getTitle() );
?>
