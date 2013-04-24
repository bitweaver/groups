{strip}

<div class="admin themes">
	<div class="header">
		<h1>{if $gContent->mInfo.thumbnail_url}<img class="thumb" style="vertical-align:middle;" src="{$gContent->mInfo.thumbnail_url.avatar}" alt="Group Image" title="{$gContent->mInfo.title|escape}" />{/if}
			&nbsp;{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<h2> {tr}Group Styles Manager{/tr}</h2>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}
		{if $approve}
			<div id="themeapprove">
				<h1>{tr}Confirm Selection{/tr}</h1>
				<p>{tr}The settings you have chosen has not been applied to the site yet. This allows you to test the styles before applying them to your site. To accept the change, please click on the accept button below{/tr}<p>
				<a href="{$smarty.const.GROUP_PKG_URL}theme.php">{biticon ipackage=icons iname="large/dialog-cancel" iexplain="Cancel"}</a>
				<a href="{$smarty.const.GROUP_PKG_URL}theme.php?
					group_id={$gContent->mGroupId}&amp;
					{if $smarty.request.group_style}
						group_style={$smarty.request.group_style}&amp;
						style_variation={$smarty.request.style_variation}&amp;
					{else $smarty.request.remove_group_style}
						remove_group_style=true&amp;
					{/if}
					approved=1">{biticon ipackage=icons iname="large/dialog-ok" iexplain="Accept"}</a>
			</div>
		{/if}

		{jstabs}
			{if $gBitSystem->isFeatureActive( 'group_layouts' )}
				{jstab title="Layout Options"}
					{form action=$smarty.server.SCRIPT_NAME legend="Assign column modules"}

						<input type="hidden" name="group_id" value="{$gContent->mGroupId}" />
						<table class="table data">
							<thead>
								<tr style="text-align:left;">
									<th style="width:30%">{tr}Column Modules{/tr}</th>
									<th style="width:10%">{tr}Order{/tr}</th>
									<th style="width:10%">{tr}Rows{/tr}</th>
									<th style="width:10%">{tr}Cache Time (seconds){/tr}</th>
									<th style="width:10%">{tr}Params{/tr}</th>
									<th style="text-align:center">{tr}Location{/tr}</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$allowedModules item=modules key=package}
								{cycle values="odd,even" assign="pkgClass"}
									<tr class="{$pkgClass} data">
										<td colspan="6"><strong>{$package}</strong></td>
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
												<input type="text" size="5" name="fAssign[{$name}][pos]" id="pos" value="{$assignedModules.$tpl.pos|escape}" />
											</td>
											<td>
												<input type="text" size="5" name="fAssign[{$name}][module_rows]" id="module_rows" value="{$assignedModules.$tpl.module_rows|escape}" />
											</td>
											<td>
												<input type="text" size="5" name="fAssign[{$name}][cache_time]" id="cache_time" value="{$assignedModules.$tpl.cache_time|escape}" />
											</td>
											<td>
												<input type="text" size="15" name="fAssign[{$name}][params]" id="params" value="{$assignedModules.$tpl.params|escape}" />
											</td>
											<td style="text-align:center">
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
										</tr>
									{/foreach}
								{/foreach}
							</tbody>
						</table>

						<div class="control-group submit">
							<input type="submit" class="btn" name="submitcolumns" value="{tr}Change columns settings{/tr}" />
						</div>

					{/form}
					{legend legend="Assign center modules"}
					{if count($centerAssignedModules) > 0}
						{form action=$smarty.server.SCRIPT_NAME}
							<input type="hidden" name="group_id" value="{$gContent->mGroupId}" />
							<table class="table data">
								<thead>
									<tr style="text-align:left;">
										<th style="width:30%">{tr}Assigned Center Modules{/tr}</th>
										<th style="width:10%">{tr}Order{/tr}</th>
										<th style="width:10%">{tr}Rows{/tr}</th>
										<th style="width:10%">{tr}Cache Time (seconds){/tr}</th>
										<th style="width:10%">{tr}Params{/tr}</th>
										<th style="text-align:center;">{tr}Unassign{/tr}</th>
									</tr>
								</thead>
								<tbody>
								{foreach from=$centerAssignedModules item=cmodule key=tpl}
									{if $cmodule.layout_area == "c"}
										{cycle values="odd,even" assign="pkgClass"}
										<tr class="{$pkgClass} data">
											<td>{$cmodule.name}
												<input type="hidden" name="fAssign[{$cmodule.module_id}][module_id]" value="{$cmodule.module_id}" />
												<input type="hidden" name="fAssign[{$cmodule.module_id}][layout_area]" value="c" />
											</td>
											<td>
												<input type="text" size="5" name="fAssign[{$cmodule.module_id}][pos]" id="pos" value="{$cmodule.pos|escape}" />
											</td>
											<td>
												<input type="text" size="5" name="fAssign[{$cmodule.module_id}][module_rows]" id="module_rows" value="{$cmodule.module_rows|escape}" />
											</td>
											<td>
												<input type="text" size="5" name="fAssign[{$cmodule.module_id}][cache_time]" id="cache_time" value="{$cmodule.cache_time|escape}" />
											</td>
											<td>
												<input type="text" size="15" name="fAssign[{$cmodule.module_id}][params]" id="params" value="{$cmodule.params|escape}" />
											</td>
											<td style="text-align:center;">
												<a title="{tr}Unassign{/tr}" href="{$smarty.const.GROUP_PKG_URL}theme.php?group_id={$gContent->mGroupId}&amp;unassigncenter=1&amp;module_id={$cmodule.module_id}">{booticon iname="icon-trash" ipackage="icons" iexplain="Unassign Module"}</a>
											</td>
										</tr>
									{/if}
								{/foreach}
								</tbody>
							</table>

							<div class="control-group submit">
								<input type="submit" class="btn" name="changecenter" value="{tr}Change center settings{/tr}" />
							</div>
						{/form}
					{else}
						<div style="margin:0 0 20px 0; text-align:center;"><em>There are currently no custom assigned center modules. Defaults are used.</em></div>
					{/if}
					{form action=$smarty.server.SCRIPT_NAME}
			<input type="hidden" name="group_id" value="{$gContent->mGroupId}" />
			<input type="hidden" name="fAssign[layout_area]" value="c" />

			<div class="control-group">
				{formlabel label="Center Piece" for="module"}
				{forminput}
					{if $fEdit && $fAssign.name}
						<input type="hidden" name="fAssign[module]" value="{$fAssign.module}" id="module" />{$fAssign.module}
					{else}
						{html_options name="fAssign[module_rsrc]" id="module" values=$allCenters options=$allCenters selected=$mod}
					{/if}
					{formhelp note="Pick the center bit you want to display when accessing this package."}
				{/forminput}
			</div>

			<div class="control-group">
				{formlabel label="Order" for="c_ord"}
				{forminput}
					<input type="text" size="5" name="fAssign[pos]" id="pos" value="" />
					{formhelp note="Select where within the column the module should be displayed."}
				{/forminput}
			</div>

			<div class="control-group">
				{formlabel label="Cache Time" for="c_cache_time"}
				{forminput}
					<input type="text" name="fAssign[cache_time]" id="c_cache_time" size="5" value="{$fAssign.cache_time|escape}" /> seconds
					{formhelp note="This is the number of seconds the module is cached before the content is refreshed. The higher the value, the less load there is on the server. (optional)"}
				{/forminput}
			</div>

			<div class="control-group">
				{formlabel label="Rows" for="c_rows"}
				{forminput}
					<input type="text" size="5" name="fAssign[module_rows]" id="c_rows" value="{$fAssign.module_rows|escape}" />
					{formhelp note="Select what the maximum number of items are displayed. (optional - default is 10)"}
				{/forminput}
			</div>

			<div class="control-group">
				{formlabel label="Parameters" for="c_params"}
				{forminput}
					<input type="text" size="48" name="fAssign[params]" id="c_params" value="{$fAssign.params|escape}" />
					{formhelp note="Here you can enter any additional parameters the module might need. (optional)"}
				{/forminput}
			</div>
						<div class="control-group submit">
							<input type="submit" class="btn" name="submitcenter" value="{tr}Assign center module{/tr}" />
						</div>
					{/form}
					{/legend}
					{legend legend="Modules Help"}
						<ul>
							<li><strong>Location</strong><br />
								{formhelp note="Select the column this module should be displayed in."}
							</li>
							<li><strong>Order</strong><br />
								{formhelp note="You can change the order in which modules are displayed by setting this value. Higher numbers are displayed further down the page. The default is 1"}
							</li>
							<li><strong>Rows</strong><br />
								{formhelp note="Select the maximum number of items to be displayed. (optional - default is 10)"}
							</li>
							<li><strong>Cache Time</strong><br />
								{formhelp note="This is the number of seconds the module is cached before the content is refreshed. The higher the value, the less load there is on the server. (optional)"}
							</li>
						</ul>
					{/legend}
				{/jstab}
			{/if}
			{if $gBitSystem->isFeatureActive( 'group_themes' )}
				{jstab title="Theme Options"}
					{if $style}
						<div>
							<a href="{$smarty.const.GROUP_PKG_URL}theme.php?group_id={$gContent->mGroupId}&amp;remove_group_style=true">{tr}Clear Custom Theme (revert this group's look and feel to the website default){/tr}</a>
						</div>
					{/if}
					{legend legend="Select a Theme"}
					<ul class="data">
						{foreach from=$stylesList item=s}
							<li class="{cycle values='odd,even"} item">
								<h2 {if $style eq $s.style}class="highlight"{/if}>
									{if $style eq $s.style}
										{booticon iname="icon-ok"  ipackage="icons"  iexplain="Current Style"}&nbsp;
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
