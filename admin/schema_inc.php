<?php
$tables = array(
	'groups' => "
		group_id I4 PRIMARY,
		content_id I4 NOTNULL,
		mod_msgs C(1) DEFAULT 'n',
		mod_content C(1) DEFAULT 'y',
		admin_content_strict C(1) DEFAULT 'n',
		view_content_public C(1) DEFAULT 'y'
		CONSTRAINT ', CONSTRAINT `groups_group_id` FOREIGN KEY (`group_id`) REFERENCES `".BIT_DB_PREFIX."users_groups` (`group_id`)
					, CONSTRAINT `groups_content_id` FOREIGN KEY (`content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)'
	",

	'groups_roles' => "
		role_id I4 PRIMARY,
		role_name C(30),
		role_desc C(255)
	",

	'groups_permissions' => "
		perm_name C(30) PRIMARY,
		perm_desc C(250)
	",

	'groups_roles_perms_map' => "
		group_content_id I4 PRIMARY,
		perm_name C(30) NOTNULL,
		role_id I4 NOTNULL
		CONSTRAINT ', CONSTRAINT `groups_roles_perms_map_group_content_id` FOREIGN KEY (`group_content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)
					, CONSTRAINT `groups_roles_perms_map_role_id` FOREIGN KEY (`role_id`) REFERENCES `".BIT_DB_PREFIX."groups_roles` (`role_id`)
					, CONSTRAINT `groups_roles_perms_map_perm_name` FOREIGN KEY (`perm_name`) REFERENCES `".BIT_DB_PREFIX."users_permissions` (`perm_name`)'
	",

	'groups_roles_users_map' => "
		group_content_id I4 PRIMARY, 
		user_id I4 NOTNULL,
		role_id I4 NOTNULL
		CONSTRAINT ', CONSTRAINT `groups_roles_users_map_group_content_id` FOREIGN KEY (`group_content_id`) REFERENCES `".BIT_DB_PREFIX."liberty_content` (`content_id`)
					, CONSTRAINT `groups_roles_users_map_user_id` FOREIGN KEY (`user_id`) REFERENCES `".BIT_DB_PREFIX."users_users` (`user_id`)
					, CONSTRAINT `groups_roles_perms_map_role_id` FOREIGN KEY (`role_id`) REFERENCES `".BIT_DB_PREFIX."groups_roles` (`role_id`)'
	",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( GROUP_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( GROUP_PKG_NAME, array(
	'description' => "The Group package allows users to create groups to organize content.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );

// ### Indexes
$indices = array(
	'groups_group_id_idx' => array('table' => 'groups', 'cols' => 'group_id', 'opts' => NULL ),
	'groups_content_id_idx' => array( 'table' => 'groups', 'cols' => 'content_id', 'opts' => array( 'UNIQUE' ) ),
);
$gBitInstaller->registerSchemaIndexes( GROUP_PKG_NAME, $indices );

// ### Sequences
$sequences = array (
	'groups_roles_role_id_seq' => array( 'start' => 4 )
);
$gBitInstaller->registerSchemaSequences( GROUP_PKG_NAME, $sequences );

$gBitInstaller->registerSchemaDefault( GROUP_PKG_NAME, array(
	//      "INSERT INTO `".BIT_DB_PREFIX."bit_group_types` (`type`) VALUES ('Group')",
) );

// ### Default group level roles and permissions
$gBitInstaller->registerSchemaDefault( LIBERTY_PKG_NAME, array(
	"INSERT INTO `".BIT_DB_PREFIX."groups_roles` (`role_id`,`role_name`, `role_desc`) VALUES (1, 'admin', 'Group Administrators')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_roles` (`role_id`,`role_name`, `role_desc`) VALUES (2, 'editors', 'Group Managers')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_roles` (`role_id`,`role_name`, `role_desc`) VALUES (3, 'registered', 'Group Members')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_permissions` (`perm_name`,`perm_desc`) VALUES ('p_group_group_content_admin', 'Can admin group content')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_permissions` (`perm_name`,`perm_desc`) VALUES ('p_group_group_content_create', 'Can create group content')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_permissions` (`perm_name`,`perm_desc`) VALUES ('p_group_group_content_submit', 'Can submit content to group for inclusion')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_permissions` (`perm_name`,`perm_desc`) VALUES ('p_group_group_members_admin', 'Can admin group members')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_permissions` (`perm_name`,`perm_desc`) VALUES ('p_group_group_members_view', 'Can view group members')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_permissions` (`perm_name`,`perm_desc`) VALUES ('p_group_group_members_invite', 'Can send invitations to the group')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_permissions` (`perm_name`,`perm_desc`) VALUES ('p_group_group_msgs_admin', 'Can admin group forums')",
	"INSERT INTO `".BIT_DB_PREFIX."groups_permissions` (`perm_name`,`perm_desc`) VALUES ('p_group_group_msgs_create', 'Can post messages to group forums')",
) );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( GROUP_PKG_NAME, array(
	array( 'p_group_admin', 'Can admin group', 'admin', GROUP_PKG_NAME ),
	array( 'p_group_create', 'Can create a group', 'registered', GROUP_PKG_NAME ),
	array( 'p_group_edit', 'Can edit any group', 'editors', GROUP_PKG_NAME ),
	array( 'p_group_view', 'Can view group', 'basic',  GROUP_PKG_NAME ),
	array( 'p_group_remove', 'Can delete group', 'admin',  GROUP_PKG_NAME ),
) );

// ### Default Preferences
$gBitInstaller->registerPreferences( GROUP_PKG_NAME, array(
	array( GROUP_PKG_NAME, 'group_default_ordering', 'group_id_desc' ),
	array( GROUP_PKG_NAME, 'group_list_group_id', 'y' ),
	array( GROUP_PKG_NAME, 'group_list_title', 'y' ),
	array( GROUP_PKG_NAME, 'group_list_description', 'y' ),
	array( GROUP_PKG_NAME, 'group_list_groups', 'y' ),
) );
?>