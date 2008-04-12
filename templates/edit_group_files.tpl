{* $Header: /cvsroot/bitweaver/_bit_groups/templates/edit_group_files.tpl,v 1.2 2008/04/12 22:39:44 wjames5 Exp $ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin group">
	<div class="header">
		<h1>
			{$gContent->mInfo.title|escape} {tr}Files{/tr}
		</h1>
	</div>

 	{if $errors}
 		{formfeedback warning=`$errors`}
 	{/if}

	<div class="body">
		{if $fileList}
			<table class="data">
				<tr>
					<th style="width:50%;">{tr}File{/tr}</th>
					<th style="width:20%;">{tr}Type{/tr}</th>
					<th style="width:10%;">{tr}Size{/tr}</th>
				</tr>
				{foreach item=item from=$fileList}
					<tr class="{cycle values="even,odd"}" >
						<td style="text-align:left;">
							<a href="{$smarty.const.BIT_ROOT_URI}{$item.storage_path}">{$item.filename}</a>
						</td>
						<td style="text-align:left;">
							{$item.mime_type}
						</td>
						<td style="text-align:center;">
							{$item.file_size|display_bytes}
						</td>
						<td style="text-align:center;">
							{$item.modifier_user}
						</td>
					</tr>
				{/foreach}
			</table>
		{/if}
		{if $gContent->hasEditPermission() || $gContent->hasUserPermission('p_group_group_att_upload') } 
			{form enctype="multipart/form-data" id="editgroupform"}
				<input type="hidden" name="group_id" value="{$gContent->mGroupId}" />
				{legend legend="Upload File"}
					{include file="bitpackage:liberty/edit_storage.tpl"}
				{/legend}
				<div class="row submit">
					<input type="submit" name="save_group" value="{tr}Upload{/tr}" />
				</div>
			{/form}
		{/if}
	</div><!-- end .body -->
</div><!-- end .group -->
{/strip}
