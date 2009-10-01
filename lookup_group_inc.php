<?php
/**
 * @version $Header:
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.
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
	// if someone gives us a group_name we try to find it
	if( !empty( $lookupHash['group_name'] ) ){
		global $gBitDb;
		$lookupHash['group_id'] = $gBitDb->getOne( "SELECT group_id FROM `".BIT_DB_PREFIX."groups` g LEFT JOIN `".BIT_DB_PREFIX."liberty_content` lc ON (g.`content_id` = lc.`content_id`) WHERE lc.`title` = ?", array($lookupHash['group_name']) );
		if( empty( $lookupHash['group_id'] ) ) {
		  $gBitSystem->fatalError(tra('No group found with the name: ').$lookupHash['group_name']);
		}
	}

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
	
	// ControlGroupInfo is for customizing layouts in other parts of the site (see BitGroup::group_content_display) 
	// but we need it here too if we want to apply layout features consistently while browsing groups/ too and keep our tpls simple
	$gBitSmarty->assign_by_ref( "controlGroupInfo", $gContent->mInfo );
}
?>
