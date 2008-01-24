{strip}
{form}
	{jstabs}
		{jstab title="Home Group"}
			{legend legend="Home Group"}
				<input type="hidden" name="page" value="{$page}" />
				<div class="row">
					{formlabel label="Home Group (main group)" for="homeGroup"}
					{forminput}
						<select name="homeGroup" id="homeGroup">
							{section name=ix loop=$groups}
								<option value="{$groups[ix].group_id|escape}" {if $groups[ix].group_id eq $home_group}selected="selected"{/if}>{$groups[ix].title|escape|truncate:20:"...":true}</option>
							{sectionelse}
								<option>{tr}No records found{/tr}</option>
							{/section}
						</select>
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="homeTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="List Settings"}
			{legend legend="List Settings"}
				<input type="hidden" name="page" value="{$page}" />
				{foreach from=$formGroupLists key=item item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$item}
						{forminput}
							{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}

				<div class="row submit">
					<input type="submit" name="listTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}
	{/jstabs}
{/form}
{/strip}
