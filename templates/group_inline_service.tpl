{strip}
{if count($contentMemberGroups)>0 && $smarty.const.ACTIVE_PACKAGE != 'group'}
	<div class="group-permalink">
		<strong>{tr}In Group{if count($contentMemberGroups) > 1 }}s{/if}{/tr}:</strong>&nbsp;
	{foreach from=$contentMemberGroups item=group}
	<a href="{$group.group_home}">{$group.group_name}</a>&nbsp;
	{/foreach}
	</div>
{/if}
{/strip}
