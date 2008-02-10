{strip}
<div class="join group">
	<div class="header">
		<h1>Join</h1>
		{form enctype="multipart/form-data" id="joingroupform"}
		{legend legend="How do you want to read this group?"}
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
				<input type="submit" name="join_group" value="{tr}Join{/tr}" />
			</div>
		{/legend}
		{/form}
	</div>
</div>
{/strip}
