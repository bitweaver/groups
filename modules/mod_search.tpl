{strip}
{if !$groupContent}
	{assign var="groupContent" value=$gContent}
{/if}
{if $gBitSystem->isPackageActive( 'group' ) && $groupContent && $groupContent->isValid() && $groupContent->mContentTypeGuid == 'bitgroup'}
	{bitmodule title="$moduleTitle" name="group_search"}
		{form method="get" ipackage=group ifile="search.php"}
			<input type="hidden" name="search_group_content_id" value="{$groupContent->mContentId}" />
			<div class="row">
				<input name="find" size="15" type="text" accesskey="s" value="{tr}Search this group{/tr}" onblur="if (this.value == '') {ldelim}this.value = '{tr}Search this group{/tr}';{rdelim}" onfocus="if (this.value == '{tr}Search this group{/tr}') {ldelim}this.value = '';{rdelim}" />
			</div>

			<div class="row submit">
				<input type="submit" name="search" value="{tr}Search{/tr}" />
			</div>
		{/form}
	{/bitmodule}
{/if}
{/strip}
