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

	require_once( 'BitGroup.php' );

	$gBitSmarty->load_filter( 'output', 'groupslayout' );
}
?>
