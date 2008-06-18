<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/about.php,v 1.5 2008/06/18 13:18:20 lsces Exp $
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

require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

// Now check permissions to access this page
if( $gContent->isValid() ) {
	$gContent->verifyViewPermission();

	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
} else {
	$gBitSystem->fatalError( tra( 'The Group you requested does not exist' ));
}

if( ($board = $gContent->getBoard()) && ($mailingList = $board->getBoardMailingList()) ) {
	$gBitSmarty->assign( 'boardsMailingList', $mailingList );
}

// Display the template
$gBitSystem->display( 'bitpackage:group/group_about.tpl', tra( 'Group' ) );
?>
