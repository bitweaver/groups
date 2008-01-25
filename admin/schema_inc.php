<?php
$tables = array(
	'groups' => "
		group_id I4 PRIMARY,
		content_id I4 NOTNULL,
		mod_msgs C(1) DEFAULT 'n',
		mod_content C(1) DEFAULT 'y',
		admin_content_strict C(1) DEFAULT 'n',
		view_content_public C(1) DEFAULT 'y',
		list_group_public C(1) DEFAULT 'y',
		CONSTRAINT ', CONSTRAINT `groups_group_id` FOREIGN KEY (`group_id`) REFERENCES `".BIT_DB_PREFIX."users_groups` (`group_id`)
					, CONSTRAINT `groups_content_id` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)'
	",

	'groups_rolls' => "
		roll_id I4 PRIMARY,
		roll_name C(30)
	",

	'groups_permsissions' => "
		perm_name C(30) PRIMARY
	",

	'groups_rolls_perms_map' => "
		group_content_id I4 PRIMARY,
		roll_id I4 PRIMARY,
		perm_name C(30)
		CONSTRAINT ', CONSTRAINT `groups_rolls_perms_map_group_content_id` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)
					, CONSTRAINT `groups_rolls_perms_map_roll_id` FOREIGN KEY (`roll_id`) REFERENCES `".BIT_DB_PREFIX."groups_rolls` (`roll_id`)
					, CONSTRAINT `groups_rolls_perms_map_perm_name` FOREIGN KEY (`perm_name`) REFERENCES `".BIT_DB_PREFIX."groups_permissions` (`perm_name`)'
	",

	'groups_rolls_users_map' => "
		group_content_id I4 PRIMARY, 
		user_id I4 NOTNULL,
		roll_id I4,
		CONSTRAINT ', CONSTRAINT `groups_rolls_users_map_group_content_id` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)
					, CONSTRAINT `groups_rolls_users_map_user_id` FOREIGN KEY (`user_id`) REFERENCES `".BIT_DB_PREFIX."users_users` (`user_id`)
					, CONSTRAINT `groups_rolls_perms_map_roll_id` FOREIGN KEY (`roll_id`) REFERENCES `".BIT_DB_PREFIX."groups_rolls` (`roll_id`)'
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( GROUPS_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( GROUPS_PKG_NAME, array(
	'description' => "Group package to demonstrate how to build a bitweaver package.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Indexes
$indices = array(
	'groups_group_id_idx' => array('table' => 'groups', 'cols' => 'group_id', 'opts' => NULL ),
	'groups_content_id_idx' => array( 'table' => 'groups', 'cols' => 'content_id', 'opts' => array( 'UNIQUE' ) ),
);
$gBitInstaller->registerSchemaIndexes( GROUPS_PKG_NAME, $indices );

// ### Sequences
/* not needed becuase the id is generated in users_groups. leaving it here just for ref during development incase we need another sequence 
$sequences = array (
	'groups_group_id_seq' => array( 'start' => 1 )
);
$gBitInstaller->registerSchemaSequences( GROUPS_PKG_NAME, $sequences );
*/

$gBitInstaller->registerSchemaDefault( GROUPS_PKG_NAME, array(
	//      "INSERT INTO `".BIT_DB_PREFIX."bit_group_types` (`type`) VALUES ('Group')",
) );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( GROUPS_PKG_NAME, array(
	array( 'p_group_admin', 'Can admin group', 'admin', GROUPS_PKG_NAME ),
	array( 'p_group_create', 'Can create a group', 'registered', GROUPS_PKG_NAME ),
	array( 'p_group_edit', 'Can edit any group', 'editors', GROUPS_PKG_NAME ),
	array( 'p_group_view', 'Can view group', 'basic',  GROUPS_PKG_NAME ),
	array( 'p_group_remove', 'Can delete group', 'admin',  GROUPS_PKG_NAME ),
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( GROUPS_PKG_NAME, array(
	array( GROUPS_PKG_NAME, 'group_default_ordering', 'group_id_desc' ),
	array( GROUPS_PKG_NAME, 'group_list_group_id', 'y' ),
	array( GROUPS_PKG_NAME, 'group_list_title', 'y' ),
	array( GROUPS_PKG_NAME, 'group_list_description', 'y' ),
	array( GROUPS_PKG_NAME, 'group_list_groups', 'y' ),
) );
?>
