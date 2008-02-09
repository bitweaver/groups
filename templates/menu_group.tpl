{strip}
	<ul>
		{if $gBitUser->hasPermission( 'p_group_view')}
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}index.php">{tr}Groups Home{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_group_view')  || $gBitUser->hasPermission( 'p_group_remove' ) }
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}list_groups.php">{tr}List Groups{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_group_create' ) || $gBitUser->hasPermission( 'p_group_edit' ) }
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}edit.php">{tr}Create Group{/tr}</a></li>
		{/if}
		{if $gContent->mGroupId && $gBitUser->hasPermission( 'p_group_view')}
			<li><hr/></li>
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}index.php?group_id={$gContent->mGroupId}" title="comingsoon">{tr}Home{/tr}</a></li>
			<li><a class="item" href="" title="comingsoon">{tr}Forum{/tr}</a></li>
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}members.php?group_id={$gContent->mGroupId}" title="comingsoon">{tr}Members{/tr}</a></li>
			<li><a class="item" href="" title="comingsoon">{tr}Content{/tr}</a></li>
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}files.php?group_id={$gContent->mGroupId}" title="comingsoon">{tr}Files{/tr}</a></li>
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}about.php?group_id={$gContent->mGroupId}" title="comingsoon">{tr}About this group{/tr}</a></li>
			<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}join.php?group_id={$gContent->mGroupId}" title="comingsoon">{tr}Join this group{/tr}</a></li>
			{if $gBitUser->isAdmin() }
				<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}manage.php?group_id={$gContent->mGroupId}" title="comingsoon">{tr}Manage members{/tr}</a></li>
			{/if}
			{if $gBitUser->isAdmin() }
				<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}invite_members.php?group_id={$gContent->mGroupId}" title="comingsoon">{tr}Invite members{/tr}</a></li>
			{/if}
			{if $gBitUser->isAdmin() || $gBitUser->hasPermission( 'p_group_edit' ) }
				<li><a class="item" href="{$smarty.const.GROUP_PKG_URL}edit.php?group_id={$gContent->mGroupId}" title="comingsoon">{tr}Group settings{/tr}</a></li>
			{/if}
		{/if}
	</ul>
{/strip}
