{* $Header$ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin group">
	<div class="header">
		<h1>{if $gContent->mInfo.thumbnail_url}<img class="thumb" style="vertical-align:middle;" src="{$gContent->mInfo.thumbnail_url.avatar}" alt="Group Image" title="{$gContent->mInfo.title|escape}" />&nbsp;{/if}
			{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<h2>{tr}Files{/tr}</h2>
	</div>

 	{if $errors}
 		{formfeedback warning=`$errors`}
 	{/if}

	<div class="body">
		{if $fileList}
			<table class="data">
				<tr>
					<th style="width:40%;">{tr}File{/tr}</th>
					<th style="width:10%;">{tr}Type{/tr}</th>
					<th style="width:10%;">{tr}Size{/tr}</th>
					<th style="width:20%;">{tr}Last Modified{/tr}</th>
					<th style="width:20%;">{tr}Uploaded by{/tr}</th>
				</tr>
				{foreach item=item from=$fileList}
					<tr class="{cycle values="even,odd"}" >
						<td style="text-align:left;">
							<a href="{$smarty.const.BIT_ROOT_URI}{$item.storage_path}">{$item.filename}</a>
						</td>
						<td style="text-align:center;">
							{$item.mime_type}
						</td>
						<td style="text-align:center;">
							{$item.file_size|display_bytes}
						</td>
						<td style="text-align:right;">
							{$item.last_modified|bit_short_datetime}
						</td>
						<td style="text-align:center;">
							{$item.user_id}
						</td>
					</tr>
				{/foreach}
			</table>
		{/if}
		{if $gContent->hasUpdatePermission() || $gContent->hasUserPermission('p_group_group_att_upload') } 
			{assign var="formid" value="editgroupform"}
			{form enctype="multipart/form-data" id=$formid}
				<input type="hidden" name="group_id" value="{$gContent->mGroupId}" />
				<input type="hidden" name="content_id" value="{$gContent->mContentId}" />
				<input type="hidden" name="title" value="{$gContent->getTitle()}" />
				{legend legend="Upload File"}
					{include file="bitpackage:liberty/edit_storage.tpl" formid=$formid}
				{/legend}
				<div class="control-group submit">
					<input type="submit" class="btn" name="save_group" value="{tr}Upload{/tr}" />
				</div>
			{/form}
		{/if}
	</div><!-- end .body -->
</div><!-- end .group -->
{/strip}
