{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$gContent->mInfo}
<div class="display group">
	<div class="floaticon">
		{if $print_page ne 'y'}
			{if $gBitUser->hasPermission( 'p_group_edit' )}
				<a title="{tr}Edit this group{/tr}" href="{$smarty.const.GROUP_PKG_URL}edit.php?group_id={$gContent->mInfo.group_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="Edit Group"}</a>
			{/if}
			{if $gBitUser->hasPermission( 'p_group_remove' )}
				<a title="{tr}Remove this group{/tr}" href="{$smarty.const.GROUP_PKG_URL}remove_group.php?group_id={$gContent->mInfo.group_id}">{biticon ipackage="icons" iname="edit-delete" iexplain="Remove Group"}</a>
			{/if}
		{/if}<!-- end print_page -->
	</div><!-- end .floaticon -->

	<div class="header">
		<h1>{$gContent->mInfo.title|escape}</h1>
	</div><!-- end .header -->

	<div class="body">
		<div class="content">
			<h2>About this group</h2>
			<div class="row">
				{formlabel label="Members"}
				<div class="forminput">{$gContent->mInfo.num_members}</div>
			</div>
			<div class="row">
				{formlabel label="Description"}
				<div class="forminput">{$gContent->mInfo.parsed_data}</div>
			</div>
			<div class="row">
				{formlabel label="Created by"}
				<div class="forminput">{displayname user=$gContent->mInfo.creator_user user_id=$gContent->mInfo.creator_user_id real_name=$gContent->mInfo.creator_real_name} on <strong>{$gContent->mInfo.created|bit_long_date}</strong></div>
			</div>
			<div class="row">
				{formlabel label="Homepage URL"}
				<div class="forminput"><a href="{$smarty.const.BIT_BASE_URI}{$gContent->getDisplayUrl()}">{$smarty.const.BIT_BASE_URI}{$gContent->getDisplayUrl()}</a></div>
			</div>
			{if $gBitUser->isInGroup( $gContent->mGroupId ) && $boardsMailingList}
			<div class="row">
				{formlabel label="Group Email Address"}
				<div class="forminput">
					{$boardsMailingList}
					{formhelp note="You can post messages to this groups's forum by sending mail to this address"}
				</div>
			</div>
			{/if}
			<!-- @TODO 
			<div class="row">
				{formlabel label="RSS Feed"}
				<div class="forminput">@TODO link to board messages feed here</div>
			</div>
			-->
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .group -->
{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$gContent->mInfo}
