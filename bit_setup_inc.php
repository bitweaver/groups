<?php
global $gBitSystem;

$registerHash = array(
	'package_name' => 'group',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

if( $gBitSystem->isPackageActive( 'group' ) ) {
	$menuHash = array(
		'package_name'  => GROUP_PKG_NAME,
		'index_url'     => GROUP_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:group/menu_group.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );

	$gLibertySystem->registerService( LIBERTY_SERVICE_GROUP, GROUP_PKG_NAME, array(
		'module_display_function'		=> 'group_module_display',
		'content_edit_mini_tpl'			=> 'bitpackage:group/edit_group_mini_inc.tpl',
		'content_nav_tpl'				=> 'bitpackage:group/view_group_nav.tpl',
		'content_body_tpl'				=> 'bitpackage:group/view_group_body.tpl',
		'content_view_tpl'				=> 'bitpackage:group/view_group_view.tpl',
		'content_display_function'		=> 'group_content_display',
		'content_list_sql_function'		=> 'group_content_list_sql',
		'content_preview_function'		=> 'group_content_preview',
		'content_edit_function'			=> 'group_content_edit',
		'content_store_function'		=> 'group_content_store',
		'comment_store_function'		=> 'group_comment_store',
		'content_expunge_function'		=> 'group_content_expunge',
		'content_user_perms_function'	=> 'group_content_user_perms',
	) );

	/**
	 * load up switchboard.
	 * we need to include bit_setup_inc incase groups gets loaded first.
	 * this is a dirty hack since we don't have a way to set the load order of bit_setup_inc.php files yet.
	 * TODO: have a load order for bit_setup_inc.php files and remove this terrible hack
	 */
	if( is_file( BIT_ROOT_PATH.'switchboard/bit_setup_inc.php' )) {
		require_once( BIT_ROOT_PATH.'switchboard/bit_setup_inc.php' );
		if( $gBitSystem->isPackageActive( 'switchboard' )) {
			global $gSwitchboardSystem;
			$gSwitchboardSystem->registerSwitchboardSender( GROUP_PKG_NAME, array( 'message' ));
		}
	}

	require_once( 'BitGroup.php' );

	$gBitSmarty->load_filter( 'output', 'groupslayout' );
}
?>
