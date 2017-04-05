{if !empty($latest_comments)}
    {foreach $latest_comments as $comment}
        <div class="media">
            <div class="media-body">
                {$comment.body|strip_tags|truncate:200:"..."}
                <div class="media-date">
                    {lang key="on"} {$comment.date|date_format:$core.config.date_format}
                    {if isset($comment.item_title)}
                        {lang key='about'} <a href="{$comment.item_url}">{$comment.item_title}</a>
                    {else}
                        <a href="{$comment.item_url}">{lang key='view_subject'}</a>
                    {/if}
                </div>
            </div>
        </div>
    {/foreach}
{/if}