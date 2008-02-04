{* $Header: /cvsroot/bitweaver/_bit_groups/templates/Attic/list_groups.tpl,v 1.2 2008/02/04 19:09:10 nickpalmer Exp $ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="listing group">
	<div class="header">
		<h1>{tr}Group Records{/tr}</h1>
	</div>

	<div class="body">
		{minifind sort_mode=$sort_mode}

		{form id="checkform"}
			<input type="hidden" name="offset" value="{$control.offset|escape}" />
			<input type="hidden" name="sort_mode" value="{$control.sort_mode|escape}" />

			<table class="data">
				<tr>
					{if $gBitSystem->isFeatureActive( 'group_list_group_id' ) eq 'y'}
						<th>{smartlink ititle="Group Id" isort=group_id offset=$control.offset iorder=desc idefault=1}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'group_list_title' ) eq 'y'}
						<th>{smartlink ititle="Title" isort=title offset=$control.offset}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'group_list_description' ) eq 'y'}
						<th>{smartlink ititle="Description" isort=description offset=$control.offset}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'group_list_data' ) eq 'y'}
						<th>{smartlink ititle="Text" isort=data offset=$control.offset}</th>
					{/if}

					{if $gBitUser->hasPermission( 'p_group_remove' )}
						<th>{tr}Actions{/tr}</th>
					{/if}
				</tr>

				{foreach item=group from=$groupsList}
					<tr class="{cycle values="even,odd"}">
						{if $gBitSystem->isFeatureActive( 'group_list_group_id' )}
							<td><a href="{$smarty.const.GROUP_PKG_URL}index.php?group_id={$group.group_id|escape:"url"}" title="{$group.group_id}">{$group.group_id}</a></td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'group_list_title' )}
							<td>{$group.title|escape}</td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'group_list_description' )}
							<td>{$group.description|escape}</td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'group_list_data' )}
							<td>{$group.data|escape}</td>
						{/if}

						{if $gBitUser->hasPermission( 'p_group_remove' )}
							<td class="actionicon">
								{smartlink ititle="Edit" ifile="edit.php" ibiticon="icons/accessories-text-editor" group_id=$group.group_id}
								<input type="checkbox" name="checked[]" title="{$group.title|escape}" value="{$group.group_id}" />
							</td>
						{/if}
					</tr>
				{foreachelse}
					<tr class="norecords"><td colspan="16">
						{tr}No records found{/tr}
					</td></tr>
				{/foreach}
			</table>

			{if $gBitUser->hasPermission( 'p_group_remove' )}
				<div style="text-align:right;">
					<script type="text/javascript">/* <![CDATA[ check / uncheck all */
						document.write("<label for=\"switcher\">{tr}Select All{/tr}</label> ");
						document.write("<input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form.id,'checked[]','switcher')\" /><br />");
					/* ]]> */</script>

					<select name="submit_mult" onchange="this.form.submit();">
						<option value="" selected="selected">{tr}with checked{/tr}:</option>
						{if $gBitUser->hasPermission( 'p_group_remove' )}
							<option value="remove_groups">{tr}remove{/tr}</option>
						{/if}
					</select>

					<noscript><div><input type="submit" value="{tr}Submit{/tr}" /></div></noscript>
				</div>
			{/if}
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
