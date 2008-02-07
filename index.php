<?php
// $Header: /cvsroot/bitweaver/_bit_groups/index.php,v 1.3 2008/02/07 02:38:11 wjames5 Exp $
// Copyright (c) 2004 bitweaver Group
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

require_once( GROUP_PKG_PATH.'lookup_group_inc.php' );

$gContent->verifyViewPermission();

// Display the template
$gBitSystem->display( 'bitpackage:group/group_home.tpl', tra( 'Groups' ) );
?>
