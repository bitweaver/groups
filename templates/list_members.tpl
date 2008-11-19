{strip}
<div class="display group">
	<div class="header">
		<h1>{if $gContent->mInfo.thumbnail_url}<img class="thumb" style="vertical-align:middle;" src="{$gContent->mInfo.thumbnail_url.avatar}" alt="Group Image" title="{$gContent->mInfo.title|escape}" />&nbsp;{/if}
			{$gContent->mInfo.title|escape|default:"Group"}</h1>
		<h2>{tr}Group Members{/tr}</h2>
	</div><!-- end .header -->

	<div class="body">
		<div class="content">
			<ol class="data">
				{foreach from=$groupMembers key=userId item=member}
					<li>{displayname hash=$member}</li>
				{foreachelse}
					<li>{tr}The group has no members.{/tr}</li>
				{/foreach}
			</ol>
		</div>
	</div><!-- end .body -->
</div>
{/strip}
