{strip}
{legend legend="Group Options"}
	{foreach from=$formGroupOptions key=item item=output}
		<div class="row">
			{formlabel label=`$output.label` for=$item}
			{forminput}
				<input type="checkbox" name="group[{$item}]" value="y" {if $gBitContent->mInfo.$item|default:$output.default == "y"}checked{/if} id="{$item}" />
				{formhelp note=`$output.note` page=`$output.page`}
			{/forminput}
		</div>
	{/foreach}
{/legend}
{/strip}
