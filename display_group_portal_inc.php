<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/display_group_portal_inc.php,v 1.1 2008/04/07 20:00:00 wjames5 Exp $
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
