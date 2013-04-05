{if $connect_group_content_id or $smarty.request.connect_group_content_id}
{if !$connect_group_content_id}
	{assign var=connect_group_content_id value=$smarty.request.connect_group_content_id}
{/if}
<script type="text/javascript">
BitGroup = {ldelim}"connect_group_content_id":{$connect_group_content_id}{rdelim};
</script>
{/if}
