{* $Header: /cvsroot/bitweaver/_bit_groups/templates/edit_group.tpl,v 1.1 2008/01/24 14:47:56 wjames5 Exp $ *}
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
				{jstab}
					{legend legend="Edit/Create Group Record"}
						<input type="hidden" name="group[group_id]" value="{$gContent->mInfo.group_id}" />

						<div class="row">
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" size="60" maxlength="200" name="group[title]" id="title" value="{$gContent->mInfo.title|escape}" />
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Description" for="description"}
							{forminput}
								<input size="60" type="text" name="group[description]" id="description" value="{$gContent->mInfo.description|escape}" />
								{formhelp note="Brief description of the page."}
							{/forminput}
						</div>

						{textarea name="group[edit]"}{/textarea}

						{* any simple service edit options *}
						{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_mini_tpl}

						<div class="row submit">
							<input type="submit" name="preview" value="{tr}Preview{/tr}" /> 
							<input type="submit" name="save_group" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{* any service edit template tabs *}
				{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_tab_tpl}
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .group -->

{/strip}
