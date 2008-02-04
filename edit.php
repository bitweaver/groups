<?php
// $Header: /cvsroot/bitweaver/_bit_groups/edit.php,v 1.2 2008/02/04 19:09:10 nickpalmer Exp $
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
	"is_public" => array(
		'label' => 'Open Join',
		'note' => 'Is anyone free to join this group?',
		'default' => 'y',
	),
	"mod_msg" => array(
		'label' => 'Comment Moderation',
		'note' => 'Does the addition of comments require moderation?',
		'default' => 'n',
	),
	"mod_content" => array(
		'label' => 'Content Moderation',
		'note' => 'Does the addition of content require moderation?',
		'default' => 'n',
	),
	"admin_content_strict" => array(
		'label' => 'Admin Edits All',
		'note' => 'Can the administrator edit any content assigned to the group?',
		'default' => 'n',
	),
	"view_content_public" => array(
		'label' => 'Publicly Viewable Content',
		'note' => 'Is content in this group publically viewable?',
		'default' => 'y',
	),
	);
$gBitSmarty->assign('formGroupOptions', $formGroupOptions);

// Display the template
$gBitSystem->display( 'bitpackage:group/edit_group.tpl', tra('Group') );
?>
