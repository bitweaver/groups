<?php
/**
 * @version $Header:
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * 
 * @package groups
 * @subpackage functions
 */
 
/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// lets see if the user is trying to respond to an invitation
if ( !empty( $_REQUEST['invite_id'] ) ){
	$inviteId = $_REQUEST['invite_id'];
	// get the invite
	$invite = $gContent->getInvitation( $inviteId );
	// is the invite valid 
	if ( isset( $invite['invite_id'] ) ){
		// is the invite attached to a registered user?
		if ( isset( $invite['login'] ) ){
			// is the user logged in
			if ( $gBitUser->isRegistered() ){
				// does the invite email match the users email account?
				if ( $gBitUser->mInfo['email'] == $invite['email'] ){
					$gContent = new BitGroup( $invite['group_id'] );
					$gContent->load();
					// user cancelled
					if( !empty( $_REQUEST['cancel'] )) {
						// send them to group home page - we leave the invite intact in case they chain their mind an so admin can see they have not joined
						header( "Location: ".BIT_ROOT_URL."group" );
						die;
					// have they confirmed they want to join
					} elseif( empty( $_REQUEST['confirm'] )) {
						// ask them to confirm they want to joinn
						$gBitSystem->confirmDialog( $invite, 
							array( 
								'warning' => tra('Do you wish to join this group?') . ' ' . $gContent->getTitle()
							) 
						);
					// they want to join
					}else{
						// sign them up
						if ( $gBitUser->addUserToGroup( $gBitUser->mUserId, $gContent->mGroupId ) ){
							// delete invite
							$gContent->expungeInvitation( $inviteId );
							$gBitUser->loadGroups( TRUE );
							// let them continue to email prefs page...
						}
					}
				// the invite email address and the users email address do not match, so customize the error msg
				}else if( $gBitUser->isRegistered() ){
					$gBitSystem->fatalPermission( NULL, 
						tra('Your user account email does not match the email address associated with the invitation you are responding to.')
						."<br /><br />".
						tra('Please check your invitation code, update your account to use the same email address as the invitation was sent to, or ask the group admin to send you an invitation to your user account email address.')
					);
				}
			}else{
				// no
				// send them to login page
				$gBitSystem->fatalPermission( NULL );
			}
		// the invite is not attached to a registered user, but the user is logged in, so customize the error msg
		}else if( $gBitUser->isRegistered() ){
				$gBitSystem->fatalPermission( NULL, 
					tra('You appear to be logged in. The invitation you are responding to is not associated with a registered user.')
					."<br /><br />".
					tra('Please check your invitation code, update your account to use the same email address as the invitation was sent to, or ask the group admin to send you an invitation to your user account email address.')
				);
		}else{
			// no 
			// send them to registration page
			$gBitSystem->fatalPermission( NULL, 
				tra('You must register first to accept an invitation to a group.')
				."<br /><br />".
				tra('Be sure to register with the email address your invitation was sent to. You will be automatically added to the group when you register.')
			);
		}
	}else{
		$gBitSystem->fatalError( tra( 'Invalid invitation code! Please check the link you were emailed, and that you have copy-pasted it correctly.') );
	}
}

// Now check permissions to access this page
$gContent->verifyViewPermission();

// verify this group is public or the user is a member
if ( ( empty( $gContent->mInfo['view_content_public'] ) || $gContent->mInfo['view_content_public'] != "y" ) && !$gBitUser->isInGroup( $gContent->mGroupId ) ){
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
   	if( !empty( $_REQUEST['save_prefs'] ) && !empty($_REQUEST['notice']) ) {
		if( $gContent->storeUserEmailPref( $_REQUEST['notice'] ) ){
			$gBitSmarty->assign( "successMsg", "Email Preference Updated" );
		}
	} elseif( !empty( $_REQUEST['leave_group'] ) ){
		// dump the users email prefs
		$gContent->deleteUserEmailPref(); 
		// remove the user from the group
		$gBitUser->removeUserFromGroup( $gBitUser->mUserId, $gContent->mGroupId );
		header( "Location: ".$gContent->getDisplayUrl() );
		die;
	}
} elseif( !empty( $_REQUEST["join_group"] ) ) {
// if join is confirmed then go for it
	// if group is free to join then do it
	if ( $gContent->mInfo['is_public'] == "y" ){
		if ( $gBitUser->addUserToGroup( $gBitUser->mUserId, $gContent->mGroupId ) ){
			if ( !empty($_REQUEST['notice']) ) {
				if( $gContent->storeUserEmailPref( $_REQUEST['notice'] ) ){
					$gBitSmarty->assign( "successMsg", "Email Preference Updated" );
				}
			}
			header( "Location: ".$gContent->getDisplayUrl() );
			die;
		}
	} else if( $gBitSystem->isPackageActive('moderation') ){
		// otherwise send the request to moderation if they aren't reloading
		if ( empty($pendingModeration) ) {
			$pendingModeration = $gModerationSystem->requestModeration('group', 'join', NULL, $gContent->mGroupId, NULL, $gContent->mContentId, empty($_REQUEST['join_message']) ? NULL : $_REQUEST['join_message'], MODERATION_PENDING, empty($_REQUEST['notice']) ? NULL : array('notice' => $_REQUEST['notice']));
			$gBitSmarty->assign('joinModeration', $pendingModeration);
		}
		else {
			$gBitSmarty->assign('errors', tra('You have already requested to join this group.'));
		}
	} else {
		$gBitSmarty->assign('errors', tra("This group is not public. You may not join."));
	}
}

// are we using a mailing list
$board = $gContent->getBoard();
if( !empty($board) ) {
	$hasMailList = $board->getPreference('boards_mailing_list');
	$gBitSmarty->assign( 'hasMailList', $hasMailList );
}

// get the users current email pref
$userEmailPref = $gContent->getUserEmailPref();
$gBitSmarty->assign( 'userEmailPref', $userEmailPref );

// display
$gBitSystem->display( 'bitpackage:group/user_prefs.tpl', tra('Join')." ".$gContent->getTitle() , array( 'display_mode' => 'display' ));
?>
