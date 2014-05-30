{strip}
	{form id="list-query-form" legend="Search" ipackage=group ifile="search.php"}

	{if $smarty.request.search_group_content_id}
		<div class="form-group">
			{formlabel label="Content Types:" for="content_type_guid"}
			{forminput}
				{html_options options=$contentTypes name="content_type_guid[]" id=content_type selected=$contentSelect size=5 multiple=TRUE}
				{formhelp note="Limit search by content type"}
			{/forminput}
		</div>
	{/if}

		<div class="form-group">
			{formlabel label="Find (key word or phrase):" for="find"}
			{forminput}
				<input type="text" name="find" value="{$find|default:$smarty.request.find|default:$prompt|escape}" {if $prompt}onclick="if (this.value == '{$prompt}') this.value = '';"{/if}/>&nbsp;
				{formhelp note="Searches titles"}
			{/forminput}
		</div>

		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='search'}

		<div class="form-group submit">
			{* requires gmap and geo packages - we send search request to gmap to display results on a map *}
			{if $listInfo && $gBitSystem->isPackageActive( 'gmap' ) }
				<div style="float:right">
					<a href="{pageurl listInfo=$listInfo}&amp;display_mode=map" title="View all that have location data">{tr}View Results on a Map{/tr}</a>
				</div>
			{/if}

			<input type="submit" class="btn btn-default" name="search" value="{tr}Search{/tr}"/>
		</div>
	{/form}
{/strip}
