{* $Header: /cvsroot/bitweaver/_bit_groups/templates/edit_group.tpl,v 1.2 2008/02/04 19:09:10 nickpalmer Exp $ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin group">
	{if $preview}
		<h2>Preview {$gContent->mInfo.title|escape}</h2>
		<div class="preview">
			{include file="bitpackage:group/group_display.tpl" page=`$gContent->mInfo.group_id`}
		</div>
	{/if}

	<div class="header">
		<h1>
			{if $gContent->mInfo.group_id}
				{tr}{tr}Edit{/tr} {$gContent->mInfo.title|escape}{/tr}
			{else}
				{tr}Create New Record{/tr}
			{/if}
		</h1>
	</div>

	<div class="body">
		{form enctype="multipart/form-data" id="editgroupform"}
			{jstabs}
				{jstab title="Group Information"}
					{legend legend="Group Information"}
						<input type="hidden" name="group[group_id]" value="{$gContent->mInfo.group_id}" />

						<div class="row">
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" size="60" maxlength="200" name="group[title]" id="title" value="{$gContent->mInfo.title|escape}" />
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Description" for="summary"}
							{forminput}
								<input size="60" type="text" name="summary" id="summary" value="{$gContent->mInfo.summary|escape}" />
								{formhelp note="Brief description of Group."}
							{/forminput}
						</div>

						{textarea name="group[edit]" help="The description of the group or other group message."}{$gContent->mInfo.data}{/textarea}
						{textarea name="group[after_registration]" noformat=true help="The message shown after a user registers. If none is provided then the user will be sent to the group directly." id="after_reg" label="After Registration Message"}{$gContent->mInfo.after_registration}{/textarea}

						{* any simple service edit options *}
						{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_mini_tpl}

						<div class="row submit">
							<input type="submit" name="preview" value="{tr}Preview{/tr}" /> 
							<input type="submit" name="save_group" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{jstab title="Group Options"}
					{include file="bitpackage:group/edit_group_options.tpl"}
				{/jstab}

				{* any service edit template tabs *}
				{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_tab_tpl}
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .group -->

{/strip}
