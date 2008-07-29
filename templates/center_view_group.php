<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/templates/center_view_group.php,v 1.2 2008/07/29 18:08:35 lsces Exp $
 * @package bitweaver
 */
global $gBitSmarty, $gBitSystem, $gQueryUserId, $moduleParams, $gBitUser, $gContent;
if( !empty( $moduleParams ) ) {
	extract( $moduleParams );
}

$lookupHash = array();
if( !empty( $moduleParams )) {
	$lookupHash = array_merge( $_REQUEST, $moduleParams['module_params'] );
}

require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

// check permissions to access this page
if( $gContent->isValid() && $gContent->hasViewPermission() ){
	/* this is the exact same as in index.php except $_REQUEST has been changed to $moduleParams
	 * might want to move this into the class
	 */
	$allowedContentTypes = $gContent->mContentTypeData;

	// recent content
	$contentListHash = array(
		"connect_group_content_id" => $gContent->mContentId,
		"exclude_content_type_guid" => "bitboard",
		);
	// if a content_type has been requested the user just wants a list of that
	if ( isset( $moduleParams['content_type_guid'] ) ){
		$contentListHash['content_type_guid'] =  $moduleParams['content_type_guid'];
		if ( isset( $allowedContentTypes[$moduleParams['content_type_guid']] ) ){
			$gBitSmarty->assign( "reqContentType", $allowedContentTypes[$moduleParams['content_type_guid']] );
		}
	}
	$contentList = $gContent->getContentList( $contentListHash );
	$gBitSmarty->assign_by_ref( "contentList", $contentList );

	// topics from related board
	$listHash = array(
		"connect_group_content_id" => $gContent->mContentId,
		"content_type_guid" => "bitboard",
		"sort_mode" => "created_asc"
		);
	$list = $gContent->getContentList( $listHash );
	if ( $listHash['cant'] ){
		$gBitSmarty->assign( 'board_id', $list[0]['board_id'] );
		
		// if a content_type has been requested the user just wants a list of that - no discussion topics
		if ( empty( $moduleParams['content_type_guid'] ) ){
			/*  boards package dependancy
			 *  we're only expecting one board to be associated with the group.
			 *  if more than one is to be allowed then maybe some support for handling 
			 *  that would need to be added here. for now we get only the discussion
			 *  topics of the oldest board, which is automagically created when the group
			 *  is created.
			 */
			require_once( BOARDS_PKG_PATH.'BitBoardTopic.php' );
			$topicsHash = array( 
				"content_id" =>  $list[0]['content_id'],
			);  
			$topic = new BitBoardTopic();
			$topics = $topic->getList( $topicsHash );
			$gBitSmarty->assign_by_ref( 'topics', $topics );
		}
	}
}
?>
