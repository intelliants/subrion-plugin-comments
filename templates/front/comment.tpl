<div class="comment clearfix">
    <div class="comment__head">
        {if $comment.author_avatar}
            {ia_image file=$comment.author_avatar|unserialize title=$comment.author class='comment__ava' gravatar=true email=$comment.email gravatar_width=200}
        {else}
            <img src="{$img}no-avatar.png" class="comment__ava" alt="{$comment.author}">
        {/if}
        <span class="comment__name">
            {if  0 == $comment.member_id}
                {if '' != $comment.url}
                    <a href="{$comment.url|escape}" rel="nofollow">{$comment.author|escape}</a>
                {else}
                    {$comment.author|escape}
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