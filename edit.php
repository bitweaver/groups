<?php
// $Header: /cvsroot/bitweaver/_bit_groups/edit.php,v 1.4 2008/02/07 02:14:40 wjames5 Exp $
// Copyright (c) 2004 bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// Now check permissions to access this page
$gBitSystem->verifyPermission('p_group_edit' );

require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

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

// Pro
// Check if the page has changed
if( !empty( $_REQUEST["save_group"] ) ) {

	// Check if all Request values are delivered, and if not, set them
	// to avoid error messages. This can happen if some features are
	// disabled
	if( $gContent->store( $_REQUEST['group'] ) ) {
		header( "Location: ".$gContent->getDisplayUrl() );
		die;
	} else {
		$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
	}
}

// What options do groups support?
$formGroupOptions = array(
	"view_content_public" => array(
		'label' => 'Publicly Viewable',
		'note' => 'Is this group and its content publically viewable?',
		'default' => 'y',
	),
	"is_public" => array(
		'label' => 'Public Membership',
		'note' => 'Is anyone free to join this group? Unchecking this box means an invitation will be required to join this group',
		'default' => 'y',
	),
	"mod_msg" => array(
		'label' => 'Moderate Messages',
		'note' => 'When checked all messages to this group will be held for moderation before being displayed.',
		'default' => 'n',
	),
	"mod_content" => array(
		'label' => 'Moderate Content',
		'note' => 'When checked any content created (like pages or blog posts) wiil be held for moderation before being displayed',
		'default' => 'n',
	),
	"admin_content_strict" => array(
		'label' => 'Admin Edits All',
		'note' => 'Can the administrator edit any content assigned to the group?',
		'default' => 'n',
	),
);
$gBitSmarty->assign('formGroupOptions', $formGroupOptions);

$formGroupRolesOptions = array(
	"stuff" => array(
		'label' => 'Set stuff',
		'note' => 'When you set stuff it does what you want',
	),
);
$gBitSmarty->assign('formGroupRolesOptions', $formGroupRolesOptions);

// Get all rolls - used in access control options
$groupRoles = $gContent->getRoles();
$gBitSmarty->assign('groupRoles', $groupRoles );

// Get all perms - used in access control options
$allRolesPerms = $gContent->getRolesPerms();
$gBitSmarty->assign('allRolesPerms', $allRolesPerms );

// Display the template
$gBitSystem->display( 'bitpackage:group/edit_group.tpl', tra('Group') );
?>
