{strip}
<div class="join display group">
	<div class="header">
		<h1>{if $gContent->mInfo.thumbnail_url}<img class="thumb" style="vertical-align:middle;" src="{$gContent->mInfo.thumbnail_url.avatar}" alt="Group Image" title="{$gContent->mInfo.title|escape}" />&nbsp;{/if}
			{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<h2>{if !$gBitUser->isInGroup( $gContent->mGroupId )}{tr}Join{/tr}{else}{tr}Preferences{/tr}{/if}</h2>
	</div><!-- end .header -->

	<div class="body">
	 	{if $errors || $successMsg}
			{formfeedback success=$successMsg error=$errorMsg}
	 	{/if}
		{if !empty($joinModeration)}
			<span class="warning">{tr}Your request to join this group is pending. You can update your preferences once your request has been approved.{/tr}
		{else}
			{form enctype="multipart/form-data" id="joingroupform"}
				{if $gBitSystem->isPackageActive('switchboard') || $hasMailList}
					{if !$gBitUser->isInGroup( $gContent->mGroupId )}
						{assign var="legendtext" value="How do you want to read this group?"}
					{else}
						{assign var="legendtext" value="Notification preferences"}
					{/if}
					{legend legend=$legendtext}
						<input type="hidden" name="group_id" value="{$gContent->mInfo.group_id}" />
						{formlabel label=Email}
						{forminput}
							<input type="radio" value="email" name="notice" {if $userEmailPref=='email'}checked="checked"{/if}/>
							{formhelp note="Recieve each message via email as it is posted."}
						{/forminput}
						{formlabel label=Digest}
						{forminput}
							<input type="radio" value="digest" name="notice" {if $userEmailPref=='digest'}checked="checked"{/if}/>
							{formhelp note="Get all messages for the day bundled into a single email."}
						{/forminput}
						{formlabel label="No Email"}
						{forminput}
							<input type="radio" value="none" name="notice" {if $userEmailPref=='none'}checked="checked"{/if}/>
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
