{strip}

<div class="listing users">
	<div class="header">
		<h1>{tr}Mailing List{/tr}{if $gContent->getPreference('group_mailing_list')}: {$gContent->getPreference('group_mailing_list')}{/if}</h1>
	</div>

	<div class="body">
		{formfeedback success=$successMsg error=$errorMsg}
{jstabs}

{jstab title="List Information"}
{if $gContent->getPreference('group_mailing_list')}
	{form}
	<input type="hidden" name="group_id" value="{$gContent->mGroupId}"/>
	<div class="row">
		{formlabel label="Subscribe"}
		{forminput}
			{if mailman_findmember($gContent->getPreference('group_mailing_list'),$gBitUser->getField('email'))}
				<p>{tr}You are currently subscribed to the mailing list using the email:{/tr} {$gBitUser->getField('email')}</p>
				<input type="submit" name="unsubscribe" value="Unsubscribe" />
			{else}
				<p>{tr}You are currently not subscribed to the mailing list.{/tr}</p>
				<input type="submit" name="subscribe" value="Subscribe" />
			{/if}
		{/forminput}
	</div>
	{if $gContent->hasAdminPermission()}
	<div class="row submit">
		{forminput}
			<input type="submit" name="delete_list" value="Delete List" />
		{/forminput}
	</div>
	{/if}
	{/form}
{else}
			{formfeedback warning="No mailing address has been configured for this group."}
	{if $gContent->hasAdminPermission()}
		{if $gBitSystem->getConfig('group_email_mailman_bin')}
{legend legend="Group Mailing List"}
{form}
	<input type="hidden" name="group_id" value="{$gContent->mGroupId}"/>
	<div class="row">
		{formlabel label="Mailing List Address" for='emailhost'}
		{forminput}
			<input type="text" name="group_mailing_list" value="{$smarty.request.group_mailing_list|default:$suggestedListName}" /> <strong> @ {$gBitSystem->getConfig('group_email_host',$gBitSystem->getConfig('kernel_server_name'))} </strong>
			{formhelp note="This is the email address for the group. It needs to be all lowercase alpha-numeric characters."}
		{/forminput}
	</div>
	<div class="row">
		{formlabel label="Administrator Password" for='emailhost'}
		{forminput}
			<input type="text" name="group_mailing_list_password" value="{$smarty.request.group_mailing_list_password}" />
			{formhelp note="This is the password used to administer the mailing list."}
		{/forminput}
	</div>
	<div class="row submit">
		{forminput}
			<input type="submit" name="create_list" value="Create List" />
		{/forminput}
	</div>
{/form}
{/legend}
		{else}
			{formfeedback error="Mailman is not configured."}
			{if $gBitUser->isAdmin()}
				<a href="{$smarty.const.KERNEL_PKG_PATH}admin/index.php?page=group">{tr}See group configuration{/tr}</a>
			{/if}
		{/if}
	{/if}
{/if}
{/jstab}
{if $listMembers}
{jstab title="List Subscribers"}
		<ol class="data">
			{foreach from=$listMembers key=userId item=member}
				<li>{displayname email=$member} &lt;{$member}&gt;</li>
			{foreachelse}
				<li>{tr}The group has no members.{/tr}</li>
			{/foreach}
		</ol>
{/jstab}
{/if}

{/jstabs}
	</div><!-- end .body -->
</div>
{/strip}
