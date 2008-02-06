{strip}
{legend legend="Group Administration"}
	<table class="data">
		{capture assign=th}
			<tr>
				<th style="width:1%"></th>
				<th>{tr}Permissions{/tr}</th>
				{foreach from=$groupRolls item=role name=role}
					<th><abbr title="{$role.role_name}">{if $smarty.foreach.roles.total > 8}{$role.role_id}{else}{$role.role_desc}{/if}</abbr></th>
				{/foreach}
			</tr>
		{/capture}
		{$th}
		{foreach from=$groupRollsPerms item=perm key=p name=perms}
			{if ($smarty.foreach.perms.iteration % 10) eq 0 and ($smarty.foreach.perms.total - $smarty.foreach.perms.iteration) gt 5}{$th}{/if}
			<tr class="{cycle values="odd,even"}{if $unassignedPerms.$p} warning{/if}">
				<td>{if $unassignedPerms.$p}{biticon iname=dialog-warning iexplian="Unassigned Permission"}{/if}</td>
				<td>{$perm.perm_desc}</td>
				{foreach from=$groupRolls item=role}
					<td style="text-align:center;">
						<input type="checkbox" value="{$perm.perm_name}" name="perms[{$role.role_id}][{$perm.perm_name}]" title="{$role.role_name}" {if $role.perms.$p}checked="checked"{/if}/>
					</td>
				{/foreach}
			</tr>
		{/foreach}
	</table>
{/legend}
{/strip}
