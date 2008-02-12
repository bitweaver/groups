{* $Header: /cvsroot/bitweaver/_bit_groups/templates/group_home.tpl,v 1.5 2008/02/12 18:01:07 wjames5 Exp $ *}
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
								<a href="{$smarty.const.GROUP_PKG_URL}index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'group_name_desc'}group_name_asc{else}group_name_desc{/if}">{tr}Group{/tr}</a> 
							</th>
							<th>
								{tr}Description{/tr}</a> 
							</th>
							<th>{tr}Members{/tr}</th>
						</tr>

						{foreach from=$groups key=groupId item=group}
							<tr class="{cycle values="odd,even"}">
								<td>
									{if $group.group_home}<a href="{$smarty.const.GROUP_PKG_URL}index.php?group_id={$group.group_id}" title="Group home page" />{/if}<strong>{$group.group_name}</strong>{if $group.group_home}</a>{/if}
								</td>
								<td>
									{$group.group_desc}
								</td>

								<td  style="text-align:center;">
									{$group.num_members}
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
