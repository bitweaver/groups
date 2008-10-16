<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_groups/BitGroup.php,v 1.106 2008/10/16 17:59:08 squareing Exp $
 * Copyright (c) 2008 bitweaver Group
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * Group class 
 * builds on core bitweaver functionality, such as the Liberty CMS engine
 * 
 * @author wjames <will@tekimaki.com> spider <spider@viovio.com>
 * @package groups
 */
 
/**
 * Initialize
 */
require_once( LIBERTY_PKG_PATH.'LibertyMime.php' );

/**
* This is used to uniquely identify the object
*/
define( 'BITGROUP_CONTENT_TYPE_GUID', 'bitgroup' );

/*
 * Defines for basic roles you can't remove
 */
define( 'GROUPS_ROLE_ADMIN', 1);
define( 'GROUPS_ROLE_MANAGER', 2);
define( 'GROUPS_ROLE_MEMBER', 3);

/**
 * Load up our moderation handlers
 */
require_once( GROUP_PKG_PATH.'moderation_inc.php' );


/**
 * @package groups
 */
class BitGroup extends LibertyMime {
	/**
	* Primary key
	* @public
	*/
	var $mGroupId;



	/**
	* During initialisation, be sure to call our base constructors
	**/
	function BitGroup( $pGroupId=NULL, $pContentId=NULL ) {
		LibertyMime::LibertyMime();
		$this->mGroupId = (int)$pGroupId;
		$this->mContentId = (int)$pContentId;
		$this->mContentTypeGuid = BITGROUP_CONTENT_TYPE_GUID;
		$this->registerContentType( BITGROUP_CONTENT_TYPE_GUID, array(
			'content_type_guid' => BITGROUP_CONTENT_TYPE_GUID,
			'content_description' => 'Group',
			'handler_class' => 'BitGroup',
			'handler_package' => 'group',
			'handler_file' => 'BitGroup.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		// Permission setup
		$this->mViewContentPerm  = 'p_group_view';
		$this->mCreateContentPerm  = 'p_group_create';
		$this->mEditContentPerm  = 'p_group_edit';
		$this->mAdminContentPerm = 'p_group_admin';

		// A reference to the group's affiliated board, see getBoard() below
		$this->mBoardObj = NULL;

		// start up switchboard if it's available
		if( $gBitSystem->isPackageActive( 'switchboard' )) {
			global $gSwitchboardSystem;
			// Register us as a sender.
			$gSwitchboardSystem->registerSwitchboardSender( GROUP_PKG_NAME, array( 'message' ));
		}
	}

	/**
	* Load the data from the database
	* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	**/
	function load() {
		if( $this->verifyId( $this->mGroupId ) || $this->verifyId( $this->mContentId ) ) {
			// LibertyContent::load()assumes you have joined already, and will not execute any sql!
			// This is a significant performance optimization
			$lookupColumn = $this->verifyId( $this->mGroupId ) ? 'group_id' : 'content_id';
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';
			array_push( $bindVars, $lookupId = @BitBase::verifyId( $this->mGroupId ) ? $this->mGroupId : $this->mContentId );
			$this->getServicesSql( 'content_load_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

			$query = "SELECT s.*, lc.*, lcds.`data` AS `summary`, lcda.`data` AS `after_registration`, ug.*, " .
			"lfp.storage_path AS `image_attachment_path`, " .
			"uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name, " .
			"uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name " .
			"$selectSql " .
			"FROM `".BIT_DB_PREFIX."groups` s " .
			"INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = s.`content_id` ) $joinSql" .
			"INNER JOIN `".BIT_DB_PREFIX."users_groups` ug ON( ug.`group_id` = s.`group_id` ) " .
			"LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_data` lcds ON (lc.`content_id` = lcds.`content_id` AND lcds.`data_type`='summary')" .
			"LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_data` lcda ON (lc.`content_id` = lcda.`content_id` AND lcda.`data_type`='after_registration')" .
			"LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` la ON( la.`content_id` = lc.`content_id` AND la.`is_primary` = 'y' )" .
			"LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lfp ON( lfp.`file_id` = la.`foreign_id` )" .
			"LEFT JOIN `".BIT_DB_PREFIX."users_users` uue ON( uue.`user_id` = lc.`modifier_user_id` )" .
			"LEFT JOIN `".BIT_DB_PREFIX."users_users` uuc ON( uuc.`user_id` = lc.`user_id` )" .
			"WHERE s.`$lookupColumn`=? $whereSql";
			$result = $this->mDb->query( $query, $bindVars );

			if( $result && $result->numRows() ) {
				$this->mInfo = $result->fields;
				$this->mContentId = $result->fields['content_id'];
				$this->mGroupId = $result->fields['group_id'];

				$this->mInfo['creator'] =( isset( $result->fields['creator_real_name'] )? $result->fields['creator_real_name'] : $result->fields['creator_user'] );
				$this->mInfo['editor'] =( isset( $result->fields['modifier_real_name'] )? $result->fields['modifier_real_name'] : $result->fields['modifier_user'] );
				$this->mInfo['display_url'] = $this->getDisplayUrl();
				$this->mInfo['parsed_data'] = $this->parseData();
				$this->mInfo['num_members'] = $this->getMembersCount( $this->mGroupId );
				$this->mInfo['thumbnail_url'] = liberty_fetch_thumbnails( array( "storage_path" => $this->mInfo['image_attachment_path'] ) );

				$this->mContentTypePrefs = $this->getContentTypePrefs();
				$this->mContentTypeData = $this->getContentTypeData();

				// sets $this->mGroupMemberPermissions
				$this->getMemberRolesAndPermsForGroup();

				LibertyMime::load();
			}
		}
		return( count( $this->mInfo ) );
	}


	/**
	 * Prepare data for preview
	 */
	function preparePreview( $pParamHash ) {
		global $gBitSystem, $gBitUser;

		if( empty( $this->mInfo['user_id'] ) ) {
			$this->mInfo['user_id'] = $gBitUser->mUserId;
			$this->mInfo['creator_user'] = $gBitUser->getField( 'login' );
			$this->mInfo['creator_real_name'] = $gBitUser->getField( 'real_name' );
		}

		$this->mInfo['creator_user_id'] = $this->mInfo['user_id'];

		if( empty( $this->mInfo['created'] ) ){
			$this->mInfo['created'] = $gBitSystem->getUTCTime();
		}

		if( isset( $pParamHash["group"]["title"] ) ) {
			$this->mInfo["title"] = $pParamHash["group"]["title"]; 
		}       

		if( isset( $pParamHash["group"]["summary"] ) ) {
			$this->mInfo["description"] = $pParamHash["group"]["summary"];
		}

		if( isset( $pParamHash["format_guid"] ) ) {
			$this->mInfo['format_guid'] = $pParamHash["format_guid"];
		}   

		if( isset( $pParamHash["group"]["edit"] ) ) {
			$this->mInfo["data"] = $pParamHash["group"]["edit"];
			$this->mInfo['parsed_data'] = $this->parseData();
		}
	}

	/**
	* @param array pParamHash hash of values that will be used to store the group
	* be sure to pass by reference in case we need to make modifcations to the hash
	*
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	*
	* @access public
	**/
	function store( &$pParamHash ) {
		global $gBitUser, $gBitSystem;
		$this->mDb->StartTrans();

		// Verify and then store group and content.
		if( $this->verify( $pParamHash ) && $gBitUser->storeGroup( $pParamHash ) && LibertyMime::store( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."groups";
			if( $this->mGroupId ) {
				// editing an existing group
				$locId = array( "group_id" => $pParamHash['group_id'] );
				$result = $this->mDb->associateUpdate( $table, $pParamHash['group_pkg_store'], $locId );
			}else {
				// new group
				$pParamHash['group_pkg_store']['content_id'] = $pParamHash['content_id'];
				$pParamHash['group_pkg_store']['group_id'] = $pParamHash['group_store']['group_id'];
				$this->mGroupId = $pParamHash['group_store']['group_id'];
				$result = $this->mDb->associateInsert( $table, $pParamHash['group_pkg_store'] );
				// Make sure this user is in the group
				$gBitUser->addUserToGroup( $gBitUser->mUserId, $this->mGroupId );
				// Restore the group in users table to update the home link now that we have a group id
				$pParamHash['home'] = GROUP_PKG_URL."index.php?group_id=".$this->mGroupId;
				$gBitUser->storeGroup( $pParamHash );
				// Autogenerate a board for this group
				if ( $gBitSystem->isPackageActive( 'boards' ) ){
					require_once( BOARDS_PKG_PATH.'BitBoard.php' );
					$board = new BitBoard();
					$boardHash = array(
							"title" => $pParamHash['title']." ".tra('Forum'),
							"data" => tra('Message board for the ').$pParamHash['title']." ".tra('Group'),
							'boards_mailing_list' => preg_replace( '/[^a-z0-9]/', '', strtolower( $pParamHash['content_store']['title'] ) ),
							'boards_mailing_list_password' => substr( md5( rand() ), 0, 8 ),
						);
					if ( $board->store( $boardHash ) ){
						$this->linkContent( $board->mInfo );
						$this->mBoardObj = &$board;
					}
				}
			}

			if ( $gBitSystem->isPackageActive( 'boards' ) ){
				if ( empty( $board ) || !is_object( $board ) ){
					$board = $this->getBoard();
				}
				// pass moderate messages selection on to our group board
				$modComments = $pParamHash['group_pkg_store']['mod_msgs'] == 'y'?$pParamHash['group_pkg_store']['mod_msgs']:NULL;
				$board->storePreference( 'moderate_comments', $modComments );
			}

			$this->mDb->CompleteTrans();
			$this->load();
		}

		return( count( $this->mErrors )== 0 );
	}

	/**
	* Make sure the data is safe to store
	* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	* This function is responsible for data integrity and validation before any operations are performed with the $pParamHash
	* NOTE: This is a PRIVATE METHOD!!!! do not call outside this class, under penalty of death!
	*
	* @param array pParams reference to hash of values that will be used to store the page, they will be modified where necessary
	*
	* @return bool TRUE on success, FALSE if verify failed. If FALSE, $this->mErrors will have reason why
	*
	* @access private
	**/
	function verify( &$pParamHash ) {
		global $gBitUser, $gBitSystem;

		// make sure we're all loaded up of we have a mGroupId
		if( $this->verifyId( $this->mGroupId ) && empty( $this->mInfo ) ) {
			$this->load();
		}

		if( @$this->verifyId( $this->mInfo['content_id'] ) ) {
			$pParamHash['content_id'] = $this->mInfo['content_id'];
		}

		if( @$this->verifyId( $this->mInfo['group_id'] ) ) {
			$pParamHash['group_id'] = $this->mInfo['group_id'];
		}
		
		if( @$this->verifyId( $this->mInfo['user_id'] ) ) {
			$pParamHash['user_id'] = $this->mInfo['user_id'];
		}

		// It is possible a derived class set this to something different
		if( @$this->verifyId( $pParamHash['content_type_guid'] ) ) {
			$pParamHash['content_type_guid'] = $this->mContentTypeGuid;
		}

		if( @$this->verifyId( $pParamHash['content_id'] ) ) {
			$pParamHash['group_pkg_store']['content_id'] = $pParamHash['content_id'];
		}

		if( @$this->verifyId( $pParamHash['group_id'] ) ) {
			$pParamHash['group_store']['group_id'] = $pParamHash['group_id'];
			$pParamHash['group_pkg_store']['group_id'] = $pParamHash['group_id'];
		}

		if( !empty( $pParamHash['data'] ) ) {
			$pParamHash['edit'] = $pParamHash['data'];
		}

		// check for name issues, first truncate length if too long
		if( !empty( $pParamHash['title'] ) ) {
			if( empty( $this->mGroupId ) ) {
				if( empty( $pParamHash['title'] ) ) {
					$this->mErrors['title'] = tra('You must enter a name for this group.');
				} else {
					$pParamHash['content_store']['title'] = substr( $pParamHash['title'], 0, 160 );
					// Copy title to name for group verify
					$pParamHash['name'] = $pParamHash['content_store']['title'];
				}
			} else {
				$pParamHash['content_store']['title'] =( isset( $pParamHash['title'] ) )? substr( $pParamHash['title'], 0, 160 ): '';
				// Copy title to name for group verify
				$pParamHash['name'] = $pParamHash['content_store']['title'];
			}
		} else if( empty( $pParamHash['title'] ) ) {
			// no name specified
			$this->mErrors['title'] = tra('You must specify a name for this group.');
		}

		// Constrain summary to 250 to fit in groups desc table as well
		if( isset($pParamHash['summary']) ) {
			$pParamHash['summary'] = substr($pParamHash['summary'], 0, 250);
			$pParamHash['desc'] = $pParamHash['summary'];
		}

		// Setup the group home URL
		if( @$this->verifyId( $this->mGroupId ) ) {
			$pParamHash['home'] = GROUP_PKG_URL."index.php?group_id=".$this->mGroupId;
		}elseif( !empty($pParamHash['name']) ) {
			$pParamHash['home'] = GROUP_PKG_URL.urlencode($pParamHash['name']);
		}

		// Do we have after_registration data?
		if( !empty($pParamHash['after_registration']) && !empty($pParamHash['name']) ) {
			$pParamHash['data_store']['after_registration'] = $pParamHash['after_registration'];
			$pParamHash['after_registration_page'] = $pParamHash['home'] = GROUP_PKG_URL.'registered/'.$pParamHash['name'];
		}
		else if ( !empty($pParamHash['name']) ){
			$pParamHash['data_store']['after_registration'] = NULL;
			$pParamHash['after_registration_page'] = GROUP_PKG_URL.urlencode($pParamHash['name']);
		}

		$pParamHash['group_pkg_store']['view_content_public'] = !empty( $pParamHash['view_content_public'] )?$pParamHash['view_content_public']:'n';
		$pParamHash['group_pkg_store']['mod_msgs'] = !empty( $pParamHash['mod_msgs'] )?$pParamHash['mod_msgs']:'n';
		$pParamHash['group_pkg_store']['mod_content'] = !empty( $pParamHash['mod_content'] )?$pParamHash['mod_content']:'n';
		$pParamHash['group_pkg_store']['admin_content_strict'] = !empty( $pParamHash['admin_content_strict'] )?$pParamHash['admin_content_strict']:'n';

		// Make sure we don't set is_default or batch_set_default for security
		if( isset($pParamHash['is_default']) || isset($pParamHash['batch_set_default']) ) {
			$this->mErrors['default'] = tra('Attempt to set group as default group or batch set default. This is not allowed.');
		}

		// Verify the group information
		$gBitUser->verifyGroup( $pParamHash );

		// Merge errors from the group checks
		$this->mErrors = array_merge($gBitUser->mErrors, $this->mErrors);

		return( count( $this->mErrors )== 0 );
	}

	/**
	* This function removes a group entry
	**/
	function expunge() {
		global $gBitUser;
		$ret = FALSE;
		if( $this->isValid() ) {
			// before we clear out the group we need to know its board so we can clear it out too
			$board = &$this->getBoard();

			// delete the group and its related group pkg settings
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX."groups` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			$query = "DELETE FROM `".BIT_DB_PREFIX."groups_roles_perms_map` WHERE `group_content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			$query = "DELETE FROM `".BIT_DB_PREFIX."groups_roles_users_map` WHERE `group_content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			$query = "DELETE FROM `".BIT_DB_PREFIX."groups_content_cnxn_map` WHERE `group_content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			$query = "DELETE FROM `".BIT_DB_PREFIX."groups_content_types` WHERE `group_content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyMime::expunge() ) {
				if( $gBitUser->remove_group($this->mGroupId) ) {
					$ret = TRUE;
					if( !empty( $board ) ) {
						// delete the associated board
						$board->expunge();
					}
					$this->mDb->CompleteTrans();
				}
				else {
					$this->mDb->RollbackTrans();
				}
			} else {
				$this->mDb->RollbackTrans();
			}
		}
		return $ret;
	}

	/**
	* Make sure group is loaded and valid
	**/
	function isValid() {
		return( $this->verifyId( $this->mGroupId ) && $this->verifyId( $this->mContentId ) );
	}

	/**
	* This function generates a list of records from the liberty_content database for use in a list page
	**/
	function getList( &$pParamHash ) {
		global $gBitSystem, $gBitUser;
		// this makes sure parameters used later on are set
		LibertyContent::prepGetList( $pParamHash );

		$selectSql = $joinSql = $whereSql = '';
		$bindVars = array();
		array_push( $bindVars, $this->mContentTypeGuid );
		$this->getServicesSql( 'content_list_sql_function', $selectSql, $joinSql, $whereSql, $bindVars );

		// this will set $find, $sort_mode, $max_records and $offset
		extract( $pParamHash );
		
		if( isset( $pParamHash['user_id'] )){
			$joinSql .= " INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON (g.`group_id` = ugm.`group_id`)";
			$whereSql .= " AND ugm.`user_id` = ?";
			$bindVars[] = $pParamHash['user_id'];
		}

		if( isset( $pParamHash['mapped_content_id'] )){
			$joinSql .= " INNER JOIN `".BIT_DB_PREFIX."groups_content_cnxn_map` gccm ON (g.`content_id` = gccm.`group_content_id`)";
			$whereSql .= " AND gccm.`to_content_id` = ?";
			$bindVars[] = $pParamHash['mapped_content_id'];
		}

		if( is_array( $find ) ) {
			// you can use an array of pages
			$whereSql .= " AND lc.`title` IN( ".implode( ',',array_fill( 0,count( $find ),'?' ) )." )";
			$bindVars = array_merge ( $bindVars, $find );
		} elseif( is_string( $find ) ) {
			// or a string
			$whereSql .= " AND UPPER( lc.`title` )like ? ";
			$bindVars[] = '%' . strtoupper( $find ). '%';
		}

		$query = "SELECT g.*, lc.`content_id`, lcds.`data` AS `summary`, lc.`title`, lc.`data`, 
			lfp.storage_path AS `image_attachment_path`, 
			ug.* $selectSql
			FROM `".BIT_DB_PREFIX."groups` g 
			INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = g.`content_id` ) 
			INNER JOIN `".BIT_DB_PREFIX."users_groups` ug ON( ug.`group_id` = g.`group_id` ) 
			LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_content_data` lcds ON (lc.`content_id` = lcds.`content_id` AND lcds.`data_type`='summary')
			LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_attachments` la ON( la.`content_id` = lc.`content_id` AND la.`is_primary` = 'y' ) 
			LEFT OUTER JOIN `".BIT_DB_PREFIX."liberty_files` lfp ON( lfp.`file_id` = la.`foreign_id` )
			$joinSql
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $sort_mode );
		$query_cant = "select count(*)
				FROM `".BIT_DB_PREFIX."groups` g 
				INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = g.`content_id` )
				INNER JOIN `".BIT_DB_PREFIX."users_groups` ug ON( ug.`group_id` = g.`group_id` ) 
			   	$joinSql
			WHERE lc.`content_type_guid` = ? $whereSql";
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );
		$ret = array();
		while( $res = $result->fetchRow() ) {
			$res['num_members'] = $this->getMembersCount( $res['group_id'] );
			$res['thumbnail_url'] = liberty_fetch_thumbnails( array( "storage_path" => $res['image_attachment_path'] ) );
			$ret[] = $res;
		}
		$pParamHash["cant"] = $this->mDb->getOne( $query_cant, $bindVars );

		// add all pagination info to pParamHash
		LibertyContent::postGetList( $pParamHash );
		return $ret;
	}

	/**
	* Generates the URL to the group page
	* @param pExistsHash the hash that was returned by LibertyContent::pageExists
	* @return the link to display the page.
	*/
	function getDisplayUrl() {
		$ret = NULL;
		if( @$this->verifyId( $this->mGroupId ) ) {
			$ret = GROUP_PKG_URL."index.php?group_id=".$this->mGroupId;
		}
		return $ret;
	}




	// -------------------- Group Roles Funtions -------------------- //
	
	function getRoles() {
        $sql = "SELECT gr.* FROM `".BIT_DB_PREFIX."groups_roles` gr 
                ORDER BY gr.`role_name` ASC";
        $ret = array();
        if ( $roles = $this->mDb->query( $sql ) ){
            while( $row = $roles->fetchRow() ) {
				$roleId = $row['role_id'];
				$ret[$roleId] = $row;
				$ret[$roleId]['perms'] = array();
				if ( @BitBase::verifyId( $this->mContentId ) ){
					$ret[$roleId]['perms'] = $this->getRolesPerms( array( 'content_id' => $this->mContentId, 'role_id' => $roleId ));
				}
            }
        }
		return $ret;
	}

	/**
	 * @param array group_content_id, if unset, all role perm types are returned
	 **/
	function getRolesPerms( $pParamHash = NULL ) {
		$result = array();
		$bindVars = array();
		$whereSql = $selectSql = $fromSql = '';
		if( @BitBase::verifyId( $pParamHash['content_id'] )) {
		//	$selectSql = ', rp.`perm_name` AS `hasPerm` ';
			$fromSql = ' INNER JOIN `'.BIT_DB_PREFIX.'groups_roles_perms_map` rp ON ( rp.`perm_name` = gp.`perm_name` ) ';
			$whereSql .= " WHERE rp.`group_content_id`=? AND rp.`role_id` = ?";
			$bindVars[] = $pParamHash['content_id'];
			$bindVars[] = $pParamHash['role_id'];
		}
		$sql = "SELECT gp.* $selectSql
		   		FROM `".BIT_DB_PREFIX."groups_permissions` gp $fromSql $whereSql
				ORDER BY gp.`perm_name` ASC";
		$result = $this->mDb->getAssoc( $sql, $bindVars );
		return $result;
	}

	function assignPermissionToRole( $perm, $pRoleId ) {
		if( $this->isValid() ) {
			$this->removePermissionFromRole( $perm, $pRoleId );
			$query = "INSERT INTO `".BIT_DB_PREFIX."groups_roles_perms_map`( `perm_name`, `role_id`, `group_content_id`, `group_id` ) VALUES(?, ?, ?, ?)";
			$result = $this->mDb->query($query, array($perm, $pRoleId, $this->mContentId, $this->mGroupId));
			return TRUE;
		}
		return FALSE;
	}

	function removePermissionFromRole( $perm, $pRoleId ) {
		if( $this->isValid() ) {
			$query = "delete from `".BIT_DB_PREFIX."groups_roles_perms_map` where `perm_name` = ?  and `role_id` = ? and `group_content_id` = ?";
			$result = $this->mDb->query($query, array($perm, $pRoleId, $this->mContentId));
			return TRUE;
		}
		return FALSE;
	}

	function assignUserRoleToGroup( $pRoleId, $pUserId ){
		if( $this->isValid() ) {
			$this->removeUserRoleFromGroup( $pRoleId, $pUserId );
			$query = "INSERT INTO `".BIT_DB_PREFIX."groups_roles_users_map`( `role_id`, `user_id`, `group_content_id`, `group_id` ) VALUES(?, ?, ?, ?)";
			$result = $this->mDb->query($query, array($pRoleId, $pUserId, $this->mContentId, $this->mGroupId));
			return TRUE;
		}
		return FALSE;
	}

	function removeUserRoleFromGroup( $pRoleId, $pUserId ){
		if( $this->isValid() ) {
			$query = "delete from `".BIT_DB_PREFIX."groups_roles_users_map` where `role_id` = ?  and `user_id` = ? and `group_content_id` = ?";
			$result = $this->mDb->query($query, array($pRoleId, $pUserId, $this->mContentId));
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * getMemberRolesAndPermsForGroup
	 * this gets the permissions the user has for their roles in the group. 
	 * perms here should not be confused with perms in users package, 
	 * and the pemissions check routines in liberty, they are unrelated
	 **/
	function getMemberRolesAndPermsForGroup(){
		global $gBitUser;

		if ( $this->verifyId( $this->mContentId ) ){
			$this->mGroupMemberRoles = array();
			$this->mGroupMemberPermissions = array();
			// Load up the roles for this user
			$roles = $this->mDb->getArray( "SELECT `role_id` from `".BIT_DB_PREFIX."groups_roles_users_map` WHERE `group_content_id` = ? AND user_id = ?", array($this->mContentId, $gBitUser->mUserId));
			foreach ( $roles as $role ){
				$this->mGroupMemberRoles[] = $role['role_id'];
			}

			// Are they a member as well?
			if ( array_key_exists((int)$this->mGroupId, $gBitUser->mGroups ) ) {
				$this->mGroupMemberRoles[] = GROUPS_ROLE_MEMBER;
			}

			// Now figure which set of permissions to load
			if ( in_array(GROUPS_ROLE_ADMIN, $this->mGroupMemberRoles) ) {
				// We might consider dropping this one and just check admin role.
				$this->mGroupMemberPermissions = $this->mDb->getArray("SELECT perm_name FROM `".BIT_DB_PREFIX."groups_permissions`");
			} elseif( !empty( $this->mGroupMemberRoles ) ){
				$query = "SELECT DISTINCT(rp.`perm_name`)
							FROM `".BIT_DB_PREFIX."groups_roles_perms_map` rp
							WHERE rp.`group_content_id` = ? AND rp.`role_id` IN (".implode( ',',array_fill( 0,count( $this->mGroupMemberRoles ),'?' ) )." )";
				$bindVars[] = $this->mContentId;
				$bindVars = array_merge($bindVars, $this->mGroupMemberRoles);
				$this->mGroupMemberPermissions = $this->mDb->getArray($query, $bindVars);
			}
		}
	}

	function getMembers(){
		$ret = array();
		if ( $this->verifyId( $this->mGroupId ) ){
			$query = "SELECT uu.`user_id` AS hash_key, uu.`login`, uu.`real_name`, uu.`user_id`, uu.`email` 
						FROM `".BIT_DB_PREFIX."users_users` uu 
						INNER JOIN `".BIT_DB_PREFIX."users_groups_map` ug ON (uu.`user_id`=ug.`user_id`) 
						WHERE `group_id`=?";
			$bindVars = array( $this->mGroupId );
			if ( $users = $this->mDb->query( $query, $bindVars ) ){
				while( $row = $users->fetchRow() ) {
					$login = $row['login'];
					$ret[$login] = $row;
					$ret[$login]['roles'] = array();
					if ( @BitBase::verifyId( $this->mContentId ) ){
						$ret[$login]['roles'] = $this->getMemberRoles( $row['user_id'] );
					}
				}
			}
		}
		return $ret;
	}

	/* getMembersCount
	 * gets the number of members in a group.
	 */
	function getMembersCount( $pGroupId ){
		$result = NULL;
		if( @$this->verifyId( $pGroupId ) ) {
			$memberCantSql = "SELECT COUNT(*) FROM `".BIT_DB_PREFIX."users_groups_map` WHERE `group_id` = ?";
			$result = $this->mDb->getOne($memberCantSql, array( $pGroupId ));
		}
		return $result;
	}

	function getMemberRoles( $pUserId ){
		$ret = array();
		if ( !empty($pUserId) && $this->verifyId( $this->mContentId  ) ){
			$query = "SELECT ru.`role_id` from `".BIT_DB_PREFIX."groups_roles_users_map` ru WHERE ru.`group_content_id` = ? AND ru.`user_id` = ? ORDER BY ru.`role_id` ASC";
			$bindVars = array ( $this->mContentId , $pUserId );
			$result = $this->mDb->getArray( $query, $bindVars );
			foreach( $result as $role ){
				$ret[] = $role['role_id'];
			}
		}
		return $ret;
	}

	// -------------------- End Group Roles Funtions -------------------- //




	// -------------------- Member Email Funtions -------------------- //
	
	function storeUserEmailPref( $pPref, $pUser=NULL ){ 
		global $gBitSystem, $gBitUser;
		// if user is NULL get the active one
		if ( !is_object( $pUser ) ){
			$pUser = &$gBitUser;
		}
		// if the content is this and its board has a mailing list then act on that
		if ( ($board = $this->getBoard()) && $board->getPreference( 'boards_mailing_list' ) ){
			require_once( UTIL_PKG_PATH.'mailman_lib.php' );
			if( $pPref == 'email' ) {
				mailman_addmember( $board->getPreference( 'boards_mailing_list' ), $pUser->getField( 'email' ) );
			} elseif( $pPref == 'none' ) {
				mailman_remove_member( $board->getPreference( 'boards_mailing_list' ), $pUser->getField( 'email' ) );
			}
		}
		// no mailing list then store in switchboard
		elseif ( $gBitSystem->isPackageActive('switchboard') ) {
			global $gSwitchboardSystem;
			if ($pPref != 'none') {
			  $gSwitchboardSystem->storeUserPref($pUser->mUserId, 'group', 'message', $this->mContentId,  $pPref); 
			}
			else {
			  $gSwitchboardSystem->deleteUserPref($pUser->mUserId, 'group', 'message', $this->mContentId);
			}
		}
	}

	// dump the users email prefs
	function deleteUserEmailPref( $pUser=NULL ){
		global $gBitSystem, $gBitUser;
		// if user is NULL get the active one
		if ( !is_object( $pUser ) ){
			$pUser = &$gBitUser;
		}
		// if the content is this and its board has a mailing list then act on that
		if ( ($board = $this->getBoard()) && $board->getPreference( 'boards_mailing_list' ) ){
			require_once( UTIL_PKG_PATH.'mailman_lib.php' );
			mailman_remove_member( $board->getPreference( 'boards_mailing_list' ), $pUser->getField( 'email' ) );
		}
		// no mailing list then store in switchboard
		if ( $gBitSystem->isPackageActive('switchboard') ) {
			global $gSwitchboardSystem;
			$gSwitchboardSystem->deleteUserPref($pUser->mUserId, 'group', 'message', $this->mContentId ); 
		}
	}

	function getUserEmailPref( $pUser=NULL ){
		global $gBitSystem, $gBitUser;
		// if user is NULL get the active one
		if ( !is_object( $pUser ) ){
			$pUser = &$gBitUser;
		}
		// if the content is this and its board has a mailing list then act on that
		if ( ($board = $this->getBoard()) && $board->getPreference( 'boards_mailing_list' ) ){
			require_once( UTIL_PKG_PATH.'mailman_lib.php' );
			if ( mailman_findmember($board->getPreference('boards_mailing_list'),$gBitUser->getField('email')) ){
				$ret = 'email';
			}else{
				$ret = 'none';
			}
		}
		// no mailing list then store in switchboard
		if ( $gBitSystem->isPackageActive('switchboard') ) {
			global $gSwitchboardSystem;
			if ( ($rslt = $gSwitchboardSystem->loadContentPrefs( $pUser->mUserId, $this->mContentId ) ) ){
				$ret =  $rslt[0]['delivery_style'];
			}
			else {
			  $ret = 'none';
			}
		}
		return $ret;
	}

	// -------------------- END Member Email Funtions -------------------- //




	// -------------------- Member Invitation Funtions -------------------- //

	function verifyInvitation( &$pParamHash ){
		$errors = array();
		if( @$this->verifyId( $pParamHash['group_id'] ) ) {
			$pParamHash['invite_store']['group_id'] = $pParamHash['group_id'];
		}else{
			$errors['group_id'] = "Invalid Group Id.";
		}

		if( !empty( $pParamHash['email'] ) ) {
			$pParamHash['invite_store']['email'] = $pParamHash['email'];
		}else{
			$errors['email'] = "No email address was set.";
		}

		$pParamHash['invite_store']['invite_id'] = $this->genInviteId();

		return( count( $errors ) == 0 );
	}

	function genInviteId(){
		global $gBitUser;
		$inviteId = $gBitUser->genPass(32);
		// lets make sure its unique
		if ( $this->mDb->getOne( "SELECT `invite_id` FROM `".BIT_DB_PREFIX."groups_invitations` WHERE `invite_id` = ?", array( $inviteId ) ) ){
			// if its already in use lets try again
			$inviteId = $this->genInviteId();
		}
		return $inviteId;		
	}

	function storeInvitation( &$pParamHash ){
        $result = FALSE;
        if( $this->verifyInvitation( $pParamHash ) ) {
            $this->mDb->StartTrans();
			$this->mDb->associateInsert( BIT_DB_PREFIX."groups_invitations", $pParamHash['invite_store'] );
            $this->mDb->CompleteTrans();

            // re-query to confirm results
            $result = $this->getInvitation( $pParamHash['invite_store']['invite_id'] );
        }
        return $result;
	}

	function getInvitation( &$pInviteId ){
        $ret = FALSE;
		if ( isset( $pInviteId ) ){
			$bindVars = array( $pInviteId );
			$query = "SELECT gi.*, uu.`user_id`, uu.`login`, uu.`real_name`
						FROM `".BIT_DB_PREFIX."groups_invitations` gi 
						LEFT OUTER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`email` = gi.`email`)
						WHERE `invite_id`=?";
			$result = $this->mDb->query( $query, $bindVars );
			if( $result && $result->numRows() ) {
				$ret = $result->fields;
			}
		}
		return $ret;
	}

	function getInvitationsList(){
		$ret = array();
		if( $this->isValid() ) {
			$bindVars = array( 'group_id' => $this->mGroupId );
			$query = "SELECT DISTINCT gi.`email`, gi.`invite_id`, uu.`user_id`, uu.`login`, uu.`real_name` 
					  FROM `".BIT_DB_PREFIX."groups_invitations` gi 
					  LEFT OUTER JOIN `".BIT_DB_PREFIX."users_users` uu ON (uu.`email` = gi.`email`)
					  WHERE gi.`group_id` = ? ORDER BY uu.`real_name`, uu.`login`, gi.`email`";
			$result = $this->mDb->query( $query, $bindVars );
			while ($res = $result->fetchrow()) {
				$ret[] = $res;
			};
		}
		return $ret;
	}

	function expungeInvitation( &$pInviteId ){
		$return = FALSE;
		if ( $this->isValid() && isset( $pInviteId ) ){
			$bindVars = array( $pInviteId );
			if ( $this->mDb->getOne("SELECT `invite_id` FROM `".BIT_DB_PREFIX."groups_invitations` WHERE `invite_id`=?", $bindVars ) ){
				$query = "DELETE FROM `".BIT_DB_PREFIX."groups_invitations` WHERE `invite_id`=?"; 
				$result = $this->mDb->query($query, $bindVars);
				$return = TRUE;
			}
		}
		return $return;
	}

	// -------------------- End Member Invitation Funtions -------------------- //




	// -------------------- Content Mapping Funtions -------------------- //

	/**
	 * linkContent
	 *
	 * @access public
	 * @return if errors
	 **/
	function linkContent( $pParamHash ) {
		if( $this->isValid() && isset( $pParamHash['content_id'] ) && $this->verifyId( $pParamHash['content_id'] ) ){
			if( $this->mDb->getOne( "SELECT `group_content_id` FROM `".BIT_DB_PREFIX."groups_content_cnxn_map` WHERE `group_content_id`=? AND `to_content_id`=?", array( $this->mContentId, $pParamHash['content_id'] ) ) ) {
				$query = "UPDATE `".BIT_DB_PREFIX."groups_content_cnxn_map` SET `to_title`= ? WHERE `group_content_id` = ? AND `to_content_id` = ? ";
			} else {
				$query = "INSERT INTO `".BIT_DB_PREFIX."groups_content_cnxn_map` ( `to_title`, `group_content_id`, `to_content_id` ) VALUES (?,?,?)";
			}
			if ( isset($pParamHash['title']) ){
				$toTitle = $pParamHash['title'];
			}else{
				$toContent = LibertyBase::getLibertyObject( $pParamHash['content_id'] );
				$toContent->load();
				$toTitle = $toContent->getTitle();
			}
			$result = $this->mDb->query( $query, array( $toTitle, $this->mContentId, $pParamHash['content_id'] ) );
		}
		return( count( $this->mErrors ) == 0 );
	}
	
	/**
	 * unlinkContent
	 *
	 * @access public
	 * @return if errors
	 * @TODO write this to work as a static function so we dont have to load up multiple groups to expunge a content item from all
	 **/
	function unlinkContent( $pParamHash ) {
		if( $this->isValid()  && isset( $pParamHash['content_id'] ) && $this->verifyId( $pParamHash['content_id'] ) ) {
			$this->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."groups_content_cnxn_map` WHERE `group_content_id`=? AND `to_content_id`=?", array( $this->mContentId, $pPramaHash['content_id'] ) );
		}
		return( count( $this->mErrors ) == 0 );
	}


	/**
	 * storeContentTypePref
	 *
	 * maps content type guid to a group to allow a group to create that content type
	 **/ 
	function storeContentTypePref( $pContentTypeGuid ){
		$bindVars = array( $this->mContentId, $pContentTypeGuid );
		if ( $this->isValid() && isset( $pContentTypeGuid ) && !$this->mDb->getOne("SELECT `group_content_id` FROM `".BIT_DB_PREFIX."groups_content_types` WHERE `group_content_id`=? AND `content_type_guid`=?", $bindVars ) ){
			$query = "INSERT INTO `".BIT_DB_PREFIX."groups_content_types`( `group_content_id`, `content_type_guid` ) VALUES(?, ?)"; 
			$result = $this->mDb->query($query, $bindVars);
		}
		return TRUE;
	}

	/**
	 * expungeContentTypePref
	 *
	 * removes a content type from the list of content types a group can create. does not expunge any existing content of the type being removed.
	 **/
	function expungeContentTypePref( $pContentTypeGuid ){
		$bindVars = array( $this->mContentId, $pContentTypeGuid );
		if ( $this->isValid() && isset( $pContentTypeGuid ) && $this->mDb->getOne("SELECT `group_content_id` FROM `".BIT_DB_PREFIX."groups_content_types` WHERE `group_content_id`=? AND `content_type_guid`=?", $bindVars ) ){
			$query = "DELETE FROM `".BIT_DB_PREFIX."groups_content_types` WHERE `group_content_id`=? AND `content_type_guid`=?"; 
			$result = $this->mDb->query($query, $bindVars);
		}
		return TRUE;
	}

	/**
	 * gets a list of content type that this group allows to be attached to it
	 */
	function getContentTypePrefs(){
		/**
		 * @TODO inclusion of bitboard is here so that group members can edit their boards
		 * However ideally we should only include bitboard when group_content_edit calls from a board edit process of the valid mapped board object
		 * that is to say an existing board that is already mapped to the group - e.g. the one we create for the group automagaically. But right
		 * now I'm too tired to work it out -wjames5
		 */
		$ret = array( 'bitboard' );
		if ( $this->isValid() ){
			if ( !empty( $this->mContentTypePrefs ) ){
				return $this->mContentTypePrefs;
			}else{
				$result = $this->mDb->query( "SELECT `content_type_guid` FROM  `".BIT_DB_PREFIX."groups_content_types` WHERE `group_content_id`=?", $this->mContentId );  
				while( $res = $result->fetchRow() ) {
					$ret[] = $res['content_type_guid'];
				}
			}
		}
		return $ret;
	}

	/**
	 * Format a hash of content types and related names
	 */
	function getContentTypeData(){
		global $gLibertySystem;
		$contentTypeData = array();
		if ( $this->isValid() ){
			if ( !empty( $this->mContentTypeData ) ){
				return $this->mContentTypeData;
			}else{
				$contentTypes = $this->getContentTypePrefs();
				foreach( $gLibertySystem->mContentTypes as $cType ) {
					if( in_array( $cType['content_type_guid'], $contentTypes ) ) {
						$contentTypeData[$cType['content_type_guid']]  = $cType['content_description'];
					}
				}
			}
		}
		return $contentTypeData;
	}

	/**
	 * get the group's affiliated baord
	 * 
	 * get the oldest board associated with the group, which was automagically created when 
	 * the group was created. If we ever want to support associating multiple boards 
	 * with a group then how to deal with that would have to be handled here.
	 */
	function getBoard(){
		if ( $this->isValid() && !is_object( $this->mBoardObj ) ){
			$listHash = array(
				"connect_group_content_id" => $this->mContentId,
				"content_type_guid" => "bitboard",
				"sort_mode" => "created_asc"
				);

			$boards = $this->getContentList( $listHash );

			if ( $listHash['cant'] && !empty( $boards[0]['board_id'] ) ){
				$boardId = $boards[0]['board_id'];
				require_once( BOARDS_PKG_PATH.'BitBoard.php' );
				$board = new BitBoard( $boardId );
				$board->load();
				$this->mBoardObj = &$board;
			}
		}

		return $this->mBoardObj;
	}

	/**
	 * Verify we can add the requested content type to the requested group. 
	 * This checks all necessary permissions and group preferences.
	 *
	 * @param LibertyContent Object $pContent - the content we wish to add to the group 
	 */
	function verifyLinkContentPermission( &$pContent ){
		global $gBitSystem;
		if ( !$this->isValid() ){
			$gBitSystem->setHttpStatus( 404 );
			$gBitSystem->fatalError( tra("The group you are trying to add content to could not be found.") );
		}
		if ( !$this->hasUserPermission( 'p_group_group_content_create' ) ){
			$gBitSystem->fatalError( tra("Sorry, you do not have permission to add content to the requested group") );
		}
		if ( !in_array( $pContent->mType['content_type_guid'], $this->mContentTypePrefs ) ){
			$gBitSystem->fatalError( tra("The content you requested can not be added to the group")." ".$this->mInfo['title'] );
		}
		return TRUE;
	}

	// -------------------- End Content Mapping Funtions -------------------- //




	// -------------------- Theme and Layout Funtions -------------------- //

	/**
	 * Groups can pick their own theme. Get it and set it.
	 */
	function setGroupStyle( $pContentId=NULL ){
		global $gBitThemes, $gBitSystem;
		if ($gBitSystem->isFeatureActive( 'group_themes' )){
			if ( $theme = $this->getPreference( 'theme', NULL, $pContentId ) ){
				$gBitThemes->setStyle( $theme );
			}
			if ( $theme_var = $this->getPreference( 'theme_variation', NULL, $pContentId ) ){
				$gBitSystem->setConfig( 'style_variation', $theme_var  );
			}
		}
	}

	/**
	 * Groups can add to their layout. Get their additions and add them to the base layout.
	 * We don't make them add base group layout items so that its less confusing for laypeople.
	 */
	function injectGroupLayoutModules(){
		global $gBitSmarty, $gBitThemes, $gCenterPieces;
		if ( $this->isValid() ){
			// Store the layout for use by the output filter.
			if( !empty($pContentId) ) {
				$_REQUEST['group_layout_id'] = $pContentId;
			}
			else {
				$_REQUEST['group_layout_id'] = $this->mContentId;
			}

			// need ref for our group menu if it needs to reference the group content object
			$gBitSmarty->assign_by_ref( 'groupContent', $this );

			/* custom query to get the group's layout modules 
			 * needed because BitTheme::getLayout forces defaults
			 * so with that we can never get an empty array, 
			 * which makes merging with defaults impossible.
			 * we rely on default because we want to allow the 
			 * site admin to insert whatever modules it wants
			 */
			$ret = array( 'l' => NULL, 'c' => NULL, 'r' => NULL, 't' => NULL, 'b' => NULL );
			$query =   "SELECT tl.*
						FROM `".BIT_DB_PREFIX."themes_layouts` tl
						WHERE  tl.`layout`=? ORDER BY ".$gBitThemes->mDb->convertSortmode( "pos_asc" );

			$result = $gBitThemes->mDb->query( $query, array(  "content_id.".$this->mContentId  ) );
			if( !empty( $result ) && $result->RecordCount() ) {
				$row = $result->fetchRow(); 
				while( $row ) {
					$row['module_params'] = $gBitThemes->parseString( $row['params'] );
					$row["visible"] = TRUE;

					if ( !is_array( $gCenterPieces ) ){ 
						$gCenterPieces = array();
					}
					if( $row['layout_area'] == CENTER_COLUMN ) {
						array_push( $gCenterPieces, $row );
					}

					if( empty( $ret[$row['layout_area']] )) {
						$ret[$row['layout_area']] = array();
					}
					array_push( $ret[$row['layout_area']], $row );

					$row = $result->fetchRow();
				}
			}
			$groupmodules = $ret;

			$merged = array();
			// the areas of the layout - so ugly
			$areas = array( 't', 'l', 'r', 'b', 'c' );
			foreach( $areas as $key ){
				$area1 = ( empty($groupmodules[$key]) || $groupmodules[$key] === NULL ) ? array() : $groupmodules[$key];
				$area2 = ( empty($gBitThemes->mLayout[$key] ) || $gBitThemes->mLayout[$key] === NULL ) ? array() : $gBitThemes->mLayout[$key];
				$merged[$key] = array_merge( $area2, $area1 );
			}
			$gBitThemes->mLayout = $merged; 
		}
	}
	
	// -------------------- End Theme and Layout Funtions -------------------- //
	
}


// -------------------- Service Funtions -------------------- //

function group_module_display(&$pParamHash){
	global $gBitThemes, $gBitSmarty, $gBitSystem, $gCenterPieces;
	if ( ACTIVE_PACKAGE == "group" && !empty( $_REQUEST['group_id'] ) ){
		/* @TODO this is being done for the group pkg itself which is dumb - we are loading the object twice. 
		 * We call it here since it is after layout gets loaded, but we should find a way to ref the group so we dont load it twice every freaking time.
		 */
		if ( $gBitSystem->isFeatureActive('group_layouts') ){
			$group = new BitGroup( $_REQUEST['group_id'] );
			$group->load();
			$group->injectGroupLayoutModules();
		}
	}
}

function group_content_list_sql( &$pObject, $pParamHash=NULL ) {
	global $gBitSystem;
	$ret = array();
	$ret['where_sql'] = "";
	if ( $gBitSystem->isPackageActive( 'group' ) && !empty($pParamHash['connect_group_content_id']) && $pObject->verifyId( $pParamHash['connect_group_content_id'] ) ){
		$ret['join_sql'] = " INNER JOIN `".BIT_DB_PREFIX."groups_content_cnxn_map` gccm ON ( lc.`content_id` = gccm.`to_content_id` )";
		if ( isset($pParamHash['content_type_guid']) && $pParamHash['content_type_guid'] == "bitboard" ){
			$ret['select_sql'] = " , brd.`board_id`";
			$ret['join_sql'] .= " INNER JOIN `".BIT_DB_PREFIX."boards` brd ON (lc.`content_id` = brd.`content_id`)";
		}
		$ret['where_sql'] .= " AND gccm.`group_content_id` = ? ";
		$ret['bind_vars'][] = (int)$pParamHash['connect_group_content_id'];
	}

	if ( isset($pParamHash['exclude_content_type_guid']) ){
		$ret['where_sql'] .= " AND lc.`content_type_guid` != ?";
		$ret['bind_vars'][] = $pParamHash['exclude_content_type_guid'];
	}
	return $ret;
}

function group_content_display( &$pObject, &$pParamHash ) {
	global $gBitSystem, $gBitSmarty, $gBitThemes;
	$listHash['mapped_content_id'] = $pObject->mContentId;
	$group = new BitGroup();
	$groups = $group->getList( $listHash );

	$group_id = NULL;
	if ( count( $groups ) == 1 ) {
		$group_id = $groups[0]['content_id'];
	} elseif ( count($groups) > 1 && !empty($_REQUEST['group_layout_id']) && is_numeric($_REQUEST['group_layout_id']) ) {
		// Is the group layout one of the groups this content is in?
		foreach ($groups as $grp) {
			if ($grp['content_id'] == $_REQUEST['group_layout_id']) {
				$group_id = $_REQUEST['group_layout_id'];
			}
		}
	}

	if( !empty($group_id) ) {
		// apply group theme
		$group->setGroupStyle( $group_id );
		
		// make data available to smarty as controlling group info for theming - similarly set in lookup_group_inc
		$gBitSmarty->assign_by_ref( 'controlGroupInfo', $groups[0] );

		// apply group layout
		if ( $gBitSystem->isFeatureActive('group_layouts') ){
			// we need to get the group pkg base layout first, this will make sure we dont use any other package as the base layout
			$gBitThemes->mLayout = NULL;
			$layout_name = "group";
			$layoutHash = array(
				'layout' => $layout_name,
				'fallback' => FALSE,
				);
			$gBitThemes->loadLayout( $layoutHash );
			// now we can add our custom groupcontent layout to it
			$group2 = new BitGroup ( null, $group_id );
			$group2->load();
			$group2->injectGroupLayoutModules();
		}
	}

	//vd( $groups );
	$pObject->mInfo['member_groups'] = $groups;
	$gBitSmarty->assign_by_ref( 'contentMemberGroups', $groups );
}

function group_content_preview( &$pObject) {
	global $gBitSystem;
	if ( $gBitSystem->isPackageActive( 'group' ) ) {		
		if (isset($_REQUEST['connect_group_content_id'])) {
			$pObject->mInfo['connect_group_content_id'] = $_REQUEST['connect_group_content_id'];
		}
	}
}

function group_content_edit( &$pObject, &$pParamHash ) {
	global $gBitSystem, $gBitSmarty, $gBitUser;
	$errors = NULL;
	if( $gBitSystem->isPackageActive( 'group' ) ){
		$connect_group_content_id = NULL;

		// when creating new content via a group we pass the group content id to the edit form
		if ( !empty( $_REQUEST['connect_group_content_id'] ) ) {
			$connect_group_content_id = $_REQUEST['connect_group_content_id'];
		}elseif( $pObject->isValid() ){
			/* when content is already assigned to a group we load up the first one into the form
			   this is to help content types like gmap which may have subcontent edit forms ajaxed in and need the group id
			   to keep the mappings of sub related content consistant. 
			   we only bother for the first group we find since we don't care to obsess about this but mostly want 
			   ensure this is right for groups that are asserting administrative control
			 */
			$listHash['mapped_content_id'] = $pObject->mContentId;
			$group = new BitGroup();
			$groups = $group->getList( $listHash );
			if ( count( $groups ) == 1 ) {
				$connect_group_content_id = $groups[0]['content_id'];
			}
		}
		if ( !empty( $connect_group_content_id ) ){
			$group2 = new BitGroup( NULL, $connect_group_content_id );
			$group2->load();
			$group2->verifyLinkContentPermission( $pObject );
			$group2->setGroupStyle();
			$gBitSmarty->assign( "connect_group_content_id", $group2->mContentId );

			// make data available to smarty as controlling group info for theming - similarly set in lookup_group_inc
			$gBitSmarty->assign_by_ref( 'controlGroupInfo', $group2->mInfo );

			// check if editing is shared
			if( !$pObject->isValid() ){
				// if the content is new and registered users can edit we assume its wiki like and check the box by default
				$groupId = 3;
				$assignedPerms[$groupId] = $gBitUser->getGroupPermissions( array( 'group_id' => $groupId ) );
			}else{
				$groupId = $group2->mGroupId;
				$assignedPerms = $pObject->getContentPermissionsList();
			}
			if( !empty( $assignedPerms[$groupId][$pObject->mEditContentPerm] ) ){
				$gBitSmarty->assign('groupEditShared', TRUE );
			}
		}
	}
}

function group_comment_store( &$pObject, &$pParamHash ) {
	global $gBitSystem, $gLibertySystem, $gBitUser;
	$errors = NULL;

	if( $gBitSystem->isPackageActive( 'group' ) && $gBitSystem->isPackageActive('switchboard') ) {
		if( $pObject->isValid() && $pObject->isContentType( BITCOMMENT_CONTENT_TYPE_GUID ) && $pObject->loadComment() && $pObject->mInfo['content_status_id'] == 50 ) {
			// load up the root, we need to know a few things
			$root = LibertyBase::getLibertyObject( $pParamHash['root_id'] );
			// if its a board and it does not have a mailing list in effect then we can send an email
			if( $root->mType['content_type_guid'] == 'bitboard' && !$root->getPreference( 'boards_mailing_list' ) ){

				// Get the groups the root is in
				$listHash['mapped_content_id'] = $pParamHash['root_id'];
				$group = new BitGroup();
				$groups = $group->getList( $listHash );

				// Get the link
				require_once( BOARDS_PKG_PATH.'BitBoardPost.php' );
				$post = new BitBoardPost( $pObject->mCommentId );
				$post->load();
				$link = BIT_BASE_URI.$post->getDisplayUrl();

				// some text we need
				$permaLink = BIT_BASE_URI.$pObject->getDisplayUrl( NULL, array('parent_id' => $pParamHash['root_id'], 'content_id'=>$pParamHash['content_id']) );
				$parseHash = $pParamHash['content_store'];
				$parseHash['uri_mode'] = TRUE;
				$parsedData = $pObject->parseData($parseHash);

				if (!empty($groups)) {
					foreach($groups as $group) {
						// Draft the message body:
						$body = tra('A new message was posted to the group').' '.$group['title'].'<br/><br/>'
								.tra('The message was posted here:').' '.$link.'<br/><br/><br/>'
								.'/----- '.tra('Here is the posted text').' -----/<br/><br/>'
								.$parsedData;

						global $gSwitchboardSystem;
						$gSwitchboardSystem->sendEvent('group', 
													   'message', 
													   $group['content_id'], 
													   array('subject' => tra('Group').': '.$group['title'].' : '.$pParamHash['title'], 'message' => $body)
													);
					}
				}
			}
		}
	}
}

function group_content_store( &$pObject, &$pParamHash ) {
	global $gBitSystem, $gLibertySystem, $gBitUser;
	$errors = NULL;

	if( $gBitSystem->isPackageActive( 'group' ) && !empty( $pParamHash['connect_group_content_id'] ) ) {
		$groupContent = new BitGroup( NULL, $pParamHash['connect_group_content_id'] );
		$groupContent->load();
		$groupContent->verifyLinkContentPermission( $pObject );
		$linkHash = array( 
						"content_id"=>$pParamHash['content_id'],
						"title"=>$pParamHash['title'],
					);
		if ( !$groupContent->linkContent( $linkHash ) ) {
			$errors=$groupContent->mErrors;
		}

		//----- end set access perms -----//
		/**
		 * Assign custom view content perm on the object to be mapped based on if group is public or not
		 * Assign custom edit contetn perm on the object to restrict access to the group for wiki like content
		 *
		 * @TODO This code is nearly identical to code in edit.php - may want to move into a group class method.
		 */
		$typeGuid = $pObject->mType['content_type_guid'];
		$contentId = $pObject->mContentId;;
		if ( !isset( $gLibertySystem->mContentTypes[$typeGuid]['content_perms'] ) ){
			$gLibertySystem->mContentTypes[$typeGuid]['content_perms'] = secure_get_content_permissions( $typeGuid );
		}
		if ( isset( $gLibertySystem->mContentTypes[$typeGuid]['content_perms']['view'] ) ){
			$viewPerm =  $gLibertySystem->mContentTypes[$typeGuid]['content_perms']['view'];
			$editPerm =  $gLibertySystem->mContentTypes[$typeGuid]['content_perms']['edit'];
			// foreach user group
			$groupsHash = array();
			$allGroups = $gBitUser->getAllGroups( $groupsHash );
			foreach( $allGroups as $groupId => $group ){
				$groupPerms = array_keys( $group['perms'] );
				// if group has content view perm by default and is not admin and not our group
				if ( $groupId != 1 && $groupId != $groupContent->mGroupId  && in_array( $viewPerm, $groupPerms ) ){
					if ( $groupContent->mInfo['view_content_public'] != 'y' ){
						// revoke
						$groupContent->storePermission( $groupId, $viewPerm, TRUE, $contentId);
					}else{
						// unrevoke if revoked
						$groupContent->removePermission( $groupId, $viewPerm, $contentId );
					}
				}
				// if group has content edit perm by default and is not admin, not editors, and not our group
				if ( $groupId != 1 && $groupId != 2  && $groupId != $groupContent->mGroupId  && in_array( $editPerm, $groupPerms ) ){
					// revoke to revoke wiki like editing - we never restore this
					$groupContent->storePermission( $groupId, $editPerm, TRUE, $contentId);
				}
			}
			
			// set custom view perm for our group
			if ( $groupContent->mInfo['view_content_public'] != 'y' ){
				// assign view to our group 
				$groupContent->storePermission( $groupContent->mGroupId, $viewPerm, FALSE, $contentId );
			}else{
				// remove custom view perm for our group since its not needed
				$groupContent->removePermission( $groupContent->mGroupId, $viewPerm, $contentId );
			}

			// set custom edit perm for our group for wiki like editing among group members
			if ( $pParamHash['group_share_edit'] == 'y' ){
				// assign edit to our group 
                $groupContent->storePermission( $groupContent->mGroupId, $editPerm, FALSE, $contentId );
			}else{
				// remove custom edit perm for our group if revoked
				$groupContent->removePermission( $groupContent->mGroupId, $editPerm, $contentId );
			}
		}
		//----- end set access perms -----//

	}
	return( $errors );
}

function group_content_expunge( &$pObject, &$pParamHash ) {
	global $gBitSystem, $gBitDb;
	$errors = NULL;
	if( $gBitSystem->isPackageActive( 'group' ) ) {
		$groups = $gBitDb->getArray( "SELECT g.`group_id` FROM `".BIT_DB_PREFIX."groups` g INNER JOIN `".BIT_DB_PREFIX."groups_content_cnxn_map` gccm ON ( gccm.`group_content_id` = g.`content_id` ) WHERE gccm.`to_content_id` = ?", array( $pObject->mContentId ) );
		foreach( $groups as $group ){
			$group = new BitGroup( $group['group_id'] );
			$unlinkHash = array( "content_id"=>$pObject->mContentId );
			if ( !$group->unlinkContent( $unlinkHash ) ) {
				$errors=$group->mErrors;
			}
		}
	}
	return( $errors );
}

function group_content_user_perms( &$pObject, $pParamHash ) {
	global $gBitUser;
	if ( $gBitUser->isRegistered() ){
		$userId = $gBitUser->mUserId;
		$contentId = $pObject->mContentId;

		// Need a different query for groups
		if ( $pObject->mContentTypeGuid == BITGROUP_CONTENT_TYPE_GUID ) {

			$query = "SELECT rpm.`perm_name` AS `hash_key`, rpm.`perm_name`, g.`group_id`, ugm.`user_id`  FROM `".BIT_DB_PREFIX."groups_roles_perms_map` rpm ".
				"LEFT JOIN `".BIT_DB_PREFIX."groups_roles_users_map` rum ON ( rpm.`role_id` = rum.`role_id` AND rpm.`group_content_id` = rum.`group_content_id`) ".
				"LEFT JOIN `".BIT_DB_PREFIX."groups` g ON (rpm.`group_content_id` = g.`content_id` ) ".
				"LEFT OUTER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON (g.`group_id` = ugm.`group_id` AND ugm.`user_id` = ?) ".
				"WHERE rpm.`group_content_id` = ? AND (rum.`user_id` = ? OR rpm.`role_id` = 3)";

		} else {

			$query = "SELECT rpm.`perm_name` AS `hash_key`, rpm.*, ccm.*, ugm.*, rpm.`perm_name`, rpm.`group_id` FROM `".BIT_DB_PREFIX."groups_roles_perms_map` rpm ".
				"LEFT JOIN `".BIT_DB_PREFIX."groups_roles_users_map` rum ON (rpm.`role_id` = rum.`role_id` AND rpm.`group_content_id` = rum.`group_content_id` ) ".
				"LEFT JOIN `".BIT_DB_PREFIX."groups_content_cnxn_map` ccm ON (rpm.`group_content_id` = ccm.`group_content_id` ) ".
				"LEFT OUTER JOIN `".BIT_DB_PREFIX."users_groups_map` ugm ON (rpm.`group_id` = ugm.`group_id` AND ugm.`user_id` = ?) ".
				"WHERE  ccm.`to_content_id` = ? AND (rum.`user_id` = ? OR rpm.`role_id` = 3)";

		}
		$perms = $pObject->mDb->getAssoc($query, array($userId, $contentId, $userId));

		// Add the admin permission for this content type if appropriate
		if( isset($perms['p_group_group_content_admin'] ) && $pObject->mContentTypeGuid != BITGROUP_CONTENT_TYPE_GUID ) {
			$perms[$pObject->mAdminContentPerm] = array('perm_name'=>$pObject->mAdminContentPerm, 'user_id' => $userId);
		}

		// grant admin comments if member has permission
		if( !empty($perms['p_group_group_msgs_admin'] ) ){
			$perms['p_boards_post_edit'] = array( 'perm_name'=>'p_boards_post_edit', 'user_id'=> $userId );
			$perms['p_liberty_edit_comments'] = array( 'perm_name'=>'p_liberty_edit_comments', 'user_id'=> $userId );
			$perms['p_liberty_admin_comments'] = array( 'perm_name'=>'p_liberty_admin_comments', 'user_id'=> $userId );
		}

		// restore comment posting permission if revoked but user has role
		if ( ( $pObject->isOwner() || !empty( $perms['p_group_group_msgs_create'] ) ) && empty( $pObject->mUserContentPerms['p_liberty_post_comments'] ) ){
			$perms['p_liberty_post_comments'] = array( 'perm_name'=>'p_liberty_post_comments', 'user_id'=> $userId );
		}

		if ( !isset($pObject->mUserContentPerms) ) {
			$pObject->mUserContentPerms = $perms;
		} elseif ( !empty($perms) ){
			$pObject->mUserContentPerms = array_merge($pObject->mUserContentPerms, $perms);
		}
	}
}

?>
