{strip}
<div class="join display group">
	<div class="header">
		<h1>{if !$gBitUser->isInGroup( $gContent->mGroupId )}{tr}Join{/tr}{else}{tr}Preferences{/tr}{/if}</h1>
	</div><!-- end .header -->

	<div class="body">
	 	{if $errors}
 			{formfeedback warning=`$errors`}
	 	{/if}
		{if !empty($joinModeration)}
			<span class="warning">{tr}Your request to join this group is pending. You can update your preferences once your request has been approved.{/tr}
		{else}
			{form enctype="multipart/form-data" id="joingroupform"}
				{if $gBitSystem->isPackageActive('switchboard')}
					{if !$gBitUser->isInGroup( $gContent->mGroupId )}
						{assign var="legendtext" value="How do you want to read this group?"}
					{else}
						{assign var="legendtext" value="Membership preferences"}
					{/if}
					{legend legend=$legendtext}
						<input type="hidden" name="group_id" value="{$gContent->mInfo.group_id}" />
						{formlabel label=Email}
						{forminput}
							<input type="radio" value="each" name="notice" checked="checked"/>
							{formhelp note="Recieve each message via email as it is posted."}
						{/forminput}
						{formlabel label=Digest}
						{forminput}
							<input type="radio" value="digest" name="notice" />
							{formhelp note="Get all messages for the day bundled into a single email."}
						{/forminput}
						{formlabel label="No Email"}
						{forminput}
							<input type="radio" value="none" name="notice" />
							{formhelp note="Recieve no emails, read all messages online."}
						{/forminput}
					{/legend}
				{/if}
				{if !$gBitUser->isInGroup( $gContent->mGroupId) && $gContent->mInfo.is_public != "y"}
					{legend legend="Join Request"}
						{forminput}
							<span class="warning">{tr}Membership in this group is moderated.{/tr}</span>
						{/forminput}
						{formlabel label="Message To Moderator"}
						{forminput}
							<textarea name="join_message"></textarea>
							{formhelp note="You may send a message to the moderator with your join request."}
						{/forminput}
					{/legend}
				{/if}
				<div class="row submit">
					{if !$gBitUser->isInGroup( $gContent->mGroupId )}
						<input type="submit" name="join_group" value="{tr}Join{/tr}" />
					{else}
						<input type="submit" name="save_prefs" value="{tr}Update{/tr}" />&nbsp;
						<input type="submit" name="leave_group" value="{tr}Unsubscribe{/tr}" />
					{/if}
				</div>
			{/form}
		{/if}
	</div>
</div>
{/strip}
