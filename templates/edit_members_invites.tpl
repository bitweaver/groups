{strip}
<div class="admin group">
	<div class="header">
		<h1>{if $gContent->mInfo.thumbnail_url}<img class="thumb" style="vertical-align:middle;" src="{$gContent->mInfo.thumbnail_url.avatar}" alt="Group Image" title="{$gContent->mInfo.title|escape}" />&nbsp;{/if}
			{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<h2>Invite Members</h2>
	</div><!-- end .header -->
	<div class="body">
		<div class="content">
		{jstabs}
			{jstab title="Invite Members"}
			{formfeedback success=$successMsg error=$errorMsg}
			{form enctype="multipart/form-data" id="sendinvite" legend="Send invitation by email"}
				<input type="hidden" name="group_id" value="{$gContent->mInfo.group_id}" />
				<div class="form-group">
					{formlabel label="Email Addresses" for=email_addresses}
					{forminput}
						<textarea rows="10" cols="35" id="emailaddresses" name="email_addresses">{$email_addresses}</textarea>
						{formhelp note="Paste valid email addresses here, separated by commas. Each person will receive an invitation to your group."}
					{/forminput}
				</div>
				<div class="form-group">
					{formlabel label="Message" for=email_body}
					{forminput}
						<textarea rows="10" cols="35" id="emailbody" name="email_body">{$email_body}</textarea>
						{formhelp note="You can customize your invitation by adding a message. Groups will automatically include the group name, description, and link in the email."}
					{/forminput}
				</div>
				<div class="form-group submit">
					<input type="submit" class="btn btn-default" name="send_invite" value="{tr}Invite Members{/tr}" />
				</div>
			{/form}
			{if $invalidEmail}
				{legend legend="Error Help"}
					<p>{tr}There was a problem with the format of your email addresses. Please check they are well formed and separated by commas. We have tried to diagnose the errors; our diagnosis may not be perfect, hopefully it is helpful. Here are what appear to be the invalid and valid emails:{/tr}</p>
					<div class="form-group">
						{formlabel label="Invalid Email Addresses"}
						{forminput}
							{formhelp note="These addresses appear to have problems."}
							<ol>
							{foreach item=address from=$invalidEmail}
								<li>{$address}&nbsp;</li/>
							{/foreach}
							</ol>
						{/forminput}
					</div>
					<div class="form-group">
						{formlabel label="Valid Email Addresses"}
						{formhelp note="These addresses appear to be ok."}
						{forminput}
							<ol>
							{foreach item=address from=$validEmail}
								<li>{$address|escape}</li/>
							{/foreach}
							</ol>
						{/forminput}
					</div>
				{/legend}
			{/if}
			{/jstab}
			{if $gContent->hasUpdatePermission() || $gContent->hasUserPermission( 'p_group_group_members_admin' )}
			{jstab title="Open Invitations"}
				{formfeedback success=$successDeleteMsg error=$errorDeleteMsg}
				{if count( $invites ) > 0 }
					{form}
						<input type="hidden" name="group_id" value="{$gContent->mInfo.group_id}" />
						<table>
						<thead>
							<tr style="text-align:left">
								<th>Email Address</th>
								<th>User Name (if registered)</th>
								<th style="text-align:center">Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan=3 style="text-align:right">
									<input type="submit" class="btn btn-default" name="delete_invites" value="Delete Checked" />
								</td>
							</tr>
						</tfoot>
						<tbody>
						{foreach item="invite" from=$invites}
						 {cycle values="odd,even" assign="inviteClass"}
						 <tr class="data {$inviteClass}">
								<td>{$invite.email}</td>
								<td>{if $invite.login}
										<a href="{$smarty.const.BIT_ROOT_URL}users/index.php?home={$invite.login}" />{if $invite.real_name}{$invite.real_name}{else}{$invite.login}{/if}</a>
									{else}
										person is not registered
									{/if}
								</td>
								<td style="text-align:center"><input type="checkbox" name="invites[]" value="{$invite.invite_id}" /></td>
							</tr>
						{/foreach}
						</tbody>
						</table>
					{/form}
				{else}
					There are no open invitations
				{/if}
			{/jstab}
			{/if}
		{/jstabs}
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .group -->
{/strip}
