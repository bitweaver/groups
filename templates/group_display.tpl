{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$gContent->mInfo}
<div class="display group">
	{include file="bitpackage:group/group_icons.tpl}
	
	<div class="header">
		<h1>{if $gContent->mInfo.thumbnail_url}<img class="thumb" style="vertical-align:middle;" src="{$gContent->mInfo.thumbnail_url.avatar}" alt="Group Image" title="{$gContent->mInfo.title|escape}" />{/if}
			{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<p>{$gContent->mInfo.summary|escape}</p>
	</div><!-- end .header -->

	<div class="body">
		<div class="content">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$gContent->mInfo}

			<div class="floaticon"><a href="{$smarty.const.BOARDS_PKG_URL}index.php?b={$gContent->mInfo.board_id}">{tr}View More{/tr}</a></div>
			<h2>Discussions</h2>
				<table class="data">
					<tr>
						<th>{tr}Title{/tr}</th>
						<th>{tr}Replies{/tr}</th>
						<th>{tr}Started{/tr}</th>
						<th>{tr}Last Reply{/tr}</th>
						{if $gContent->hasUserPermission('p_boards_update') || $gContent->hasUserPermission('p_boards_post_update')}
							<th>{tr}Actions{/tr}</th>
						{/if}
					</tr>
					{foreach item=thread from=$topics}
					<tr class="{cycle values="even,odd"} {if $gBitSystem->isFeatureActive('boards_post_anon_moderation') && $thread.unreg > 0}unapproved{elseif $thread.th_moved>0}moved{/if} {if $thread.th_sticky==1} highlight{/if}" >
						<td style="white-space:nowrap;">{* topic status icons *}
							{if $thread.th_moved>0}
								{biticon ipackage="icons" iname="go-jump" iexplain="Moved Topic"}
							{else}
								{assign var=flip value=$thread.flip}
								{foreach from=$flip item=flip_s key=flip_name}
									{include file="bitpackage:boards/flipswitch.tpl"}
								{/foreach}
							{/if}
							<a href="{$thread.url}" title="{$thread.title|escape}">{$thread.title|escape}</a>
						</td>

						<td style="text-align:center;">{if $thread.post_count-1}{$thread.post_count-1}{else}-{/if}</td>

						<td style="text-align:center;">
							{$thread.flc_created|reltime:short|escape}<br/>
							{if $thread.flc_user_id < 0}{$thread.anon_name|escape}{else}{displayname user_id=$thread.flc_user_id}{/if}
						</td>

						<td style="text-align:center;">
							{if $thread.post_count > 1}{$thread.llc_last_modified|reltime:short|escape}{else}-{/if}<br/>
							{if $thread.post_count > 1}{if $thread.llc_user_id < 0}{$thread.l_anon_name|escape}{else}{displayname user_id=$thread.llc_user_id}{/if}{else}{/if}
						</td>

						{if $gContent->hasUserPermission('p_boards_update') || $gContent->hasUserPermission('p_boards_post_update')}
							<td style="text-align:center;">{if $thread.unreg > 0}<a class="highlight" href="{$thread.url}" title="{$thread.title|escape}">{$thread.unreg}</a>{else}-{/if}</td>
						{/if}

					</tr>
					{foreachelse}
					<tr>
						<td>{tr}No records found{/tr}</td>
					</tr>
					{/foreach}
				</table>
			{if $gBitUser->isAdmin() || $gContent->isOwner() || ($gBitUser->isInGroup( $gContent->mGroupId ) && $gContent->hasUserPermission('p_group_group_msgs_create'))}
				<div class="navbar">
					<a class="button" title="{tr}New Topic{/tr}" href="{$smarty.const.BOARDS_PKG_URL}index.php?b={$gContent->mInfo.board_id}&amp;post_comment_request=1#editcomments">{biticon ipackage="icons" iname="mail-message-new" iexplain="New Topic" iforce="icon"} {tr}New Topic{/tr}</a>
				</div>
			{/if}

			{if $contentList}
				<h2>Group Content</h2>
				<table class="data">
					<tr>
						<th style="width:20%;">{tr}Type{/tr}</th>
						<th style="width:50%;">{tr}Title{/tr}</th>
						<th style="width:20%;">{tr}Last Updated{/tr}</th>
						<th style="width:10%;">{tr}Last Editor{/tr}</th>
					</tr>
					{foreach item=item from=$contentList}
						<tr class="{cycle values="even,odd"}" >
							<td style="text-align:left;">
								{$item.content_description}
							</td>
							<td style="text-align:left;">
								<a href="{$item.display_url}">{$item.title}</a>
							</td>
							<td style="text-align:center;">
								{$item.last_modified|reltime:short|escape}
							</td>
							<td style="text-align:center;">
								{$item.modifier_user}
							</td>
						</tr>
					{/foreach}
				</table>
			{/if}

		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .group -->
{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$gContent->mInfo}
