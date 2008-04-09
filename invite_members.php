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
		// @TODO store the email address in the invite tabl
		$inviteCode = "@TODO_GET_KEY_ID";
		// format the message and subject and send to switchboard
		$subject = "Invitation to join ".$gContent->getTitle()." ".tra('Group');
		// create email body text. @TODO move this to a tpl	
		$body = $gBitUser->getDisplayName()." ".$gBitUser->mInfo['email']." has invited you to join the group ".$gContent->getTitle()." at ".BIT_ROOT_URI;
		$body .= "\n\n".$_REQUEST['email_body']."\n\nTo join this group click the following url:\n".BIT_ROOT_URI."group/join.php?invite=".$inviteCode;
		$body .= "\n\n".tra('Group Description')."\n\n".$gContent->mInfo['group_desc'];
		// format email addresses for Switchboard
		foreach( $valid as $email ){
			$recipients[] = array( 'email' => $email );
		}
		// send email
		$gSwitchboardSystem->sendEmail( $subject, $body, $recipients );
		$msg = tra( 'Invitations sent!' );
		$gBitSmarty->assign_by_ref( 'successMsg', $msg );
	}
}

// @TODO get a list of open invitations

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Invite Members' ) );
$gBitSystem->display( "bitpackage:group/edit_members_invites.tpl" );
?>
