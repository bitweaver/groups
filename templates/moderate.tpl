{strip}
{form}
	<div class="row">
		{if !empty($moderation.request)}
			{if $moderation.type=='add_content' && $moderation.data.map_content_id}
				<strong>{tr}Request{/tr}:</strong> {$moderation.data.content_name} <a href="{$smarty.const.BIT_ROOT_URL}index.php?content_id={$moderation.data.map_content_id}">{$moderation.data.title}</a> {tr}submitted to group{/tr}.<br />
			{else}
				<strong>{tr}Request{/tr}:</strong> {$moderation.request|escape:html}<br/>
			{/if}
				<strong>{tr}Requesting User{/tr}:</strong> {displayname user_id=$moderation.source_user_id}<br/>
		{/if}
	</div>
	<input type="hidden" name="group_id" value="{$gContent->mInfo.group_id}" />
	<input type=hidden name=moderation_id value="{$moderation.moderation_id}" />
	<div class="row reply">
		{$moderation.reply|escape:html}
		{if $moderation.responsible == 0}
			Add a Reply (optional):<br />
			<textarea name="reply" id="reply-{$moderation.moderation_id}"></textarea>
		{/if}
	</div>
	<div class="row submit">
		{foreach from=$moderation.transitions item=transition}
			<input type=submit name="transition" value="{$transition}" />&nbsp;
		{/foreach}
	</div>
{/form}
{/strip}
 
