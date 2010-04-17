<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/tasks.php,v 1.12 2010/04/17 22:46:08 wjames5 Exp $
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
require_once( '../kernel/setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin  
if( $gContent->isValid() ) {
	if( !( $gContent->hasUpdatePermission() || $gContent->hasUserPermission('p_group_group_members_admin') ) ){
		$gBitSystem->fatalError( tra( "You do not have permission to manage this group's tasks" ) );
	}

	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
}else{
	$gBitSystem->fatalError( tra( 'The Group, whose tasks you are attempting to administrate, does not exist' ));
}

// moderations
if (isset($_REQUEST['moderation_id'])) {
	if( !empty($_REQUEST['transition']) ){
		$moderation = $gModerationSystem->getModeration(  $_REQUEST['moderation_id'] );
		$gModerationSystem->setModerationReply($_REQUEST['moderation_id'],
											   $_REQUEST['transition'],
											   (empty($_REQUEST['reply']) ?
												NULL : $_REQUEST['reply']) );

		if ( $moderation['type'] == "add_content" ){
			$msg = $moderation['data']['content_name']." ".$moderation['data']['title']." ".$_REQUEST['transition'];
		}
		$gBitSmarty->assign_by_ref( 'successModMsg', $msg );
	}else{
		$msg = tra( 'Invalid request!' );
		$gBitSmarty->assign_by_ref( 'errorModMsg', $msg );
	}
}

$modListHash = array( 'moderator_group_id'=>$gContent->mGroupId,
					  'status'=>'Pending' );
$modRequests = $gModerationSystem->getList( $modListHash );
$gBitSmarty->assign_by_ref( 'modRequests', $modRequests );

// email notification
if ( isset($_REQUEST['send_email']) && $gBitSystem->isPackageActive('switchboard') ) {
	$subject = "[".$gContent->getTitle()." ".tra('Group')."] ".$_REQUEST['email_subject'];
	$body = $_REQUEST['email_body'];
	$bodyHash['alt_message'] = $body;
	$usersHash = array( 'email', 'real_name', 'login', 'user_id' );
	$recipients = $gContent->getMembers();
	$emailHash['recipients'] = $recipients;
	$emailHash['subject'] = $subject;
	$emailHash['message'] = $bodyHash;
	$gSwitchboardSystem->sendEmail( $emailHash );
	$gBitSmarty->assign( 'successEmailMsg', 'Email sent!' );
}

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Tasks' ) );
$gBitSystem->display( "bitpackage:group/edit_group_tasks.tpl" , NULL, array( 'display_mode' => 'display' ));
?>
