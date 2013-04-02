{strip}
{if $connect_group_content_id}
	<input type="hidden" name="connect_group_content_id" value="{$connect_group_content_id}" />

	{if !$gContent->isValid() || $gContent->isOwner() || $gContent->hasAdminPermission()}
	<div class="control-group">
		{formlabel label="Allow Fellow Group Members To Edit" for="group_share_update"}
		{forminput}
			<input type="checkbox" name="group_share_update" value="y" {if $groupUpdateShared}checked="checked"{/if} />
			{formhelp note="Checking this box will allow fellow group users to edit this content."}
		{/forminput}
	</div>
	{/if}
{/if}
{/strip}
