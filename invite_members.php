<?php
/**
 * @version $Header:
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
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

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin to edit an existing group 
if( $gContent->isValid() ) {
	if( !( $gContent->hasUpdatePermission() || $gContent->hasUserPermission('p_group_group_members_invite') ) ){
		$gBitSystem->fatalError( tra( 'You do not have permission to invite people to this group' ) );
	}

	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
}else{
	$gBitSystem->fatalError( tra( 'The Group you requested does not exist' ));
}

if ( !empty($_REQUEST['send_invite']) && $gBitSystem->isPackageActive('switchboard') ) {
	// check all the email addresses are well formed
    // $tokens = split("[ \t\r\n,]", $_REQUEST['email_addresses'] );
    $tokens = split("[^-A-z0-9\.@_]", $_REQUEST['email_addresses'] );

    $invalid = array();
    $valid = array();

	for ($i = 0; $i < count($tokens); $i++) {
		$tok = strtolower( $tokens[$i] );
		if( strlen( $tok ) ) { 
			if ( !ereg (
				'^[-!#$%&\`*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.
				 '[-!$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
				 '[-!$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$'
					, $tok ) ) { 
				array_push( $invalid, $tok );
			} else {
				array_push( $valid, $tok );
			}   
		}   
	}   

	if( count( $invalid ) > 0 ){
		// report an error and treat as preview
		$msg = tra( "There was a problem with the format of your email addresses. We have tried to diagnose the errors, please see below." );
		$gBitSmarty->assign_by_ref( 'errorMsg', $msg );
		$gBitSmarty->assign_by_ref( 'invalidEmail', $invalid ); 
		$gBitSmarty->assign_by_ref( 'validEmail', $valid ); 
		$gBitSmarty->assign_by_ref( 'email_addresses', $_REQUEST['email_addresses'] );
		$gBitSmarty->assign_by_ref( 'email_body', $_REQUEST['email_body'] );
	}else if( count( $valid ) > 0 ){
		// store the email addresses in the invite table and send email
		foreach ( $valid as $email ){
			$inviteHash = array( 'group_id' => $gContent->mGroupId,
								 'email' => $email );
			// @TODO this order seems wrong or we should but a db rollback in here. 
			// need to confirm the email was sent.
			if ( $invite = $gContent->storeInvitation( $inviteHash ) ){
				$inviteId = $invite['invite_id'];

				// format the message and subject and send to switchboard
				$subject = "Invitation to join ".$gContent->getTitle()." ".tra('Group');
				// create email body text. 
				// @TODO maybe move this to a tpl	
				$body = $gBitUser->getDisplayName()." ".$gBitUser->mInfo['email']." has invited you to join the group ".$gContent->getTitle()." at ".BIT_ROOT_URI;
				$body .= "\n\n".$_REQUEST['email_body']."\n\nTo join this group click the following url:\n".GROUP_PKG_URI."join.php?invite_id=".$inviteId;
				$body .= !empty($gContent->mInfo['group_desc'])? "\n\n".tra('Group Description')."\n\n".$gContent->mInfo['group_desc'] : "" ;
				// we pass in a hash so that the mailer knows which mime type we're sending. eventually when we have a tpl we can send as plain and html.
				$bodyHash['alt_message'] = $body;

				// send email - each one must be separate since invite code is unique
				$recipients = array( array( 'email' => $email ) );

				$emailHash['recipients'] = $recipients;
				$emailHash['subject'] = $subject;
				$emailHash['message'] = $bodyHash;
				// @TODO add error handling so we know if there was a sending error
				$gSwitchboardSystem->sendEmail( $emailHash );
			}
		}
		$msg = tra( 'Invitations sent!' );
		$gBitSmarty->assign_by_ref( 'successMsg', $msg );
	}
}

if ( !empty($_REQUEST['delete_invites']) && !empty( $_REQUEST['invites'] ) ) {
	if ( $gContent->hasUpdatePermission() || $gContent->hasUserPermission( 'p_group_group_members_admin' ) ){
		$deleted = TRUE;
		foreach( $_REQUEST['invites'] as $invite ){
			if ( !$gContent->expungeInvitation( $invite ) ){
				$deleted = FALSE;
				$msg = tra( 'There was a problem deleting one or more invitations.' );
				$gBitSmarty->assign_by_ref( 'errorDeleteMsg', $msg );
			}
		}
		if ( $deleted ){
			$msg = tra( 'Invitations deleted!' );
			$gBitSmarty->assign_by_ref( 'successDeleteMsg', $msg );
		}
	}else{
		$gBitSystem->fatalError( tra( 'You do not have permission to moderate invitations to this group' ) );
	}
}

if( $gContent->hasUpdatePermission() || $gContent->hasUserPermission( 'p_group_group_members_admin' ) ){
	// get a list of all pending invites
	$invites = $gContent->getInvitationsList(); 
	$gBitSmarty->assign_by_ref( 'invites', $invites );
}

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Invite Members' ) );
$gBitSystem->display( "bitpackage:group/edit_members_invites.tpl" , NULL, array( 'display_mode' => 'display' ));
?>
