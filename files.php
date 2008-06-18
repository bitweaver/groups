<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/files.php,v 1.4 2008/06/18 13:18:20 lsces Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
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
	if( !( $gContent->hasEditPermission() || $gContent->hasUserPermission('p_group_group_att_upload') ) ){
		$gBitSystem->fatalError( tra( "You do not have permission to add files to this group" ) );
	}
	
	$storeHash = array_merge( $_REQUEST, $gContent->mInfo );
	if( !$gContent->store( $storeHash ) ) {
		$gBitSmarty->assign_by_ref( 'errors', $gContent->mErrors );
	}
}

$gBitSmarty->assign('fileList',$gContent->mStorage);

// Display the template
$gBitSystem->display( 'bitpackage:group/edit_group_files.tpl', tra('Group File') );
?>
