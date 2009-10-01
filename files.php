<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/files.php,v 1.11 2009/10/01 13:45:40 wjames5 Exp $
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

require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must have view permissiont to see file list 
if( $gContent->isValid() ) {
	$gContent->verifyViewPermission();

	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
} else {
	$gBitSystem->fatalError( tra( 'The Group you are trying to upload files to does not exist' ));
}

// If we are saving
if( !empty( $_REQUEST["save_group"] ) ) {
	if( !( $gContent->hasUpdatePermission() || $gContent->hasUserPermission('p_group_group_att_upload') ) ){
		$gBitSystem->fatalError( tra( "You do not have permission to add files to this group" ) );
	}
	
	// make sure we dont set a file as primary by accident since primary is used for the group image/logo
	if( !isset( $_REQUEST['liberty_attachments']['primary'] ) ){
		$_REQUEST['liberty_attachments']['primary'] = 'none';
	}

	$storeHash = array_merge( $_REQUEST, $gContent->mInfo );
	if( !$gContent->store( $storeHash ) ) {
		$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
	}
}

$gBitSmarty->assign('fileList',$gContent->mStorage);
$gBitThemes->loadAjax( 'mochikit', array( 'Iter.js', 'DOM.js' ) );

// since the carpet moved under automatic inclusion of LibertyAttachment.js if ajax is enabled we have to force it here.
if( $gBitSystem->getConfig("liberty_attachment_style") == "ajax" ){
	$gBitThemes->loadJavascript( LIBERTY_PKG_PATH.'scripts/LibertyAttachment.js', TRUE );
	$gBitThemes->loadJavascript( GROUP_PKG_PATH.'scripts/LibertyAttachment.js', TRUE );
}

$gBitSmarty->assign( 'group_display_mode', 'files' );

// Display the template
$gBitSystem->display( 'bitpackage:group/edit_group_files.tpl', tra('Group File') , array( 'display_mode' => 'display' ));
?>
