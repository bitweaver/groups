{strip}
	<ul>
		{if $gBitUser->hasPermission( 'p_group_view')}
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}index.php">{tr}Groups Home{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_group_view')}
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}list_groups.php">{tr}List Groups{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_group_edit' )}
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}edit.php">{tr}Create Group{/tr}</a></li>
		{/if}
		{if !$groupContent}
			{assign var="groupContent" value=$gContent}
		{/if}
		{if $groupContent->mGroupId && $groupContent->hasUserPermission( 'p_group_view', TRUE, TRUE)}
			<li><hr/><h3>{$groupContent->getTitle()}</h3></li>
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}index.php?group_id={$groupContent->mGroupId}">{tr}Home{/tr}</a></li>
			<li><a class="item" href="{$smarty.const.BOARDS_PKG_URL}index.php?b={$board_id}" title="{tr}post messages{/tr}">{tr}Forum{/tr}</a></li>
			{if $groupContent->hasUserPermission( 'p_group_group_members_view' )}
				<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}members.php?group_id={$groupContent->mGroupId}" title="{tr}view group members{/tr}">{tr}Members{/tr}</a></li>
			{/if}

			{if $groupContent->mContentTypeData}
			<li><a class="item" href="#">{tr}Content{/tr}</a>
				<ul>
					{foreach item=name key=type from=$groupContent->mContentTypeData}
					<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}index.php?group_id={$groupContent->mGroupId}&content_type_guid={$type}">{tr}{$name}{/tr}</a></li>
					{if $contentTypeEditUrl && $name == $contentTypeDesc && $groupContent->hasUserPermission( 'p_group_group_content_create' )}
						<li><a class="item" href="{$contentTypeEditUrl}">{tr}Add a {$name}{/tr}</a></li>
						{/if}
					{/foreach}
				</ul>
			</li>
			{/if}

			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}files.php?group_id={$groupContent->mGroupId}" title="{tr}view attachments{/tr}">{tr}Files{/tr}</a></li>
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}about.php?group_id={$groupContent->mGroupId}">{tr}About this group{/tr}</a></li>
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}join.php?group_id={$groupContent->mGroupId}">{if !$gBitUser->isInGroup( $groupContent->mGroupId )}{tr}Join this group{/tr}{else}{tr}Edit my membership{/tr}{/if}</a></li>
			{if $groupContent->hasAdminPermission() || $groupContent->hasUserPermission('p_group_group_members_admin')}
				<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}manage.php?group_id={$groupContent->mGroupId}" title="assign roles">{tr}Manage members{/tr}</a></li>
				<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}tasks.php?group_id={$groupContent->mGroupId}">{tr}Management Tasks{/tr}</a></li>
			{/if}
			{if $groupContent->hasAdminPermission() || $groupContent->hasEditPermission() }
				<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}invite_members.php?group_id={$groupContent->mGroupId}">{tr}Invite members{/tr}</a></li>
				<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}edit.php?group_id={$groupContent->mGroupId}">{tr}Group settings{/tr}</a></li>
			{/if}
			{if ($gBitSystem->isFeatureActive( 'group_themes' ) || $gBitSystem->isFeatureActive( 'group_layouts' )) && ($groupContent->hasAdminPermission() || $groupContent->hasEditPermission()) }
				<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}theme.php?group_id={$groupContent->mGroupId}">{tr}Group styles{/tr}</a></li>
			{/if}
		{/if}
	</ul>
{/strip}
