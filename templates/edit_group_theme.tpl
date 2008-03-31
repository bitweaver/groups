{strip}

<div class="admin themes">
	<div class="header">
		<h1> {tr}Group Theme Manager{/tr}</h1>
	</div>

	<div class="body">
		{if $approve}
			<div id="themeapprove">
				<h1>{tr}Confirm Selection{/tr}</h1>
				<p>{tr}The settings you have chosen has not been applied to the site yet. This allows you to test the styles before applying them to your site. To accept the change, please click on the accept button below{/tr}<p>
				<a href="{$smarty.const.GROUP_PKG_URL}theme.php">{biticon ipackage=icons iname="large/dialog-cancel" iexplain="Cancel"}</a>
				<a href="{$smarty.const.GROUP_PKG_URL}theme.php?group_id={$gContent->mGroupId}&amp;group_style={$smarty.request.group_style}&amp;style_variation={$smarty.request.style_variation}&amp;approved=1">{biticon ipackage=icons iname="large/dialog-ok" iexplain="Accept"}</a>
			</div>
		{/if}

		{jstabs}
			{jstab title="Site Style"}
				{legend legend="Pick Group Style"}
					<ul class="data">
						{foreach from=$stylesList item=s}
							<li class="{cycle values='odd,even"} item">
								<h2 {if $style eq $s.style}class="highlight"{/if}>
									{if $style eq $s.style}
										{biticon ipackage="icons" iname="dialog-ok" iexplain="Current Style"}&nbsp;
									{/if}
									<a href="{$smarty.const.GROUP_PKG_URL}theme.php?group_id={$gContent->mGroupId}&amp;group_style={$s.style}">{$s.style|replace:"_":" "}</a>
								</h2>

								{if $s.style_info.preview}
									<a href="{$smarty.const.GROUP_PKG_URL}theme.php?group_id={$gContent->mGroupId}&amp;group_style={$s.style}">
										<img class="thumb" src="{$s.style_info.preview}" alt="{tr}Theme Preview{/tr}" title="{$s.style}" />
									</a>
								{/if}

								{$s.style_info.description}
								{if $s.alternate}
									<h3>{tr}Variations of this style{/tr}</h3>
									<ul>
										{foreach from=$s.alternate key=variation item=d}
											<li><a href="{$smarty.const.GROUP_PKG_URL}theme.php?group_id={$gContent->mGroupId}&amp;group_style={$s.style}&amp;style_variation={$variation}">{$variation|replace:"_":" "}</a></li>
										{/foreach}
									</ul>
								{/if}

								<div class="clear"></div>
							</li>
						{/foreach}
					</ul>
				{/legend}
			{/jstab}
		{/jstabs}
	</div> <!-- end .body -->
</div>  <!-- end .themes -->

{/strip}
