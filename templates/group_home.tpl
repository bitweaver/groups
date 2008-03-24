{* $Header: /cvsroot/bitweaver/_bit_groups/templates/group_home.tpl,v 1.6 2008/03/24 14:40:30 wjames5 Exp $ *}
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
				{if $memberGroups}
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

						{foreach from=$memberGroups key=groupId item=memberGroup}
							<tr class="{cycle values="odd,even"}">
								<td>
									{if $memberGroup.group_home}<a href="{$smarty.const.GROUP_PKG_URL}index.php?group_id={$memberGroup.group_id}" title="Group home page" />{/if}<strong>{$memberGroup.group_name}</strong>{if $memberGroup.group_home}</a>{/if}
								</td>
								<td>
									{$memberGroup.group_desc}
								</td>

								<td  style="text-align:center;">
									{$memberGroup.num_members}
								</td>
							</tr>
						{/foreach}
					</table>
					
					{pagination}
				</div>
				{else}
					<em>You currently do not belong to any groups</em>
				{/if}
			{/if}
			{if $recentGroups}
				<h2>Search Groups</h2>
				{minifind sort_mode=$sort_mode}
				<h2>New Groups</h2>

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

						{foreach from=$recentGroups key=groupId item=recentGroup}
							<tr class="{cycle values="odd,even"}">
								<td>
									{if $recentGroup.group_home}<a href="{$smarty.const.GROUP_PKG_URL}index.php?group_id={$recentGroup.group_id}" title="Group home page" />{/if}<strong>{$recentGroup.group_name}</strong>{if $recentGroup.group_home}</a>{/if}
								</td>
								<td>
									{$recentGroup.group_desc}
								</td>

								<td  style="text-align:center;">
									{$recentGroup.num_members}
								</td>
							</tr>
						{/foreach}
					</table>
					
					{pagination}
				</div>
			{/if}
		</div><!-- end .content -->
	</div><!-- end .body -->
</div>
{/strip}
