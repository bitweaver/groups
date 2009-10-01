<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/search.php,v 1.5 2009/10/01 13:45:41 wjames5 Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * @author Will James, Tekimaki LCC <will@tekimaki.com>
 * 
 * @package groups
 * @subpackage functions
 */
 
/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );
require_once( GROUP_PKG_PATH.'BitGroup.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_group_view' );

// A request to mass delete multiple groups may be made through the list interface - handle it
require_once( GROUP_PKG_PATH.'expunge_group_inc.php' );

// A group may have been requested to just search its related content - try and look it up
require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );
if( $gContent->isValid() ){
	// service relies on a unique param name as content_id or group_id would be too generic
	 $_REQUEST['search_group_content_id'] = $gContent->mContentId; 
}

// Call gmap map view service if map mode requested
if( !empty( $_REQUEST['display_mode'] ) && $_REQUEST['display_mode'] == 'map' ){ 
	if( !empty(  $_REQUEST['search_group_content_id'] ) ){
		if( $groupTitle = @BitGroup::getTitle( NULL, $_REQUEST['search_group_content_id'] ) ){
			$pageTitle = tra("Search Group").": ".$groupTitle;
		}else{
			$gBitSystem->fatalError( "The group you requested to search could not be found." );
		}
	}else{
		$_REQUEST['content_type_guid'] = BITGROUP_CONTENT_TYPE_GUID;
		$pageTitle = tra("Groups");
	}

	if( empty( $_REQUEST['content_type_guid'] ) ){
		// maps needs a signal to get a default list
		$_REQUEST['content_type_guid'] = 'Any';
		// force comments inclusion since it would be stupid not to include comments (discussions)
		$_REQUEST['include_comments'] = 'y';
	}
	include_once( GMAP_PKG_PATH.'map_content_list_inc.php' );
	// end
}
// If we have a search_group_content_id then we're searching for content in that group, not groups
elseif( !empty( $_REQUEST['search_group_content_id'] ) ){
	// override group content type param if coupled with a search_group_content_id
	if ( !empty($_REQUEST['content_type_guid']) && ( $_REQUEST['content_type_guid'] == 'bitgroup' ) ){
		$_REQUEST['content_type_guid'] = NULL;
	}
	if( empty( $_REQUEST['content_type_guid'] ) ){
		// force comments inclusion since it would be stupid not to include comments (discussions)
		$_REQUEST['include_comments'] = 'y';
	}
	// we use the group list.tpl not liberty's so just give us the results
	$_REQUEST['output'] = 'raw';
	include_once( LIBERTY_PKG_PATH.'list_content.php' );
	$gBitSmarty->assign_by_ref('contentList', $contentList);
}
// We just want to search for groups / get a list of groups
else{
	$group = new BitGroup();
	$groupsList = $group->getList( $_REQUEST );
	$gBitSmarty->assign_by_ref( 'contentList', $groupsList );
	// getList() has now placed all the pagination information in $_REQUEST['listInfo']
	$gBitSmarty->assign_by_ref( 'listInfo', $_REQUEST['listInfo'] );
}

// Display the list template
$gBitSystem->display( 'bitpackage:group/search_groups.tpl', tra( 'Groups' ) , array( 'display_mode' => 'list' ));
