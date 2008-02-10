<?php
// $Header: /cvsroot/bitweaver/_bit_groups/edit.php,v 1.8 2008/02/10 17:24:14 wjames5 Exp $
// Copyright (c) 2004 bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
$gBitSystem->verifyPermission('p_group_edit' );

if( isset( $_REQUEST['group']["title"] ) ) {
	$gContent->mInfo["title"] = $_REQUEST['group']["title"];
}

if( isset( $_REQUEST['group']["description"] ) ) {
	$gContent->mInfo["description"] = $_REQUEST['group']["description"];
}

if( isset( $_REQUEST["format_guid"] ) ) {
	$gContent->mInfo['format_guid'] = $_REQUEST["format_guid"];
}

if( isset( $_REQUEST['group']["edit"] ) ) {
	$gContent->mInfo["data"] = $_REQUEST['group']["edit"];
	$gContent->mInfo['parsed_data'] = $gContent->parseData();
}

// If we are in preview mode then preview it!
if( isset( $_REQUEST["preview"] ) ) {
	$gBitSmarty->assign('preview', 'y');
	$gContent->invokeServices('content_preview_function');
}
else {
  	$gContent->invokeServices( 'content_edit_function' );
}

// Get all rolls - used in access control options
$groupRoles = $gContent->getRoles();
$gBitSmarty->assign('groupRoles', $groupRoles );

// Get all perms - used in access control options
$allRolesPerms = $gContent->getRolesPerms();
$gBitSmarty->assign('allRolesPerms', $allRolesPerms );

// Pro
// Check if the page has changed
if( !empty( $_REQUEST["save_group"] ) ) {

	// Check if all Request values are delivered, and if not, set them
	// to avoid error messages. This can happen if some features are
	// disabled
	if( $gContent->store( $_REQUEST['group'] ) ) {

		// if that went ok store role permissions for the group
		foreach( array_keys( $groupRoles ) as $roleId ) {
			// don't store role_id 1 which is reserved for admin. no point in storing admin perms.
			if ( $roleId != 1 ){
				foreach( array_keys( $allRolesPerms ) as $perm ) {
					if( !empty( $_REQUEST['perms'][$roleId][$perm] )) {
						$gContent->assignPermissionToRole( $perm, $roleId, $gContent->mContentId );
					} else {
						$gContent->removePermissionFromRole( $perm, $roleId, $gContent->mContentId );
					}
				}
			}
		}

		header( "Location: ".$gContent->getDisplayUrl() );
		die;
	} else {
		$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
	}
}

// get options hash
require_once(GROUP_PKG_PATH.'options_inc.php'); 

// Display the template
$gBitSystem->display( 'bitpackage:group/edit_group.tpl', tra('Group') );
?>
