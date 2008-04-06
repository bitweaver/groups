{strip}
{legend legend="Group Email List"}

Please see <a href="http://www.bitweaver.org/wiki/GroupsPackageConfig">configuration requirements</a> prior to utilizing this feature.

	<div class="row">
		{formlabel label="Mailing List Address" for='emailhost'}
		{forminput}
			<input type="text" name="group_email_address" value="{$gBitSystem->getConfig('group_email_amin',$gBitSystem->getConfig('site_sender_email'))}" />
			{formhelp note="This is the email for the master administrator for all mailing lists."}
		{/forminput}
	</div>

{/legend}
{/strip}
