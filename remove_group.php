<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_groups/remove_group.php,v 1.3 2008/02/14 23:40:24 wjames5 Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: remove_group.php,v 1.3 2008/02/14 23:40:24 wjames5 Exp $
 * @package group
 * @subpackage functions
 */

/**
 * required setup
 */
require_once( '../bit_setup_inc.php' );
include_once( GROUP_PKG_PATH.'BitGroup.php');
include_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

$gBitSystem->verifyPackage( 'group' );

if( !$gContent->isValid() ) {
	$gBitSystem->fatalError( "No group indicated" );
}

$gContent->hasUserPermission( 'p_group_admin' );

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
