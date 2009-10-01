<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/display_group_portal_inc.php,v 1.6 2009/10/01 14:17:00 wjames5 Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * 
 * $Id: display_group_portal_inc.php,v 1.6 2009/10/01 14:17:00 wjames5 Exp $
 * @package groups
 * @subpackage functions
 */

/**
 * if no group is requested, if no default is set, 
 * or the group requested is not valid we deliver a 
 * splash page about groups
 */

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
$gBitSystem->verifyPermission( 'p_group_view' );

// get a list of groups the user is a member of
if ( $gBitUser->isRegistered() ){
	$memberHash = $_REQUEST;
	$memberHash['user_id'] = $gBitUser->mUserId;
	$memberGroupsList = $gContent->getList( $memberHash );
	$gBitSmarty->assign('memberGroups', $memberGroupsList);

	// add information necessary for pagination
	$memberHash['listInfo']['parameters']['list'] = 'mygroups';
	$gBitSmarty->assign_by_ref( "memberListInfo", $memberHash['listInfo'] );

	$gBitSmarty->assign( 'sort_mode', ( isset($_REQUEST['sort_mode'])?$_REQUEST['sort_mode']:NULL ) );
}

// if request for paginated mygroups list then we dont bother with listing new groups
if ( empty( $_REQUEST['list'] ) || $_REQUEST['list'] != 'mygroups' ){
	// get a list of most recently created groups
	$recentHash = $_REQUEST;
	$recentHash['sort_mode'] = "created_desc";
	$recentGroupsList = $gContent->getList( $recentHash );
	$gBitSmarty->assign('recentGroups', $recentGroupsList);

	// add information necessary for pagination
	$gBitSmarty->assign_by_ref( "recentListInfo", $recentHash['listInfo'] );
}

$gBitSystem->display( 'bitpackage:group/group_home.tpl', tra( 'Groups' ) , array( 'display_mode' => 'display' ));

?>
