{strip}
<div class="admin group">
	<div class="header">
		<h1>{if $gContent->mInfo.thumbnail_url}<img class="thumb" style="vertical-align:middle;" src="{$gContent->mInfo.thumbnail_url.avatar}" alt="Group Image" title="{$gContent->mInfo.title|escape}" />&nbsp;{/if}
			{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<h2>Manage Members</h2>
	</div><!-- end .header -->

	<div class="body">
		<div class="content">
			{form enctype="multipart/form-data" id="editmemberroles"}
			{legend legend="Member Administration"}
				<input type="hidden" name="group[group_id]" value="{$gContent->mInfo.group_id}" />
				<p>{tr}You can give your group members different roles, and then assign different administrative permissions to those roles in your Group Settings{/tr}</p>
				<div class="form-group">
					<table class="table data">
						{capture assign=th}
							<tr>
								<th>{tr}Group Member{/tr}</th>
								{foreach from=$groupRoles item=role name=role}
									<th><abbr title="{$role.role_name}">{if $smarty.foreach.roles.total > 8}{$role.role_id}{else}{$role.role_desc}{/if}</abbr></th>
								{/foreach}
								<th>{tr}Remove{/tr}</th>
							</tr>
						{/capture}
						{$th}
						{foreach from=$groupMembers item=user key=name name=users}
							{if ($smarty.foreach.users.iteration % 10) eq 0 and ($smarty.foreach.users.total - $smarty.foreach.users.iteration) gt 5}{$th}{/if}
							<tr class="{cycle values="odd,even"}">
								<td>{$name}</td>
								{foreach from=$groupRoles item=role key=r}
									<td style="text-align:center;">
										<input type="checkbox" value="{$user.user_id}" name="users[{$role.role_id}][{$user.user_id}]" title="Add {$user.user_name} to {$role.role_desc}"
										{if $role.role_id eq 3 || ($role.role_id eq 1 && $gContent->mInfo.user_id eq $user.user_id)} checked="checked" disabled="disabled" {elseif $r|in_array:$user.roles} checked="checked" {/if}/>
									</td>
								{/foreach}
									<td style="text-align:center;">
										{if $role.role_id eq 3 &&  $user.user_id != $gContent->mInfo.user_id }
										<a href="{$smarty.const.GROUP_PKG_URL}manage.php?action=removeuser&amp;group_id={$gContent->mGroupId}&amp;assign_user={$user.user_id}">{booticon iname="icon-trash" ipackage="icons" iexplain="remove from group"}</a>
										{/if}
									</td>
							</tr>
						{/foreach}
					</table>
				</div>
				<div class="form-group submit">
					<input type="submit" class="btn btn-default" name="save_roles" value="{tr}Save{/tr}" />
				</div>
			{/legend}
			{/form}
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .group -->
{/strip}
