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
// verify edit permission of the content to be submitted to the group 
if ( !$linkContent->hasEditPermission() ){
	$gBitSystem->fatalError( "You do not have sufficient permission to submit this content to a group." );
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

// get a list of groups the content is already in - we'll need it one way or another
$memberGroupsHash['mapped_content_id'] = $linkContent->mContentId;
$memberGroups = $gContent->getList( $memberGroupsHash );

// some checks if its already been mapped before 
if ( !empty( $memberGroups ) ){
	// verify the submitted content is not already in the group submitted to - check this after permission check to make sure user can know things about the content
	if ( $gContent->isValid() ){
		foreach( $memberGroups as $memberGroup ){
			if ( $memberGroup['group_id'] == $gContent->mGroupId ){
				//@ TODO maybe we should redirect to the page as within the group instead of fataling 
				$gBitSystem->fatalError( "This content has already be added to this group." );
			}
		}
	}
	// if its not in the requested group but groups can admin content and its in one group already then die -  we only allow one mapping per content when groups can admin
	if( $gBitSystem->isFeatureActive('group_admin_content')) {
		$gBitSystem->fatalError( "Sorry, this website only allows content to be mapped to one group, and this content is already in another group." );
	}
}

// ok - we've made it though all the various checks - lets get it on.
// if we dont have a valid group to map the content to then lets offer some options
if( !$gContent->isValid() ) {
	// if no group is requested, if no default is set, or the group requested is not valid we deliver a list of groups the user belongs to
	$userGroupsHash = $_REQUEST;
	$userGroupsHash['user_id'] = $gBitUser->mUserId;
	$userGroupsList = $gContent->getList( $userGroupsHash );
	if ( !empty( $userGroupsList ) ){
		// limit this list to content group is not linked too already.
		$mG = $uG = array();
		foreach( $memberGroups as $data1 ){
			$mG[$data1['group_id']] = $data1;
		}
		foreach( $userGroupsList as $data2 ){
			$uG[$data2['group_id']] = $data2;
		}
		$nonmemberGroups = array_diff_key( $uG, $mG );
		$gBitSmarty->assign('nonmemberGroups', $nonmemberGroups);
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
		// @TODO if site allows groups to admin content then give a confirmation warning.
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
