<?php
/**
 * @version $Header:
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
global $gContent;
require_once( GROUP_PKG_PATH.'BitGroup.php');
require_once( LIBERTY_PKG_PATH.'lookup_content_inc.php' );

// this is needed when the center module is applied to avoid abusing $_REQUEST
if( empty( $lookupHash )) {
	$lookupHash = &$_REQUEST;
}

// if we already have a gContent, we assume someone else created it for us, and has properly loaded everything up.
if( empty( $gContent ) || !is_object( $gContent ) || !$gContent->isValid() ) {
	// if group_id supplied, use that
	if( @BitBase::verifyId( $lookupHash['group_id'] ) ) {
		$gContent = new BitGroup( $lookupHash['group_id'] );

	// if content_id supplied, use that
	} elseif( @BitBase::verifyId( $lookupHash['content_id'] ) ) {
		$gContent = new BitGroup( NULL, $lookupHash['content_id'] );

	} elseif (@BitBase::verifyId( $lookupHash['group']['group_id'] ) ) {
		$gContent = new BitGroup( $lookupHash['group']['group_id'] );

	// otherwise create new object
	} else {
		$gContent = new BitGroup();
	}

	$gContent->load();
	$gBitSmarty->assign_by_ref( "gContent", $gContent );
}
?>
