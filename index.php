<?php
// $Header: /cvsroot/bitweaver/_bit_groups/index.php,v 1.16 2008/03/25 19:49:34 wjames5 Exp $
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
	// get a list of content types this group allows
	$contentTypeGuids = $gContent->getContentTypePrefs();
	$allowedContentTypes = array();
	foreach( $gLibertySystem->mContentTypes as $cType ) {
		if( in_array( $cType['content_type_guid'], $contentTypeGuids ) ) {
			$allowedContentTypes[$cType['content_type_guid']]  = $cType['content_description'];
		}
	}
	$gBitSmarty->assign( 'allowedContentTypes', $allowedContentTypes );

	// recent content
	$contentListHash = array(
		"connect_group_content_id" => $gContent->mContentId,
		"exclude_content_type_guid" => "bitboard",
		);
	// if a content_type has been requested the user just wants a list of that
	if ( isset( $_REQUEST['content_type'] ) ){
		$contentListHash['content_type_guid'] =  $_REQUEST['content_type'];
		$gBitSmarty->assign( "reqContentType", $allowedContentTypes[$_REQUEST['content_type']] );
	}
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
		$gBitSmarty->assign( 'board_id', $list['data'][0]['board_id'] );
		
		// if a content_type has been requested the user just wants a list of that - no discussion topics
		if ( empty( $_REQUEST['content_type'] ) ){
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
		}
	}

	$gContent->addHit();
	// Display the template
	// @TODO probably want to use a center display so that we can force use of something like the blog posts roll if just that associated content type is requested
	if ( isset ( $_REQUEST['content_type'] ) && isset( $gLibertySystem->mContentTypes[$_REQUEST['content_type']] ) ){
		global $gCenterPieces;

		$contentType = $_REQUEST['content_type'];
		$package = $gLibertySystem->mContentTypes[$contentType]['handler_package'];
		$contentDesc = $gLibertySystem->mContentTypes[$contentType]['content_description'];

		/* this is how we might do things if we followed a naming convention
		 * more likely we'll ask for the class for appropriate tpl info
		 */
		/*
		$gDefaultCenter = 'bitpackage:'.$package.'/center_list_'. $contentType.'.tpl';
		$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );
		*/

		/* this is a temp demonstration hardcoded in. when we get the appropriate data
		 * from the class file we'll inject it into a hash something like this -wjames5
		 */
		$centerModuleParams = array( 
			"layout_area" =>  "c",
			"module_rows" =>  10,
			"module_rsrc" =>  "bitpackage:blogs/center_list_blog_posts.tpl",
			"params" => "",
			"cache_time" => 0,
			"groups" => null,
			"pos" => 1,
			"visible" => TRUE,
		);

		if ( !is_array($gCenterPieces) ){
			$gCenterPieces = array();
		}

		array_push( $gCenterPieces, $centerModuleParams );

		$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', tra('List Group '.$contentDesc.'s') );
	}else{
		$gBitSystem->display( 'bitpackage:group/group_display.tpl', tra( 'Group' ) );
	}
}
?>
