{* $Header: /cvsroot/bitweaver/_bit_groups/templates/group_home.tpl,v 1.3 2008/02/11 01:47:31 wjames5 Exp $ *}
{strip}
<div class="display group">
	<div class="header">
		<h1>{tr}Groups{/tr}</h1>
	</div>

	<div class="body">
		<div class="content">
			{if !$gBitUser->isRegistered()}
				<h2>Create a Group in Three East Steps</h2>
				<div>
					<ul>
						<li>1 Register</li>
						<li>2 Create a Group</li>
						<li>3 Invite People</li>
					</ul>
					<p><a href="{$smarty.const.USERS_PKG_URL}register.php">{tr}register{/tr}</a></p>
				</div>
			{else}
				{if $gBitUser->hasPermission( 'p_group_edit' ) }
					<p><a class="item" href="{$smarty.const.GROUP_PKG_URL}edit.php">{tr}Create a Group{/tr}</a></p>
				{/if}
				<h2>Your Groups</h2>

				<div>
					<table class="data">
						<tr>
							<th style="text-align:left;">
								<a href="{$smarty.const.USERS_PKG_URL}my_groups.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'group_name_desc'}group_name_asc{else}group_name_desc{/if}">{tr}Name{/tr}</a> 
							</th>
							<th>
								{tr}Description{/tr}</a> 
							</th>
							{if $gBitUser->hasPermission( 'p_users_assign_group_members' )}
								<th>{tr}Members{/tr}</th>
							{/if}
							{if $gBitUser->hasPermission( 'p_users_assign_group_perms' )}
								<th>{tr}Permissions{/tr}</th>
							{/if}
							<th>{tr}Action{/tr}</th>
						</tr>

						{foreach from=$groups key=groupId item=group}
							<tr class="{cycle values="odd,even"}">
								<td>
									<strong>{$group.group_name}</strong>{if $group.is_default eq 'y'}<em class="warning"> *{tr}Default group{/tr}*</em>{/if}
								</td>
								<td>
									{$group.group_desc}<br />
									{if $group.group_home}{tr}Home Page{/tr}:<strong> {$group.group_home}</strong><br />{/if}
									{if $group.included}
										{tr}Included Groups{/tr}
										<ul>
											{foreach from=$group.included key=incGroupId item=incGroupName}
												<li>{$incGroupName}</li>
											{/foreach}
										</ul>
									{/if}
								</td>

								{if $gBitUser->hasPermission( 'p_users_assign_group_members' )}
									<td  style="text-align:center;">
											{foreach from=$groupUsers key=userId item=user}
											&nbsp;{displayname hash=$user}<br />
										{foreachelse}
											<strong>{tr}none{/tr}</strong>
										{/foreach}
									</td>
								{/if}

								{if $gBitUser->hasPermission( 'p_users_assign_group_perms' )}
									<td style="text-align:center;">
										{foreach from=$group.perms key=permName item=perm}
											&nbsp;{$perm.perm_desc}<br />
										{foreachelse}
											<strong>{tr}none{/tr}</strong>
										{/foreach}
									</td>
								{/if}

								<td class="actionicon">
									<a href="{$smarty.const.USERS_PKG_URL}my_groups.php?group_id={$groupId}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="edit"}</a>
									{if $groupId ne -1}{* sorry for hardcoding, really need php define ANONYMOUS_GROUP_ID - spiderr *}
										<a href="{$smarty.const.USERS_PKG_URL}my_groups.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;action=delete&amp;group_id={$groupId}" 
										onclick="return confirm('{tr}Are you sure you want to delete this group?{/tr}')">{biticon ipackage="icons" iname="edit-delete" iexplain="Delete Group"}</a>
									{/if}
								</td>
							</tr>
						{/foreach}
					</table>

					{pagination}
				</div>
			{/if}
			<div class="listing group">
				<h2>Search Groups</h2>
				{minifind sort_mode=$sort_mode}
				<p>@TODO insert list of new public groups</p>
			</div>
		</div><!-- end .content -->
	</div><!-- end .body -->
</div>
{/strip}
