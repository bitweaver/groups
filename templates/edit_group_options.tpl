{strip}
{legend legend="Group Options"}
	{foreach from=$formGroupOptions key=item item=output}
		<div class="form-group">
			{formlabel label=$output.label for=$item}
			{forminput}
				<input type="checkbox" name="group[{$item}]" value="y" {if $gContent->mInfo.$item|default:$output.default == "y"}checked{/if} id="{$item}" />
				{formhelp note=$output.note page=$output.page}
                {if $item eq 'mod_msgs' && $mailinglist_pwd}
                    {formhelp note="<strong>You can moderate message by using mailinglist password: `$mailinglist_pwd`</strong"}
                {/if}
			{/forminput}
		</div>
	{/foreach}
{/legend}
{if $formGroupContent.guids}
	{legend legend="Group Content"}
		<div class="form-group">
			{formlabel label="Allowed Content Types"}
			{forminput}
				{html_checkboxes options=$formGroupContent.guids name=group_content separator="<br />" checked=$formGroupContent.checked}
				{formhelp note="You can select what content types your group members can create and associate with their group."}
			{/forminput}
		</div>
	{/legend}
{/if}
{/strip}
