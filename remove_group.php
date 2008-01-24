<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_groups/remove_group.php,v 1.1 2008/01/24 14:47:56 wjames5 Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: remove_group.php,v 1.1 2008/01/24 14:47:56 wjames5 Exp $
 * @package group
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );
include_once( GROUPS_PKG_PATH.'BitGroup.php');
include_once( GROUPS_PKG_PATH.'lookup_group_inc.php' );

$gBitSystem->verifyPackage( 'groups' );

if( !$gContent->isValid() ) {
	$gBitSystem->fatalError( "No group indicated" );
}

$gBitSystem->verifyPermission( 'p_group_remove' );

if( isset( $_REQUEST["confirm"] ) ) {
	if( $gContent->expunge()  ) {
		header ("location: ".BIT_ROOT_URL );
		die;
	} else {
		vd( $gContent->mErrors );
	}
}

$gBitSystem->setBrowserTitle( tra( 'Confirm delete of: ' ).$gContent->getTitle() );
$formHash['remove'] = TRUE;
$formHash['group_id'] = $_REQUEST['group_id'];
$msgHash = array(
	'label' => tra( 'Delete Group' ),
	'confirm_item' => $gContent->getTitle(),
	'warning' => tra( 'This group will be completely deleted.<br />This cannot be undone!' ),
);
$gBitSystem->confirmDialog( $formHash,$msgHash );

?>
