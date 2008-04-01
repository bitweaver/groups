{* $Header: /cvsroot/bitweaver/_bit_groups/templates/group_add_content.tpl,v 1.1 2008/04/01 20:59:10 wjames5 Exp $ *}
{strip}
<div class="display group">
	<div class="header">
		<h1>{tr}Submit Content to Group{/tr}</h1>
	</div>

	<div class="body">
		<div class="content">
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
							<th>{tr}Action{/tr}</th>
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
									<a href="{$smarty.const.GROUP_PKG_URL}add_to_group.php?group_id={$memberGroup.group_id}&amp;submit_content_id={$submit_content_id}">Submit</a>
								</td>
							</tr>
						{/foreach}
					</table>
					
					{pagination}
				</div>
			{elseif $recentGroups}
					<em>You currently do not belong to any groups. You must first join a group before you can submit content to it.</em>
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
