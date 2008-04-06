<?php
// $Header: /cvsroot/bitweaver/_bit_groups/Attic/mailing_list.php,v 1.1 2008/04/06 22:38:50 spiderr Exp $
// Copyright (c) bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );
require_once(GROUP_PKG_PATH.'mailman_lib.php' );

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

if( !$gContent->getPreference( 'group_mailing_list' ) ) {
	$gBitSmarty->assign( 'suggestedListName', preg_replace( '/[^a-z0-9]/', '', strtolower( $gContent->getTitle() ) ) );
}
if( !empty( $_REQUEST['create_list'] ) ) {
	//------ Email List ------//
	if( !($error = mailman_verify_list( $_REQUEST['group_mailing_list'] )) ) {
		if( mailman_newlist( $_REQUEST['group_mailing_list'] ) ) {
			$gContent->storePreference( 'group_mailing_list', !empty( $_REQUEST['group_mailing_list'] ) ? $_REQUEST['group_mailing_list'] : NULL );
		}
	} else {
		$gBitSmarty->assign( 'errorMsg', $error );
	}

//		if( $gContent->getPreference( 'group_mailing_list' ) && $_REQUEST['group_mailing_list'] != $gContent->getPreference( 'group_mailing_list' ) ) {
			// Name change
//			groups_mailman_rename( $gContent->getPreference( 'group_mailing_list' ), $_REQUEST['group_mailing_list'] );
//		}

} elseif( !empty( $_REQUEST['delete_list'] ) ) {
	if( $gContent->getPreference( 'group_mailing_list' ) ) {
		groups_mailman_command( 'rmlist', $gContent->getPreference( 'group_mailing_list' ) );
	}
}

// Get all the groups members
// @TODO use pagination
$groupMembers = $gContent->getMembers();
$gBitSmarty->assign_by_ref( 'groupMembers', $groupMembers );

// display
$gBitSystem->setBrowserTitle( $gContent->getTitle() ." ".  tra( 'Group Members' ) );
$gBitSystem->display( "bitpackage:group/mailing_list.tpl" );
?>
