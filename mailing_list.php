<?php
// $Header: /cvsroot/bitweaver/_bit_groups/Attic/mailing_list.php,v 1.6 2008/04/09 16:07:37 spiderr Exp $
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
	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
} else {
	$gBitSystem->fatalError( tra( 'The Group you requested does not exist' ));
}

if( !empty( $_REQUEST['create_list'] ) ) {
	//------ Email List ------//
	if( !($error = mailman_newlist( array( 'listname' => $_REQUEST['group_mailing_list'], 'admin-password'=>$_REQUEST['group_mailing_list_password'], 'listadmin-addr'=>$gBitUser->getField( 'email' ) ) )) ) {
		$gContent->storePreference( 'group_mailing_list', !empty( $_REQUEST['group_mailing_list'] ) ? $_REQUEST['group_mailing_list'] : NULL );
		$gContent->storePreference( 'group_mailing_list_password', $_REQUEST['group_mailing_list_password'] );
	} else {
		$gBitSmarty->assign( 'errorMsg', $error );
	}

//		if( $gContent->getPreference( 'group_mailing_list' ) && $_REQUEST['group_mailing_list'] != $gContent->getPreference( 'group_mailing_list' ) ) {
			// Name change
//			groups_mailman_rename( $gContent->getPreference( 'group_mailing_list' ), $_REQUEST['group_mailing_list'] );
//		}

} elseif( !empty( $_REQUEST['delete_list'] ) ) {
	if( $gContent->getPreference( 'group_mailing_list' ) ) {
		if( empty( $_REQUEST['confirm'] ) ) {
			$formHash['delete_list'] = TRUE;
			$formHash['group_id'] = $gContent->mGroupId;
			$gBitSystem->confirmDialog( $formHash, array( 'warning' => 'Are you sure you want to delete the mailing list '.$gContent->getTitle().'?', 'error' => 'This cannot be undone!' ) );
		} else {
			if( !($error = mailman_rmlist( $gContent->getPreference( 'group_mailing_list' ) )) ) {
				$gContent->storePreference( 'group_mailing_list', NULL );
				$gContent->storePreference( 'group_mailing_list_password', NULL );
				header( "Location: ".GROUP_PKG_URL."mailing_list.php?group_id=".$gContent->mGroupId );
			} else {
				$gBitSmarty->assign( 'errorMsg', $error );
			}
		}
	}
} elseif( !empty( $_REQUEST['subscribe'] ) ) {
	if( $gContent->getPreference( 'group_mailing_list' ) ) {
		mailman_addmember( $gContent->getPreference( 'group_mailing_list' ), $gBitUser->getField( 'email' ) );
	}
} elseif( !empty( $_REQUEST['unsubscribe'] ) ) {
	if( $gContent->getPreference( 'group_mailing_list' ) ) {
		mailman_remove_member( $gContent->getPreference( 'group_mailing_list' ), $gBitUser->getField( 'email' ) );
	}
}

if( $gContent->getPreference( 'group_mailing_list' ) ) {
	if ( $gContent->hasUserPermission( 'p_group_group_members_view' ) ){
		$members = mailman_list_members( $gContent->getPreference( 'group_mailing_list' ) );
		$gBitSmarty->assign_by_ref( 'listMembers', $members );
	}
} else {
	$gBitSmarty->assign( 'suggestedListName', preg_replace( '/[^a-z0-9]/', '', strtolower( $gContent->getTitle() ) ) );
}

// display
$gBitSystem->display( "bitpackage:group/mailing_list.tpl", $gContent->getTitle() ." ".  tra( 'Group Mailing List' ) );
?>
