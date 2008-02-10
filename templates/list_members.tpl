{strip}
<div class="floaticon">
	<a href="{$smarty.const.USERS_PKG_URL}admin/edit_group.php">
		{biticon ipackage="icons" iname="system-users" iexplain="Group List"}
	</a>
	{bithelp}
</div>

<div class="listing users">
	<div class="header">
		<h1>{tr}Group Members{/tr}: {$groupInfo.group_name}</h1>
	</div>

	<div class="body">
		{formfeedback success=$successMsg error=$errorMsg}

		<ol class="data">
			{foreach from=$groupMembers key=userId item=member}
				<li>{displayname hash=$member}
					{if $gBitUser->isAdmin()}
					&nbsp;<a href="{$smarty.const.USERS_PKG_URL}admin/assign_user.php?action=removegroup&amp;group_id={$gContentId->mGroupId}&amp;assign_user={$member.user_id}">{biticon ipackage="icons" iname="edit-delete" iexplain="remove from group"}</a>
					{/if}
				</li>
			{foreachelse}
				<li>{tr}The group has no members.{/tr}</li>
			{/foreach}
		</ol>
	</div><!-- end .body -->
</div>
{/strip}