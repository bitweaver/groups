{strip}
{legend legend="Group Administration"}
	<p>{tr}You can fine tune administration of your group by assigning permissions to different roles. In the membership administration panel you can assign members to more advanced roles like Group Manager or Group Administrator{/tr}</p>
	<table class="data">
		{capture assign=th}
			<tr>
				<th>{tr}Permissions{/tr}</th>
				{foreach from=$groupRoles item=role name=role}
					<th><abbr title="{$role.role_name}">{if $smarty.foreach.roles.total > 8}{$role.role_id}{else}{$role.role_desc}{/if}</abbr></th>
				{/foreach}
			</tr>
		{/capture}
		{$th}
		{foreach from=$allRolesPerms item=perm key=p name=perms}
			{if ($smarty.foreach.perms.iteration % 10) eq 0 and ($smarty.foreach.perms.total - $smarty.foreach.perms.iteration) gt 5}{$th}{/if}
			<tr class="{cycle values="odd,even"}">
				<td>{$perm}</td>
				{foreach from=$groupRoles item=role}
					<td class="aligncenter">
						<input type="checkbox" name="group[perms][{$role.role_id}][{$p}]" title="{$role.role_name}"
							{if $role.role_id eq 1 } checked="checked" disabled="disabled" {elseif $role.perms.$p} checked="checked" {/if}/>
					</td>
				{/foreach}
			</tr>
		{/foreach}
	</table>
{/legend}
{/strip}
