<?php
// $Header: /cvsroot/bitweaver/_bit_groups/index.php,v 1.10 2008/03/03 21:27:54 wjames5 Exp $
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
// hackish but needed to hide group menu if perm check above fails
$gBitSmarty->assign( 'viewable', true );

if( !isset( $_REQUEST['group_id'] ) || !$gContent->isValid() ) {
	// if no group is requested, if no default is set, or the group requested is not valid we deliver a splash page about groups
	// get a list of groups the user is a member of
	if ( $gBitUser->isRegistered() ){
		$listHash = $_REQUEST;
		$listHash['user_id'] = $gBitUser->mUserId;
		$groupList = $gContent->getList( $listHash );
		$gBitSmarty->assign('groups', $groupList);
		$gBitSmarty->assign( 'sort_mode', ( isset($_REQUEST['sort_mode'])?$_REQUEST['sort_mode']:NULL ) );
	}
	$gBitSystem->display( 'bitpackage:group/group_home.tpl', tra( 'Groups' ) );
}else{
	// load up the attached board
	$listHash = array(
		"connect_group_content_id" => $gContent->mContentId,
		"content_type_guid" => "bitboard"
		);
	$list = $gContent->getContentList( $listHash );
	if ( $list['cant'] ){
		// we're only expecting one. if support for more than one is added this needs to change to display a list in the case of many
		// board package dependancy
		require_once( BOARDS_PKG_PATH.'BitBoard.php' );
		$board = new BitBoard( NULL, $list['data'][0]['content_id'] );
		$board->load();
		$gBitSmarty->assign_by_ref( 'board', $board );

		$commentsParentId=$board->mContentId;
		$comments_return_url=  BOARDS_PKG_URL."index.php?b=".urlencode($board->mBitBoardId);

		// require_once (LIBERTY_PKG_PATH.'comments_inc.php');

		$threads = new BitBoardTopic();
		$threadsListHash = array( "b" => $board->mBitBoardId );
		$threadList = $threads->getList( $threadsListHash );
		$gBitSmarty->assign_by_ref( 'threadList', $threadList );
		$gBitSmarty->assign_by_ref( 'listInfo', $_REQUEST['listInfo'] );

		$boardBlock = $gBitSmarty->fetch( 'bitpackage:boards/list_topics.tpl' );
		$gBitSmarty->assign_by_ref( 'boardBlock', $boardBlock);
		$gBitSmarty->assign_by_ref( 'gContent', $gContent );
	}

	$gContent->addHit();
	// Display the template
	$gBitSystem->display( 'bitpackage:group/group_display.tpl', tra( 'Group' ) );
}
?>
