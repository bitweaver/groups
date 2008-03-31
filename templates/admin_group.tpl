{strip}
{form}
	{jstabs}
		{jstab title="Group Options"}
			{legend legend="Group Content"}
				<div class="row">
					{formlabel label="Allowed Content Types"}
					{forminput}
						{html_checkboxes options=$formGroupContent.guids name=group_content separator="<br />" checked=$formGroupContent.checked}
						{formhelp note="You can select what content types groups can create and associate with their group. This creates a default list of allowed content. Additionally, groups can't individually limit from this list what content types their group members can create.."}
					{/forminput}
				</div>
			{/legend}
			{legend legend="Display Options"}
				<p>
					{tr}Content that is associated with a group or groups can display a list of those groups on the content object's page and in lists. Choose any one or more of the following to display permalinks to groups a content obj is mapped to.{/tr}
				</p>
				{foreach from=$formGroupServiceDisplayOptions key=item item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$item}
						{forminput}
							{if $output.type == 'numeric'}
								<input size="5" type='text' name="{$item}" id="{$item}" value="{$gBitSystem->getConfig($item,$output.default)}" />
							{elseif $output.type == 'input'}
								<input type='text' name="{$item}" id="{$item}" value="{$gBitSystem->getConfig($item,$output.default)}" />
							{else}
								{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
							{/if}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
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
			{/legend}
		{/jstab}
	{/jstabs}
	<div class="row submit">
		<input type="submit" name="group_preferences" value="{tr}Change Preferences{/tr}" />
	</div>
{/form}
{/strip}
