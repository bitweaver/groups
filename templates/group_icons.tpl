<div class="floaticon">
	{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon' serviceHash=$gContent->mInfo}

	{if $print_page ne 'y'}
		{if $gContent->hasUpdatePermission()}
			<a title="{tr}Edit this group{/tr}" href="{$smarty.const.GROUP_PKG_URL}edit.php?group_id={$gContent->mInfo.group_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="Edit Group"}</a>
		{/if}
		{if $gContent->hasUserPermission( 'p_group_remove' ) || $gContent->isOwner()}
			<a title="{tr}Remove this group{/tr}" href="{$smarty.const.GROUP_PKG_URL}remove_group.php?group_id={$gContent->mInfo.group_id}">{biticon ipackage="icons" iname="edit-delete" iexplain="Remove Group"}</a>
		{/if}
	{/if}<!-- end print_page -->
</div><!-- end .floaticon -->
