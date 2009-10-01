<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/theme.php,v 1.10 2009/10/01 14:17:00 wjames5 Exp $
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
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin to edit an existing group 
if( $gContent->isValid() ) {
	if( !$gContent->hasUpdatePermission() ){
		$gBitSystem->fatalError( tra( "You do not have permission to edit this group's theme." ));
	}
	if ( !( $gBitSystem->isFeatureActive( 'group_themes' ) || $gBitSystem->isFeatureActive( 'group_layouts' )) ){
		$gBitSystem->fatalError( tra( 'Sorry, custom styling groups has been disabled by the site administrator.' ));
	}
}else{
	$gBitSystem->fatalError( tra( 'The Group, whose theme you are attempting to administrate, does not exist.' ));
}

if ($gBitSystem->isFeatureActive( 'group_themes' )){
	// apply the site style
	if( !empty( $_REQUEST["group_style"] ) || !empty( $_REQUEST['remove_group_style'] ) ) {
		if( !empty( $_REQUEST['approved'] ) ) {
			$gContent->storePreference( 'theme', ( !empty( $_REQUEST["group_style"] )?$_REQUEST["group_style"]:NULL ) );
			$gContent->storePreference( 'theme_variation', !empty( $_REQUEST["style_variation"] ) ? $_REQUEST["style_variation"] : '' );
			$gBitSystem->setConfig( 'style_variation', !empty( $_REQUEST["style_variation"] ) ? $_REQUEST["style_variation"] : '', GROUP_PKG_NAME );
			if( !empty( $_REQUEST["group_style"] ) ){
				$gBitThemes->setStyle( $_REQUEST["group_style"] );
			}
		} else {
			$gBitSystem->setConfig( 'style_variation', !empty( $_REQUEST["style_variation"] ) ? $_REQUEST["style_variation"] : '', GROUP_PKG_NAME );
			$gBitSmarty->assign( 'approve', TRUE );
			if( !empty( $_REQUEST["group_style"] ) ){
				$gBitThemes->setStyle( $_REQUEST["group_style"] );
			}
		}
	}else{
		// if it has a custom theme lets theme it
		$gContent->setGroupStyle();
	}

	// Get list of available styles
	$styles = $gBitThemes->getStyles( NULL, TRUE );
	$gBitSmarty->assign_by_ref( "styles", $styles );

	$subDirs = array( 'style_info', 'alternate' );
	$stylesList = $gBitThemes->getStylesList( NULL, NULL, $subDirs );
	$gBitSmarty->assign_by_ref( "stylesList", $stylesList );
}

// get layout info
include_once( GROUP_PKG_PATH.'admin_layout_inc.php' );

// display
$gBitSystem->setBrowserTitle( tra( 'Custom style group'." ".$gContent->getTitle() ) );
$gBitSystem->display( "bitpackage:group/edit_group_theme.tpl" , NULL, array( 'display_mode' => 'display' )); ?>
