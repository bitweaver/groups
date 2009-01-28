{if $groupSearchTitle && $smarty.request.search_group_content_id && !($smarty.request.content_type_guid eq 'bitgroup')}
	{if $smarty.const.ACTIVE_PACKAGE != 'group'}
		<div class="row">
			{formlabel label="Group:" for="search_group_content_id"}
				{forminput}
				{$groupSearchTitle}
				<input type="hidden" name="search_group_content_id" value="{$smarty.request.search_group_content_id}" />
				{formhelp note="Only show content associated with this group." }
			{/forminput}
		</div>
	{else}
		<input type="hidden" name="search_group_content_id" value="{$smarty.request.search_group_content_id}" />
	{/if}
{/if}
