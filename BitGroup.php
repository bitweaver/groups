<?php
/**
* $Header
* $Id: BitGroup.php
*/

/**
* Group class 
* builds on core bitweaver functionality, such as the Liberty CMS engine
*
* @date created 2008/1/23
* @author wjames <will@tekimaki.com> spider <spider@viovio.com>
* @version 
* @class BitGroup
*/

require_once( LIBERTY_PKG_PATH.'LibertyAttachable.php' );

/**
* This is used to uniquely identify the object
*/
define( 'BITGROUP_CONTENT_TYPE_GUID', 'bitgroup' );

/**
 * @package group
 */
class BitGroup extends LibertyAttachable {
	/**
	* Primary key
	* @public
	*/
	var $mGroupId;

	/**
	* During initialisation, be sure to call our base constructors
	**/
	function BitGroup( $pGroupId=NULL, $pContentId=NULL ) {
		LibertyAttachable::LibertyAttachable();
		$this->mGroupId = (int)$pGroupId;
		$this->mContentId = (int)$pContentId;
		$this->mContentTypeGuid = BITGROUP_CONTENT_TYPE_GUID;
		$this->registerContentType( BITGROUP_CONTENT_TYPE_GUID, array(
			'content_type_guid' => BITGROUP_CONTENT_TYPE_GUID,
			'content_description' => 'Group package with bare essentials',
			'handler_class' => 'BitGroup',
			'handler_package' => 'group',
			'handler_file' => 'BitGroup.php',
			'maintainer_url' => 'http://www.bitweaver.org'
		) );
		// Permission setup
		$this->mViewContentPerm  = 'p_group_view';
		$this->mEditContentPerm  = 'p_group_edit';
		$this->mAdminContentPerm = 'p_group_admin';
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

			$query = "SELECT s.*, lc.*, " .
			"uue.`login` AS modifier_user, uue.`real_name` AS modifier_real_name, " .
			"uuc.`login` AS creator_user, uuc.`real_name` AS creator_real_name " .
			"$selectSql " .
			"FROM `".BIT_DB_PREFIX."groups` s " .
			"INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = s.`content_id` ) $joinSql" .
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
				
				$this->getMemberPermsForGroup();

				LibertyAttachable::load();
			}
		}
		return( count( $this->mInfo ) );
	}

	/**
	* Any method named Store inherently implies data will be written to the database
	* @param pParamHash be sure to pass by reference in case we need to make modifcations to the hash
	* This is the ONLY method that should be called in order to store( create or update )an group!
	* It is very smart and will figure out what to do for you. It should be considered a black box.
	*
	* @param array pParams hash of values that will be used to store the page
	*
	* @return bool TRUE on success, FALSE if store could not occur. If FALSE, $this->mErrors will have reason why
	*
	* @access public
	**/
	function store( &$pParamHash ) {
		global $gBitUser;
		$this->mDb->StartTrans();

		// Verify and then store group and content.
		if( $this->verify( $pParamHash ) && $gBitUser->storeGroup( $pParamHash ) && LibertyAttachable::store( $pParamHash ) ) {
			$table = BIT_DB_PREFIX."groups";
			if( $this->mGroupId ) {
				$locId = array( "group_id" => $pParamHash['group_id'] );
				$result = $this->mDb->associateUpdate( $table, $pParamHash['group_pkg_store'], $locId );
			}
			else {
				// Get content
				$pParamHash['group_pkg_store']['content_id'] = $pParamHash['content_id'];
				$pParamHash['group_pkg_store']['group_id'] = $pParamHash['group_store']['group_id'];
				$this->mGroupId = $pParamHash['group_store']['group_id'];
				$result = $this->mDb->associateInsert( $table, $pParamHash['group_pkg_store'] );
				// Make sure this user is in the group
				$gBitUser->addUserToGroup( $gBitUser->mUserId, $this->mGroupId );
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
		$pParamHash['home'] = GROUP_PKG_URL.urlencode($pParamHash['name']);

		// Do we have after_registration data?
		if( !empty($pParamHash['after_registration']) ) {
			$pParamHash['data_store']['after_registration'] = $pParamHash['after_registration'];
			$pParamHash['after_registration_page'] = $pParamHash['home'] = GROUP_PKG_URL.'registered/'.$pParamHash['name'];
		}
		else {
			$pParamHash['data_store']['after_registration'] = NULL;
			$pParamHash['after_registration_page'] = GROUP_PKG_URL.urlencode($pParamHash['name']);
		}

		// Make sure we don't set is_default or batch_set_default for security
		if( isset($pParamHash['is_default']) || isset($pParamHash['batch_set_default']) ) {
			$this->mErrors['default'] = tra('Attempt to set group as default group or batch set default. This is not allowed.');
		}

		// Verify the group information
		$gBitUser->verifyGroup( $pParamHash );

		// Merge errors from the group checks
		$this->mErrors = array_merge($gBitUser->mErrors, $this->mErrors);

		vd($pParamHash);

		return( count( $this->mErrors )== 0 );
	}

	/**
	* This function removes a group entry
	**/
	function expunge() {
		$ret = FALSE;
		if( $this->isValid() ) {
			$this->mDb->StartTrans();
			$query = "DELETE FROM `".BIT_DB_PREFIX."groups` WHERE `content_id` = ?";
			$result = $this->mDb->query( $query, array( $this->mContentId ) );
			if( LibertyAttachable::expunge() ) {
				if( $gBitUser->removeGroup($this->mGroupId) ) {
					$ret = TRUE;
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
		return( $this->verifyId( $this->mGroupId ) );
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

		if( is_array( $find ) ) {
			// you can use an array of pages
			$whereSql .= " AND lc.`title` IN( ".implode( ',',array_fill( 0,count( $find ),'?' ) )." )";
			$bindVars = array_merge ( $bindVars, $find );
		} elseif( is_string( $find ) ) {
			// or a string
			$whereSql .= " AND UPPER( lc.`title` )like ? ";
			$bindVars[] = '%' . strtoupper( $find ). '%';
		}

		$query = "SELECT ts.*, lc.`content_id`, lc.`title`, lc.`data` $selectSql
			FROM `".BIT_DB_PREFIX."groups` ts INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = ts.`content_id` ) $joinSql
			WHERE lc.`content_type_guid` = ? $whereSql
			ORDER BY ".$this->mDb->convertSortmode( $sort_mode );
		$query_cant = "select count(*)
				FROM `".BIT_DB_PREFIX."groups` ts INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON( lc.`content_id` = ts.`content_id` ) $joinSql
			WHERE lc.`content_type_guid` = ? $whereSql";
		$result = $this->mDb->query( $query, $bindVars, $max_records, $offset );
		$ret = array();
		while( $res = $result->fetchRow() ) {
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

	function getRoles() {
        $sql = "SELECT gr.* FROM `".BIT_DB_PREFIX."groups_roles` gr 
                ORDER BY gr.`role_name` ASC";
        $ret = array();
        if ( $rolls = $this->mDb->query( $sql ) ){
            while( $row = $rolls->fetchRow() ) {
				$roleId = $row['role_id'];
				$ret[$roleId] = $row;
				$ret[$roleId]['perms'] = array();
				if ( @BitBase::verifyId( $this->mContentId ) ){
					$ret[$roleId]['perms'] = $this->getRolesPerms( array( 'content_id' => $this->mContentId ));
				}
            }
        }
		return $ret;
	}

	/**
	 * @param array group_content_id, if unset, all groups are perms are returned
	 **/
	function getRolesPerms( $pParamHash = NULL ) {
		$result = array();
		$bindVars = array();
		$whereSql = $selectSql = $fromSql = '';
		if( @BitBase::verifyId( $pParamHash['content_id'] )) {
			$selectSql = ', rp.`perm_value` AS `hasPerm` ';
			$fromSql = ' INNER JOIN `'.BIT_DB_PREFIX.'groups_roles_perms_map` rp ON ( rp.`perm_name` = gp.`perm_name` ) ';
			$whereSql .= " WHERE ugp.`group_content_id`=?";
			$bindVars[] = $pParamHash['contet_id'];
		}
		$sql = "SELECT gp.* $selectSql
		   		FROM `".BIT_DB_PREFIX."groups_permissions` gp $fromSql $whereSql
				ORDER BY gp.`perm_name` ASC";
		$result = $this->mDb->getAssoc( $sql, $bindVars );
		return $result;
	}

	function assignPermissionToRoll() {
	}

	function removePermissionFromRoll() {
	}

	function getMemberPermsForGroup(){
		if ( $this->verifyId( $this->mContentId ) ){
			$query = "SELECT p.`perm_name`, rp.`role_id`, ru.`role_id` AS user_role_id 
				FROM `".BIT_DB_PREFIX."groups_permissions` p
				LEFT JOIN `".BIT_DB_PREFIX."groups_roles_perms_map` rp ON (p.`perm_name` = rp.`perm_name`)
				LEFT JOIN `".BIT_DB_PREFIX."groups_roles_users_map` ru ON (rp.`group_content_id` = ru.`group_content_id` AND rp.`role_id` = ru.`role_id`) 
				WHERE rp.`group_content_id` IS NULL OR rp.`group_content_id` = ?";
			 $this->mGroupMemberPermissions = $this->mDb->getAssoc( $query, array( $this->mContentId ) ); 
		}
	}
}
?>
