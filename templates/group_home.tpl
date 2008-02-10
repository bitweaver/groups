{* $Header: /cvsroot/bitweaver/_bit_groups/templates/group_home.tpl,v 1.2 2008/02/10 00:37:36 wjames5 Exp $ *}
{strip}
<div class="display group">
	<div class="header">
		<h1>{tr}Groups{/tr}</h1>
	</div>

	<div class="body">
		<div class="content">
			{if !$gBitUser->isRegistered()}
				<h2>Create a Group in Three East Steps</h2>
				<div>
					<ul>
						<li>1 Register</li>
						<li>2 Create a Group</li>
						<li>3 Invite People</li>
					</ul>
					<br/><br/>
					<a href="{$smarty.const.USERS_PKG_URL}register.php">{tr}register{/tr}</a>
				</div>
			{else}
				<h2>Your Groups</h2>
				<div>
					@TODO list user's groups here
				</div>
			{/if}
			<div class="listing group">
				<h2>Search Groups</h2>
				{minifind sort_mode=$sort_mode}
				<p>@TODO insert list of new public groups</p>
			</div>
		</div><!-- end .content -->
	</div><!-- end .body -->
</div>
{/strip}
