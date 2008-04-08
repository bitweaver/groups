{strip}
<div class="admin group">
	<div class="header">
		<h1>Management Tasks</h1>
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
							<div class="row">
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
						{formfeedback success=$successMsg error=$errorMsg}
						<ul>{$list_moderations}</ul>
					{/legend}
				{else}
					<p>{tr}There are currently no requests to be reviewed.{/tr}</p>
				{/if}
			{/jstab}
		{/jstabs}
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .group -->
{/strip}
