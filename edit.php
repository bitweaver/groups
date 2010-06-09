<?php
/**
 * @version $Header$
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * 
 * $Id$
 * @package groups
 * @subpackage functions
 */
 
/**
 * Initialization
 */
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin to edit an existing group 
if( $gContent->isValid() ) {
	$gContent->verifyUpdatePermission();

	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
} else {
	$gContent->verifyCreatePermission();
}

// user canceled out get us out of here
if( !empty( $_REQUEST['cancel'] ) ) {
	if(  $gContent->isValid() ) {
		bit_redirect( $gContent->getDisplayUrl() );
	}else{
		bit_redirect( GROUP_PKG_URL );
	}
}

// get content types groups can associate with their group
$exclude = array( 'bitboard', 'bitgroup', 'bitcomment' );
$formGroupContent = array();
foreach( $gLibertySystem->mContentTypes as $cType ) {
    if( !in_array( $cType['content_type_guid'], $exclude ) && $gBitSystem->getConfig( 'group_content_'.$cType['content_type_guid'] ) ) {
		$formGroupContent['guids'][$cType['content_type_guid']]  = $gLibertySystem->getContentTypeName( $cType['content_type_guid'] );
    }
}

// If we are in preview mode then preview it!
if( isset( $_REQUEST["preview"] ) ) {
	$gContent->load();
    $gContent->preparePreview( $_REQUEST );
	$gBitSmarty->assign('preview', 'y');
	$gContent->invokeServices('content_preview_function');
}else {
  	$gContent->invokeServices( 'content_edit_function' );
}

// Get all rolls - used in access control options
$groupRoles = $gContent->getRoles();
$gBitSmarty->assign('groupRoles', $groupRoles );

// Get all perms - used in access control options
$allRolesPerms = $gContent->getRolesPerms();
$gBitSmarty->assign('allRolesPerms', $allRolesPerms );

// If we are saving
if( !empty( $_REQUEST["save_group"] ) || !empty( $_REQUEST["save_group_continue"] ) ) {

	// get the current public status of the group before we save. we need this later for setting access control
	$publicStatus = isset( $gContent->mInfo['view_content_public'] )?$gContent->mInfo['view_content_public']:NULL;

	// get the current post messages permission for group members before we save. we need this later for setting post messages access control
	$membersPostMsgsStatus = in_array( 'p_group_group_msgs_create', array_keys( $groupRoles[3]['perms'] ) )?TRUE:FALSE;

	// store it
	if( $gContent->store( $_REQUEST ) ) {
		// if its new store set the creator's email pref to receive email
		if( $gContent->mInfo['last_modified'] == $gContent->mInfo['created'] ){
			$gContent->storeUserEmailPref( 'email' );

			// if we are using mailiman mailing lists in boards then we send an annoying welcome message with the list password because mailman is such a fucking pain to theme
			// @TODO some day this will use a tpl and not even mailman
			if( $gBitSystem->getConfig( 'boards_sync_mail_server' ) && ( $board = $gContent->getBoard() ) && ( $mailingList = $board->getBoardMailingList() ) ){
               // format the message and subject and send to switchboard
                $subject = "Congratulations on your new ".$gBitSystem->getConfig('site_title')." group: ".$gContent->getTitle();

				$body = "Welcome ".$gBitUser->getDisplayName().",\n\n";
				$body .= "Here is some important information for you about your new group.\n\n";
				$body .= "You can get to your group at ".str_replace( '//', '/', (BIT_ROOT_URI.$gContent->getField('display_url')) )."\n\n";
				$body .= "You can post to your group's discussion board via email by sending messages to: ".$mailingList."\n\n";
				$body .= "If your group discussions are moderated, you can approve or reject messages group members post using email. You will receive email notices when new messages are posted. ";
				$body .= "To accept or reject them you will need your list password. Your mailing list password is: ".$board->getPreference('boards_mailing_list_password')." Please make a note of it.";

                // we pass in a hash so that the mailer knows which mime type we're sending. eventually when we have a tpl we can send as plain and html.
                $bodyHash['alt_message'] = $body;

                // send email - each one must be separate since invite code is unique
                $emailHash['recipients'] = array( array( 'email' => $gBitUser->mInfo['email'] ) );
				$emailHash['subject'] = $subject;
				$emailHash['message'] = $bodyHash;
                // @TODO add error handling so we know if there was a sending error
				$gSwitchboardSystem->sendEmail( $emailHash );
			}	
		}

		// if that went ok store role permissions for the group
		foreach( array_keys( $groupRoles ) as $roleId ) {
			if ( $roleId == 1 ) {
				// TODO: This could be made a LOT more efficient with one query.
				foreach( array_keys( $allRolesPerms ) as $perm ) {
					// Assign all permissions to admins.
					$gContent->assignPermissionToRole($perm, $roleId);
				}
			}
			else {
				foreach( array_keys( $allRolesPerms ) as $perm ) {
					if( !empty( $_REQUEST['group']['perms'][$roleId][$perm] )) {
						$gContent->assignPermissionToRole( $perm, $roleId);
					} else {
						$gContent->removePermissionFromRole( $perm, $roleId);
					}
				}
			}
		}
		
		
		//----- set comment posting permissions for related board -----//

		/**
		 * since groups inherently requires membership by default to post comments 
		 * to the group, then we need to revoke p_liberty_post_comments from all 
		 * other groups on the related board.
		 *
		 * further more, if members of the group also can not post comments, e.g. it
		 * is an announce only grop,  then we need to not give p_liberty_post_comments 
		 * to this group for the related board.
		 */
		// only set the custom preferences if there has been a status change
		$membersPostMsgs = !empty( $_REQUEST['group']['perms'][3]['p_group_group_msgs_create'] )?TRUE:FALSE;
		if ( $membersPostMsgs != $membersPostMsgsStatus ){ 
			$boardContentId = $gContent->mBoardObj->mContentId;
			$commPostPerm = 'p_liberty_post_comments';
			$allGroups = $gBitUser->getAllGroups($pListHash);
			// for each group thats not this group and has the permission revoke it
			foreach( $allGroups as $groupId => $group ){
				$groupPerms = array_keys( $group['perms'] );
				// if group has comment post perm by default and is not admin and not our group
				if ( $groupId != 1 && $groupId != $gContent->mGroupId  && in_array( $commPostPerm, $groupPerms ) ){
					// revoke
					$gContent->storePermission( $groupId, $commPostPerm, TRUE, $boardContentId );
				}
			}

			// set the perm for this group
			if ( $membersPostMsgs ){
				// if members can post comments then give the group the perm
				$gContent->storePermission( $gContent->mGroupId, $commPostPerm, FALSE, $boardContentId );
			}else{
				// if not lets make sure its not set
				$gContent->removePermission( $gContent->mGroupId, $commPostPerm, $boardContentId );
			}
		}
		// end setting p_liberty_post_comments custom permissions


		// store content types group can create
		if(!empty($formGroupContent['guids'])) {
			$groupContentTypes = array_keys( $formGroupContent['guids'] );
			// we check the full list so that if the admin options changed we automagically clean up the group
			foreach( $gLibertySystem->mContentTypes as $cType ) {
				$type = $cType['content_type_guid'];
				if ( !empty( $_REQUEST['group_content'] ) && in_array( $type, $_REQUEST['group_content'] ) && in_array( $type, $groupContentTypes ) ) {
					$gContent->storeContentTypePref( $type );
				} else {
					$gContent->expungeContentTypePref( $type );
				}
			}
		}

		// make sure the list is up to date after storing any prefs
		$gContent->mContentTypePrefs = $gContent->getContentTypePrefs();


		//------ set access permissions for the group and group related content ------//

		/* we only do this if the is_public status has changed,
		 * otherwise we will screw up access on any content that has been made public in private groups
		 * and we'll be setting custom perms over and over needlessly
		 */

		if ( $gContent->mInfo['view_content_public'] != $publicStatus ){
			// get list of user groups and their perms
			$pListHash = array();
			$allGroups = $gBitUser->getAllGroups($pListHash);
			// set view perms for our group
			foreach( $allGroups as $groupId => $group ){
				$groupPerms = array_keys( $group['perms'] );
				if ( $groupId != 1 && $groupId != $gContent->mGroupId && in_array( $gContent->mViewContentPerm, $groupPerms ) ){
					if ( $gContent->mInfo['view_content_public'] != 'y' ){
						// revoke
						$gContent->storePermission( $groupId, $gContent->mViewContentPerm, TRUE );
					}else{
						// unrevoke if revoked
						$gContent->removePermission( $groupId, $gContent->mViewContentPerm );
					}
				}
			}
			
			// assign a custom perm for our group if private else remove it if not needed
			if ( $gContent->mInfo['view_content_public'] != 'y' ){
				// assign custom view perm for our group
				$gContent->storePermission( $gContent->mGroupId, $gContent->mViewContentPerm );
			}else{
				// remove custom view perm for our group since its not needed
				$gContent->removePermission( $gContent->mGroupId, $gContent->mViewContentPerm );
			}
				
			// assign custom view perms for our group's linked content
			// we need this to get all view perms
			require_once(  LIBERTYSECURE_PKG_PATH.'libertysecure_lib.php' );
			// get all group linked content
			$listHash = array( "connect_group_content_id" => $gContent->mContentId );
			$list = $gContent->getContentList( $listHash );
			// for each content item set custom view perms
			foreach( $list as $content ){
				$typeGuid = $content['content_type_guid'];
				$contentId = $content['content_id'];
				if ( !isset( $gLibertySystem->mContentTypes[$typeGuid]['content_perms'] ) ){
					$gLibertySystem->mContentTypes[$typeGuid]['content_perms'] = secure_get_content_permissions( $typeGuid );
				}
				if ( isset( $gLibertySystem->mContentTypes[$typeGuid]['content_perms']['view'] ) ){
					$viewPerm =  $gLibertySystem->mContentTypes[$typeGuid]['content_perms']['view'];
					// foreach user group
					foreach( $allGroups as $groupId => $group ){
						$groupPerms = array_keys( $group['perms'] );
						// if group has content view perm by default and is not admin and not our group
						if ( $groupId != 1 && $groupId != $gContent->mGroupId  && in_array( $viewPerm, $groupPerms ) ){
							if ( $gContent->mInfo['view_content_public'] != 'y' ){
								// revoke
								$gContent->storePermission( $groupId, $viewPerm, TRUE, $contentId);
							}else{
								// unrevoke if revoked
								$gContent->removePermission( $groupId, $viewPerm, $contentId );
							}
						}
					}
					
					// set custom perm for our group
					if ( $gContent->mInfo['view_content_public'] != 'y' ){
						// assign view to our group 
						$gContent->storePermission( $gContent->mGroupId, $viewPerm, FALSE, $contentId );
					}else{
						// remove custom view perm for our group since its not needed
						$gContent->removePermission( $gContent->mGroupId, $viewPerm, $contentId );
					}
				}
			}
		}
		//----- end set access perms -----//

		if( !empty( $_REQUEST['save_group'] ) ){
			header( "Location: ".$gContent->getDisplayUrl() );
			die;
		}
		else{
			// user has requested to continue editing
			$gContent->load();
		}
	} else {
		$gContent->preparePreview( $_REQUEST );
		$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
	}
}

/* Check which content types this group allows
 * we ask for a fresh list since it might have changed
 */
$formGroupContent['checked'] = $gContent->getContentTypePrefs();
if( !$gContent->isValid() ){
	// if content is new our prefs will be nearly empty, so we fill them with all options, the form thus becomes opt out
	$formGroupContent['checked'] = array_merge( $formGroupContent['checked'], array_keys( $formGroupContent['guids'] ) );
}
$gBitSmarty->assign( 'formGroupContent', $formGroupContent );

// get options hash
require_once(GROUP_PKG_PATH.'options_inc.php'); 
// if is_public is empty in the hash, set it ot "n" to make our template work nice
if ( $gContent->isValid() && empty( $gContent->mInfo['is_public'] ) ){ $gContent->mInfo['is_public'] = 'n'; }

// to facilitate uploading a logo during group creation we include the attachment form, however it must NOT use ajax since preflight creation of the group content
// is not compatible with the complexity of group creation
$gBitSystem->setConfig('liberty_attachment_style', 'standard');

// Display the template
$gBitSystem->display( 'bitpackage:group/edit_group.tpl', tra('Group') , array( 'display_mode' => 'edit' ));
?>
