<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/display_group_portal_inc.php,v 1.2 2008/06/18 13:18:20 lsces Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 * 
 * $Id: display_group_portal_inc.php,v 1.2 2008/06/18 13:18:20 lsces Exp $
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
	$gBitSmarty->assign( 'sort_mode', ( isset($_REQUEST['sort_mode'])?$_REQUEST['sort_mode']:NULL ) );
}
// get a list of most recently created groups
$recentHash = $_REQUEST;
$recentHash['sort_mode'] = "created_desc";
$recentGroupsList = $gContent->getList( $recentHash );
$gBitSmarty->assign('recentGroups', $recentGroupsList);
$gBitSystem->display( 'bitpackage:group/group_home.tpl', tra( 'Groups' ) );

?>
