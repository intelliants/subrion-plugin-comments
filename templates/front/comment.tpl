<div class="comment clearfix">
	<div class="comment__head">
		{if $comment.author_avatar}
			{assign author_avatar $comment.author_avatar|unserialize}
			{if $author_avatar}
				{printImage imgfile=$author_avatar.path class="comment__ava" title=$comment.author}
			{else}
				<img src="{$img}no-avatar.png" class="comment__ava" alt="{$comment.author}">
			{/if}
		{else}
			<img src="{$img}no-avatar.png" class="comment__ava" alt="{$comment.author}">
		{/if}
		<span class="comment__name">
			{if  0 == $comment.member_id}
				{if '' != $comment.url}
					<a href="{$comment.url|escape:'html'}" rel="nofollow">{$comment.author|escape:'html'}</a>
				{else}
					{$comment.author|escape:'html'}
				{/if}
			{else}
				{ia_url type='link' data=$comment item='members' text=$comment.author}
			{/if}
		</span>
		<span class="comment__date">{lang key='on'} {$comment.date|date_format:$core.config.date_format}</span>
	</div>
	<div class="comment__body">
		{$comment.body}
	</div>
</div>