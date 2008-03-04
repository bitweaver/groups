{strip}
<div class="join group">
	<div class="header">
		<h1>{if !$gBitUser->isInGroup( $gContent->mGroupId )}{tr}Join{/tr}{else}{tr}Preferences{/tr}{/if}</h1>
	 	{if $errors}
 			{formfeedback warning=`$errors`}
	 	{/if}
		{form enctype="multipart/form-data" id="joingroupform"}
		{if !$gBitUser->isInGroup( $gContent->mGroupId )}
			{assign var="legendtext" value="How do you want to read this group?"}
		{else}
			{assign var="legendtext" value="Membership preferences"}
		{/if}
		{legend legend=$legendtext}
			<input type="hidden" name="group_id" value="{$gContent->mInfo.group_id}" />
			<div class="row">
				<input type="radio" value="each" name="notice" checked="checked"/> Email<br/>
				Recieve each message via email as it is posted.<br/>
				<input type="radio" value="digest" name="notice" /> Digest<br/>
				Get all messages for the day bundled into a single email.<br/>
				<input type="radio" value="none" name="notice" /> No Email<br/>
				Recieve no emails, read all messages online.
			</div>
			<div class="row submit">
				{if !$gBitUser->isInGroup( $gContent->mGroupId )}
					<input type="submit" name="join_group" value="{tr}Join{/tr}" />
				{else}
					<input type="submit" name="save_prefs" value="{tr}Update{/tr}" />&nbsp;
					<input type="submit" name="leave_group" value="{tr}Unsubscribe{/tr}" />
				{/if}
			</div>
		{/legend}
		{/form}
	</div>
</div>
{/strip}
