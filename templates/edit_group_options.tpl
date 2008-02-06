{strip}
{legend legend="Group Options"}
	{foreach from=$formGroupOptions key=item item=output}
		<div class="row">
			{formlabel label=`$output.label` for=$item}
			{forminput}
				{html_checkboxes name="$item" values="y" checked=$gBitContent->mInfo.$item|default:$output.default labels=false id=$item}
				{formhelp note=`$output.note` page=`$output.page`}
			{/forminput}
		</div>
	{/foreach}
{/legend}
{/strip}
