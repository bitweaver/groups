{strip}
{if $connect_group_content_id}
	<input type="hidden" name="connect_group_content_id" value="{$connect_group_content_id}" />

	{if !$gContent->isValid() || $gContent->isOwner() || $gContent->hasAdminPermission()}
	<div class="control-group">
		<label class="checkbox">
			<input type="checkbox" name="group_share_update" value="y" {if $groupUpdateShared}checked="checked"{/if} />Allow Fellow Group Members To Edit
			{formhelp note="Checking this box will allow fellow group users to edit this content."}
		</label>
	</div>
	{/if}
{/if}
{/strip}
