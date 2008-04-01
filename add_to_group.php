<?php

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// verify the user can add stuff to a group
if ( !$gBitUser->isRegistered() ){
	$gBitSystem->fatalPermission( NULL, 'Sorry, but you must be registered and belong to any group to submit content to it.' );
}

// verify someone has bothered to submit something
if ( empty( $_REQUEST['submit_content_id'] ) ){
	$gBitSystem->fatalError( "You have not specified anything to submit to a group." );
}
// verify the thing to be added is valid
$linkContent = LibertyBase::getLibertyObject( $_REQUEST['submit_content_id'] );
$linkContent->load();
if ( !$linkContent->isValid() ){
	$gBitSystem->fatalError( "The content you are trying to submit to a group is invalid." );
}

require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
if( $gContent->isValid() ) {
	$gContent->verifyViewPermission();

	// check permission, and content types allowed - also checks validity of group - but lets ignore that and offer the user some options if they don't request a group
	$gContent->verifyLinkContentPermission( $linkContent );
} else {
	$gBitSystem->verifyPermission( 'p_group_view' );
}

// if the content type submitted is not allowed in the group then reject it

// if we dont have a valid group to map the content to then lets offer some options
if( !$gContent->isValid() ) {
	// if no group is requested, if no default is set, or the group requested is not valid we deliver a list of groups the user belongs to
	$memberHash = $_REQUEST;
	$memberHash['user_id'] = $gBitUser->mUserId;
	$memberGroupsList = $gContent->getList( $memberHash );
	if ( !empty( $memberGroupsList ) ){
		$gBitSmarty->assign('memberGroups', $memberGroupsList);
		$gBitSmarty->assign( 'sort_mode', ( isset($_REQUEST['sort_mode'])?$_REQUEST['sort_mode']:NULL ) );
		$gBitSmarty->assign('submit_content_id', $_REQUEST['submit_content_id'] );
	}else{
	// if the user does not belong to any groups lets offer some they can join
		// get a list of most recently created groups
		$recentHash = $_REQUEST;
		$recentHash['sort_mode'] = "created_desc";
		$recentGroupsList = $gContent->getList( $recentHash );
		$gBitSmarty->assign('recentGroups', $recentGroupsList);
	}
}else{
	// if group is open to content submissions then do it
	if ( $gContent->mInfo['mod_content']!="y" ){
		if ( $gContent->linkContent( array( 'content_id' => $linkContent->mContentId, 'title' => $linkContent->getTitle() ) ) ){
			header( "Location: ".$gContent->getDisplayUrl()."&content_type_guid=".$linkContent->mContentTypeGuid."&item_id=".$linkContent->mContentId );
			die;
		}
	} else if( $gBitSystem->isPackageActive('moderation') ){
		// otherwise send the request to moderation
		// @TODO Don't think moderation can handle two content id situations, need feed back from Nick - wjames5
		// $gModerationSystem->requestModeration('group', 'add_content', NULL, $gContent->mGroupId, $gContent->mContentId);
		vd( 'Need way to moderate submissions!' );
		// @TODO display some page letting user know their submisssion is awaiting moderation
	} 
}
$gBitSystem->display( 'bitpackage:group/group_add_content.tpl', tra( 'Groups' ) );
?>
