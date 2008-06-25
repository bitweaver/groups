<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/display_group_inc.php,v 1.3 2008/06/25 22:21:10 spiderr Exp $
 * @package groups
 * @subpackage functions
 */

/**
 * Initialization
 */
$gBitSystem->verifyPackage( 'group' );
require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
if( !$gContent->isValid() ) {
	$gBitSystem->setHttpStatus( 404 );
	$gBitSystem->fatalError( "The group you requested could not be found." );
}
$gContent->verifyViewPermission();

// if it has a custom theme lets theme it
$gContent->setGroupStyle();

$allowedContentTypes = $gContent->mContentTypeData;

// recent content
$contentListHash = array(
	"connect_group_content_id" => $gContent->mContentId,
	"exclude_content_type_guid" => "bitboard",
	);
// if a content_type has been requested the user just wants a list of that
if ( isset( $_REQUEST['content_type_guid'] ) ){
	$contentListHash['content_type_guid'] =  $_REQUEST['content_type_guid'];
	if ( isset( $allowedContentTypes[$_REQUEST['content_type_guid']] ) ){
		$gBitSmarty->assign( "reqContentType", $allowedContentTypes[$_REQUEST['content_type_guid']] );
	}
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

// if a content type is requested we just want that content for the group, otherwise display the main group template
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
		if ( isset( $_REQUEST['item_id'] ) && ( $_REQUEST['item_id'] != $gContent->mContentId ) ){
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

		if ( isset( $_REQUEST['item_id'] ) && ( $_REQUEST['item_id'] != $gContent->mContentId ) ){
			$centerModuleParams['content_id'] = $_REQUEST['item_id'];
		}

		$centerModuleParams['module_params'] = $centerModuleParams;

		if ( !is_array($gCenterPieces) ){
			$gCenterPieces = array();
		}

		array_push( $gCenterPieces, $centerModuleParams );

		// get the menu for this content type and add it to our group menu
		if ( isset( $gBitSystem->mAppMenu[$package] ) ){
			$contentTypeEditUrl = $class->getEditUrl( NULL, array( "connect_group_content_id"=>$gContent->mContentId ) );
			$gBitSmarty->assign( 'contentTypeEditUrl', $contentTypeEditUrl );
		}

		$pageTitle = tra('List Group '.$contentDesc.'s');

	}
}else{
	// Display the template
	$gDefaultCenter = 'bitpackage:group/group_display.tpl';
	$gBitSmarty->assign_by_ref( 'gDefaultCenter', $gDefaultCenter );
	$pageTitle = tra("Group");
}

$gBitSystem->display( 'bitpackage:kernel/dynamic.tpl', $pageTitle , array( 'display_mode' => 'display' ));
?>
