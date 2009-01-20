{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$gContent->mInfo}
{strip}
<div class="display group">
	{include file="bitpackage:group/group_icons.tpl"}

	<div class="header">
		<h1>{if $gContent->mInfo.thumbnail_url}<img class="thumb" style="vertical-align:middle;" src="{$gContent->mInfo.thumbnail_url.avatar}" alt="Group Image" title="{$gContent->mInfo.title|escape}" />&nbsp;{/if}
			{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<h2>About this group</h2>
	</div><!-- end .header -->

	<div class="body">
		<div class="content">
			<div class="row">
				{formlabel label="Members"}
				<div class="forminput">{$gContent->mInfo.num_members}</div>
			</div>
			<div class="row">
				{formlabel label="Description"}
				<div class="forminput">{$gContent->mInfo.parsed_data}</div>
			</div>
			<div class="row">
				{formlabel label="Created by"}
				<div class="forminput">{displayname user=$gContent->mInfo.creator_user user_id=$gContent->mInfo.creator_user_id real_name=$gContent->mInfo.creator_real_name} on <strong>{$gContent->mInfo.created|bit_long_date}</strong></div>
			</div>
			<div class="row">
				{formlabel label="Homepage URL"}
				<div class="forminput"><a href="{$smarty.const.BIT_BASE_URI}{$gContent->getDisplayUrl()}">{$smarty.const.BIT_BASE_URI}{$gContent->getDisplayUrl()}</a></div>
			</div>
			{if $gBitUser->isInGroup( $gContent->mGroupId ) && $boardsMailingList}
			<div class="row">
				{formlabel label="Group Email Address"}
				<div class="forminput">
					{$boardsMailingList}
					{formhelp note="You can post messages to this groups's forum by sending mail to this address"}
				</div>
			</div>
			{/if}
			<!-- @TODO 
			<div class="row">
				{formlabel label="RSS Feed"}
				<div class="forminput">@TODO link to board messages feed here</div>
			</div>
			-->
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .group -->
{/strip}
{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$gContent->mInfo}
