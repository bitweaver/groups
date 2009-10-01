<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/members.php,v 1.11 2009/10/01 13:45:40 wjames5 Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
 * 
 * @package groups
 * @subpackage functions
 */
 
/**
 * Initialization
 */
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
if( $gContent->isValid() ) {
	$gContent->verifyViewPermission();
	if ( !$gContent->hasUserPermission( 'p_group_group_members_view' ) ){
		$gBitSystem->fatalError( tra("You do not have permission to see a list of this group's members") );
	}

	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
}else{
	$gBitSystem->fatalError( tra( 'The Group you requested does not exist' ));
}

// Get all the groups members
// @TODO use pagination
$groupMembers = $gContent->getMembers();
$gBitSmarty->assign_by_ref( 'groupMembers', $groupMembers );

// display
$gBitSmarty->assign( 'group_display_mode', 'members' );
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Members' ) );
$gBitSystem->display( "bitpackage:group/list_members.tpl" , NULL, array( 'display_mode' => 'display' ));
?>
