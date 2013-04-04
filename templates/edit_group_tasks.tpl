{strip}
<div class="admin group">
	<div class="header">
		<h1>{if $gContent->mInfo.thumbnail_url}<img class="thumb" style="vertical-align:middle;" src="{$gContent->mInfo.thumbnail_url.avatar}" alt="Group Image" title="{$gContent->mInfo.title|escape}" />&nbsp;{/if}
			{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<h2>Management Tasks</h2>
	</div><!-- end .header -->
	<div class="body">
		<div class="content">
		{jstabs}
			{jstab title="Pending Requests"}
				{assign var=had_moderation value=false}
				{assign var=last_type value=false}
				{capture assign=list_moderations}{strip}
				{foreach from=$modRequests item=moderation}
					{if $moderation.responsible == $smarty.const.MODERATION_NEEDED &&
						($moderation.moderator_id == $gBitUser->mUserId ||
							array_key_exists($moderation.moderator_group_id, $gBitUser->mGroups) ||
						$gBitUser->isAdmin())}
						{assign var=had_moderation value=true}
						{* set a title for a list by type *}
						{if $last_type != $moderation.type}
							{if $last_type}
								</ul></div>
							{/if}
							{assign var=last_type value=$moderation.type}
							<div class="control-group">
								<h3>{if $moderation.type=='add_content'}Content Submissions{else}{$moderation.type|ucwords}{/if}</h3>
								<ul>
						{/if}
						{* add the row *}
						<li>{include file="bitpackage:group/moderate.tpl"}</li>
					{/if}
				{/foreach}
				{if $had_moderation}
					</ul></div>
				{/if}
				{/strip}{/capture}

				{if $list_moderations}
					{legend legend="Review Requests"}
						{formfeedback success=$successModMsg error=$errorModMsg}
						<ul>{$list_moderations}</ul>
					{/legend}
				{else}
					<p>{tr}There are currently no requests to be reviewed.{/tr}</p>
				{/if}
			{/jstab}
			{jstab title="Email Members"}
				{legend legend="Send an email blast to members of the group"}
				{formfeedback success=$successEmailMsg error=$errorEmailMsg}
				{form}
						<input type="hidden" name="group_id" value="{$gContent->mInfo.group_id}" />
						<div class="control-group">
							{formlabel label="Subject" for="subject"}
							{forminput}
								<input type="text" size="60" maxlength="200" name="email_subject" id="subject" value="" />
							{/forminput}
						</div>
						{textarea name="email_body" label="Email Body"}{/textarea}
						<div class="control-group submit">
							<input type="submit" class="btn" name="send_email" value="{tr}Send{/tr}" />
						</div>
				{/form}
				{/legend}
			{/jstab}
		{/jstabs}
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .group -->
{/strip}
