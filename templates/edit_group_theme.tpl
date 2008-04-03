{strip}

<div class="admin themes">
	<div class="header">
		<h1> {tr}Group Styles Manager{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}
		{if $approve}
			<div id="themeapprove">
				<h1>{tr}Confirm Selection{/tr}</h1>
				<p>{tr}The settings you have chosen has not been applied to the site yet. This allows you to test the styles before applying them to your site. To accept the change, please click on the accept button below{/tr}<p>
				<a href="{$smarty.const.GROUP_PKG_URL}theme.php">{biticon ipackage=icons iname="large/dialog-cancel" iexplain="Cancel"}</a>
				<a href="{$smarty.const.GROUP_PKG_URL}theme.php?group_id={$gContent->mGroupId}&amp;group_style={$smarty.request.group_style}&amp;style_variation={$smarty.request.style_variation}&amp;approved=1">{biticon ipackage=icons iname="large/dialog-ok" iexplain="Accept"}</a>
			</div>
		{/if}

		{jstabs}
			{if $gBitSystem->isFeatureActive( 'group_layouts' )}
				{jstab title="Layout Options"}
					{form action=$smarty.server.PHP_SELF legend="Assign column modules"}

						<input type="hidden" name="group_id" value="{$gContent->mGroupId}" />
						<table class="data">
							<thead>
								<tr style="text-align:left;">
									<th>{tr}Modules{/tr}</th>
									<th>{tr}Location{/tr}</th>
									<th>{tr}Position{/tr}</th>
									<th>{tr}Rows{/tr}</th>
									<th>{tr}Cache Time{/tr}</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$allModules item=modules key=package}
								{cycle values="odd,even" assign="pkgClass"}
									<tr class="{$pkgClass} data">
										<td colspan="4"><strong>{$package}</strong></td>
									</tr>
									{foreach from=$modules item=name key=tpl}
										<tr class="{$pkgClass} data">
											<td>{$name}
												<input type="hidden" name="fAssign[{$name}][module_rsrc]" value="{$tpl}" />
												{if $assignedModules.$tpl}
													<input type="hidden" name="fAssign[{$name}][module_id]" value="{$assignedModules.$tpl.module_id}" />
												{/if}
											</td>
											<td>
												<select name="fAssign[{$name}][layout_area]">
													{assign var='layout_area' value='unassign'}
													{if $assignedModules.$tpl}
													{assign var='layout_area' value=$assignedModules.$tpl.layout_area}
													{/if}
													<option value="unassign" {if $layout_area eq 'unassign'}selected="selected"{/if}>{tr}Do not display{/tr}</option>
													<option value="l" {if $layout_area eq 'l'}selected="selected"{/if}>{tr}Left column{/tr}</option>
													<option value="r" {if $layout_area eq 'r'}selected="selected"{/if}>{tr}Right column{/tr}</option>
												</select>
											</td>
											<td>
												<input type="text" size="5" name="fAssign[{$name}][pos]" id="pos" value="{$assignedModules.$tpl.pos|escape}" />
											</td>
											<td>
												<input type="text" size="5" name="fAssign[{$name}][module_rows]" id="module_rows" value="{$assignedModules.$tpl.module_rows|escape}" />
											</td>
											<td>
												<input type="text" size="5" name="fAssign[{$name}][cache_time]" id="cache_time" value="{$assignedModules.$tpl.cache_time|escape}" /> seconds
											</td>
										</tr>
									{/foreach}
								{/foreach}
							</tbody>
						</table>

						<div class="row submit">
							<input type="submit" name="submitcolumns" value="{tr}Change column layout{/tr}" />
						</div>

						<h3>{tr}Modules Help{/tr}</h3>
						<ul>
							<li><strong>Location</strong><br />
								{formhelp note="Select the column this module should be displayed in."}
							</li>
							<li><strong>Position</strong><br />
								{formhelp note="You can change the order in which modules are displayed by setting this value. Higher numbers are displayed further down the page. The default is 1"}
							</li>
							<li><strong>Rows</strong><br />
								{formhelp note="Select the maximum number of items to be displayed. (optional - default is 10)"}
							</li>
							<li><strong>Cache Time</strong><br />
								{formhelp note="This is the number of seconds the module is cached before the content is refreshed. The higher the value, the less load there is on the server. (optional)"}
							</li>
						</ul>
					{/form}
					{form action=$smarty.server.PHP_SELF legend="Assign center pieces"}
						@TODO form for customizing center layout r
					{/form}
				{/jstab}
			{/if}
			{if $gBitSystem->isFeatureActive( 'group_themes' )}
				{jstab title="Theme Options"}
					{legend legend="Select a Theme"}
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
			{/if}
		{/jstabs}
	</div> <!-- end .body -->
</div>  <!-- end .themes -->

{/strip}
