<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/Attic/list_groups.php,v 1.9 2009/01/26 16:50:08 tekimaki_admin Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
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

// Call gmap map view service if map mode requested
if( !empty( $_REQUEST['display_mode'] ) && $_REQUEST['display_mode'] == 'map' ){ 
	$_REQUEST['content_type_guid'] = BITGROUP_CONTENT_TYPE_GUID;
	$pageTitle = tra("Groups");
	include_once( GMAP_PKG_PATH.'map_content_list_inc.php' );
	// end
}

// create new group object
$group = new BitGroup();
$groupsList = $group->getList( $_REQUEST );
$gBitSmarty->assign_by_ref( 'groupsList', $groupsList );

// getList() has now placed all the pagination information in $_REQUEST['listInfo']
$gBitSmarty->assign_by_ref( 'listInfo', $_REQUEST['listInfo'] );

// Display the list template
$gBitSystem->display( 'bitpackage:group/list_groups.tpl', tra( 'Groups' ) , array( 'display_mode' => 'list' ));
