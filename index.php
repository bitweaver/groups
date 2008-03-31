<?php
// $Header: /cvsroot/bitweaver/_bit_groups/index.php,v 1.19 2008/03/31 02:11:46 wjames5 Exp $
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
	if ( isset( $_REQUEST['content_type_guid'] ) ){
		$contentListHash['content_type_guid'] =  $_REQUEST['content_type_guid'];
		$gBitSmarty->assign( "reqContentType", $allowedContentTypes[$_REQUEST['content_type_guid']] );
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
		if ( empty( $_REQUEST['content_type_guid'] ) ){
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
	if ( isset ( $_REQUEST['content_type_guid'] ) && isset( $gLibertySystem->mContentTypes[$_REQUEST['content_type_guid']] ) ){
		global $gCenterPieces;

		$contentType = $_REQUEST['content_type_guid'];
		$contentTypeHash = $gLibertySystem->mContentTypes[$contentType];
		$class =  $contentTypeHash['handler_class'];
		$classFile =  $contentTypeHash['handler_file'];
		$package = $contentTypeHash['handler_package'];
		$contentDesc = $contentTypeHash['content_description'];
		$gBitSmarty->assign( 'contentTypeDesc', $contentDesc );

		$pathVar = strtoupper($package).'_PKG_PATH'; 
		if( defined( $pathVar ) ) { 
			
			$_REQUEST['connect_group_content_id'] = $gContent->mContentId; 

			require_once( constant( $pathVar ).$classFile );
		
			$class = new $class(); 
			if ( isset( $_REQUEST['content_id'] ) && ( $_REQUEST['content_id'] != $gContent->mContentId ) ){
				$tpl = $class->getViewTemplate( 'view' );
			}else{
				$tpl = $class->getViewTemplate( 'list' );
			}

			$centerModuleParams = array( 
				"layout_area" =>  "c",
				"module_rows" =>  10,
				"module_rsrc" =>  $tpl,
				"params" => "",
				"cache_time" => 0,
				"groups" => null,
				"pos" => 1,
				"visible" => TRUE,
				"content_type_guid" => $contentType,
				"module_params" => array(),
			);

			if ( isset( $_REQUEST['content_id'] ) && ( $_REQUEST['content_id'] != $gContent->mContentId ) ){
				$centerModuleParams = $_REQUEST['content_id'];
			}

			if ( !is_array($gCenterPieces) ){
				$gCenterPieces = array();
			}

			array_push( $gCenterPieces, $centerModuleParams );

			// get the menu for this content type and add it to our group menu
			if ( isset( $gBitSystem->mAppMenu[$package] ) ){
				$contentTypeEditUrl = $class->getEditUrl( NULL, array( "connect_group_content_id"=>$gContent->mContentId ) );
				$gBitSmarty->assign( 'contentTypeEditUrl', $contentTypeEditUrl );
			}

			$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', tra('List Group '.$contentDesc.'s') );
	   	}
	}else{
		$gBitSystem->display( 'bitpackage:group/group_display.tpl', tra( 'Group' ) );
	}
}
?>
