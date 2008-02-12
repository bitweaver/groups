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
		<h1>{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<p>{$gContent->mInfo.summary|escape}</p>
	</div><!-- end .header -->

	<div class="body">
		<div class="content">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$gContent->mInfo}
		</div><!-- end .content -->
	</div><!-- end .body -->
	{$boardBlock}
</div><!-- end .group -->
{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$gContent->mInfo}
