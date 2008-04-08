{strip}
<div class="admin group">
	<div class="header">
		<h1>Invite Members</h1>
	</div><!-- end .header -->
	<div class="body">
		<div class="content">
			{formfeedback success=$successMsg error=$errorMsg}
			{form enctype="multipart/form-data" id="sendinvite" legend="Send invitation by email"}
				<input type="hidden" name="group_id" value="{$gContent->mInfo.group_id}" />
				<div class="row">
					{formlabel label="Email Addresses" for=email_addresses}
					{forminput}
						<textarea rows="10" cols="35" id="emailaddresses" name="email_addresses"></textarea>
						{formhelp note="Each person will receive an invitation to your group."}
					{/forminput}
				</div>
				<div class="row">
					{formlabel label="Message" for=email_body}
					{forminput}
						<textarea rows="10" cols="35" id="emailbody" name="email_body"></textarea>
						{formhelp note="You can customize your invitation by adding a message. Groups will automatically include the group name, description, and link in the email."}
					{/forminput}
				</div>
				<div class="row submit">
					<input type="submit" name="send_invite" value="{tr}Invite Members{/tr}" />
				</div>
			{/form}
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .group -->
{/strip}
