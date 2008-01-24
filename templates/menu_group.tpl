{strip}
	<ul>
		{if $gBitUser->hasPermission( 'p_group_read')}
			<li><a class="item" href="{$smarty.const.GROUPS_PKG_URL}index.php">{tr}Groups Home{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_group_read')  || $gBitUser->hasPermission( 'p_group_remove' ) }
			<li><a class="item" href="{$smarty.const.GROUPS_PKG_URL}list_groups.php">{tr}List Groups{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_group_create' ) || $gBitUser->hasPermission( 'p_group_edit' ) }
			<li><a class="item" href="{$smarty.const.GROUPS_PKG_URL}edit.php">{tr}Create Group{/tr}</a></li>
		{/if}
	</ul>
{/strip}
