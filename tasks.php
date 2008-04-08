<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin  
if( $gContent->isValid() ) {
	if( !( $gContent->hasEditPermission() || $gContent->hasUserPermission('p_group_group_members_admin') ) ){
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
			$msg = $moderation['data']['content_description']." ".$moderation['data']['title']." ".$_REQUEST['transition'];
		}
		$gBitSmarty->assign_by_ref( 'successMsg', $msg );
	}else{
		$msg = tra( 'Invalid request!' );
		$gBitSmarty->assign_by_ref( 'errorMsg', $msg );
	}
}

$modListHash = array( 'moderator_group_id'=>$gContent->mGroupId,
					  'status'=>'Pending' );
$modRequests = $gModerationSystem->getList( $modListHash );
$gBitSmarty->assign_by_ref( 'modRequests', $modRequests );

// email notification
if ( isset($_REQUEST['send_email']) && $gBitSystem->isPackageActive('switchboard') ) {
	$messageHash = array( 'subject' => "[".$gContent->getTitle()." ".tra('Group')."] ".$_REQUEST['email_subject'],
						  'message' => $_REQUEST['email_body'] );
	// @TODO finish this sending of email through SB, not sure how to format recipient list
	// $recipients = ?;
	// $gSwitchboardSystem->sendEvent('group', 'message', $gContent->mGroupId, $subject, $message );
}

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Tasks' ) );
$gBitSystem->display( "bitpackage:group/edit_group_tasks.tpl" );
?>
