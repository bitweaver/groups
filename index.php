<?php
// $Header: /cvsroot/bitweaver/_bit_groups/index.php,v 1.14 2008/03/24 19:15:03 wjames5 Exp $
// Copyright (c) 2008 bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

//if the request is from the search field redirect us to the list - otherwise do what we do
if ( isset( $_REQUEST['find'] ) ){
	header ("location: ".GROUP_PKG_URI."list_groups.php?find=".$_REQUEST['find'] );
	die;
}

if( !isset( $_REQUEST['group_id'] ) ) {
	// if a default group has been set we'll use that
	$_REQUEST['group_id'] = $gBitSystem->getConfig( "home_group" );
}

require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
if( $gContent->isValid() ) {
	$gContent->verifyViewPermission();
} else {
	$gBitSystem->verifyPermission( 'p_group_view' );
}

if( !isset( $_REQUEST['group_id'] ) || !$gContent->isValid() ) {
	// if no group is requested, if no default is set, or the group requested is not valid we deliver a splash page about groups
	// get a list of groups the user is a member of
	if ( $gBitUser->isRegistered() ){
		$memberHash = $_REQUEST;
		$memberHash['user_id'] = $gBitUser->mUserId;
		$memberGroupsList = $gContent->getList( $memberHash );
		$gBitSmarty->assign('memberGroups', $memberGroupsList);
		$gBitSmarty->assign( 'sort_mode', ( isset($_REQUEST['sort_mode'])?$_REQUEST['sort_mode']:NULL ) );
	}
	// get a list of most recently created groups
	$recentHash = $_REQUEST;
	$recentHash['sort_mode'] = "created_desc";
	$recentGroupsList = $gContent->getList( $recentHash );
	$gBitSmarty->assign('recentGroups', $recentGroupsList);
	$gBitSystem->display( 'bitpackage:group/group_home.tpl', tra( 'Groups' ) );
}else{
	// we have a valid group - lets get its associated content
	// recent content
	$contentListHash = array(
		"connect_group_content_id" => $gContent->mContentId,
		"exclude_content_type_guid" => "bitboard",
		);
	$contentList = $gContent->getContentList( $contentListHash );
	$gBitSmarty->assign_by_ref( "contentList", $contentList['data'] );

	// topics from related board
	$listHash = array(
		"connect_group_content_id" => $gContent->mContentId,
		"content_type_guid" => "bitboard",
		"sort_mode" => "created_asc"
		);
	$list = $gContent->getContentList( $listHash );
	if ( $list['cant'] ){
		/*  boards package dependancy
		 *  we're only expecting one board to be associated with the group.
		 *  if more than one is to be allowed then maybe some support for handling 
		 *  that would need to be added here. for now we get only the discussion
		 *  topics of the oldest board, which is automagically created when the group
		 *  is created.
		 */
		require_once( BOARDS_PKG_PATH.'BitBoardTopic.php' );
		$topicsHash = array( 
			"content_id" =>  $list['data'][0]['content_id'],
	   	);  
		$topic = new BitBoardTopic();
		$topics = $topic->getList( $topicsHash );
		$gBitSmarty->assign_by_ref( 'topics', $topics );
		$gBitSmarty->assign( 'board_id', $list['data'][0]['board_id'] );
	}

	$gContent->addHit();
	// Display the template
	$gBitSystem->display( 'bitpackage:group/group_display.tpl', tra( 'Group' ) );
}
?>
