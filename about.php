<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/about.php,v 1.10 2010/02/08 21:27:23 wjames5 Exp $
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
require_once( '../kernel/setup_inc.php' );

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

$gBitSmarty->assign( 'group_display_mode', 'about' );

// Display the template
$gBitSystem->display( 'bitpackage:group/group_about.tpl', tra( 'Group' ) , array( 'display_mode' => 'display' ));
?>
