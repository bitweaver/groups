<?php
// Initialization
require_once( '../bit_setup_inc.php' );

// Is package installed and enabled
$gBitSystem->verifyPackage( 'group' );

// load group
require_once(GROUP_PKG_PATH.'lookup_group_inc.php' );

// must be owner or admin to edit an existing group 
if( $gContent->isValid() ) {
	if( !$gContent->hasEditPermission() ){
		$gBitSystem->fatalError( tra( "You do not have permission to edit this group's theme." ));
	}
	if ( !( $gBitSystem->isFeatureActive( 'group_themes' ) || $gBitSystem->isFeatureActive( 'group_layouts' )) ){
		$gBitSystem->fatalError( tra( 'Sorry, custom styling groups has been disabled by the site administrator.' ));
	}

	// if it has a custom theme lets theme it
	$gContent->setGroupStyle();
}else{
	$gBitSystem->fatalError( tra( 'The Group, whose theme you are attempting to administrate, does not exist.' ));
}

if ($gBitSystem->isFeatureActive( 'group_themes' )){
	// apply the site style
	if( !empty( $_REQUEST["group_style"] ) ) {
		if( !empty( $_REQUEST['approved'] ) ) {
			$gContent->storePreference( 'theme', $_REQUEST["group_style"] );
			$gContent->storePreference( 'theme_variation', !empty( $_REQUEST["style_variation"] ) ? $_REQUEST["style_variation"] : '' );
			$gBitSystem->setConfig( 'style_variation', !empty( $_REQUEST["style_variation"] ) ? $_REQUEST["style_variation"] : '', GROUP_PKG_NAME );
			$gBitThemes->setStyle( $_REQUEST["group_style"] );
		} else {
			$gBitSystem->setConfig( 'style_variation', !empty( $_REQUEST["style_variation"] ) ? $_REQUEST["style_variation"] : '', GROUP_PKG_NAME );
			$gBitSmarty->assign( 'approve', TRUE );
			$gBitThemes->setStyle( $_REQUEST["group_style"] );
		}
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
$gBitSystem->display( "bitpackage:group/edit_group_theme.tpl" ); ?>
