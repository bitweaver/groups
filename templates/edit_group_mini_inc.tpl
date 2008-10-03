{strip}
{if $connect_group_content_id}
	<input type="hidden" name="connect_group_content_id" value="{$connect_group_content_id}" />

	{if !$gContent->isValid() || $gContent->isOwner() || $gContent->hasAdminPermission()}
	<div class="row">
		{formlabel label="Allow Fellow Group Members To Edit" for="group_share_edit"}
		{forminput}
			<input type="checkbox" name="group_share_edit" value="y" {if $groupEditShared}checked="checked"{/if} />
			{formhelp note="Checking this box will allow fellow group users to edit this content."}
		{/forminput}
	</div>
	{/if}
{/if}
{/strip}
