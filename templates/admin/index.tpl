{if iaCore::ACTION_EDIT == $pageAction}
    <form method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
        {preventCsrf}

        <div class="wrap-list">
            <div class="wrap-group">
                <div class="wrap-group-heading">
                    <h4>{lang key='options'}</h4>
                </div>

                <div class="row">
                    <label class="col col-lg-2 control-label" for="input-item">{lang key="item"}</label>
                    <div class="col col-lg-4">
                        <input type="text" value="{$comment.item} [id: {$comment.item_id}]" id="input-item" disabled="disabled">
                    </div>
                </div>

                <div class="row">
                    <label class="col col-lg-2 control-label" for="input-author">{lang key="author"}</label>
                    <div class="col col-lg-4">
                        <input type="text" name="author" value="{$comment.author}" id="input-author">
                    </div>
                </div>

                <div class="row">
                    <label class="col col-lg-2 control-label" for="input-email">{lang key="email"}</label>
                    <div class="col col-lg-4">
                        <input type="text" name="email" id="input-email" value="{$comment.email}">
                    </div>
                </div>

                <div class="row">
                    <label class="col col-lg-2 control-label" for="input-url">{lang key="url"}</label>
                    <div class="col col-lg-4">
                        <input type="text" name="url" id="input-url" value="{$comment.url}">
                    </div>
                </div>


                <div class="row">
                    <label class="col col-lg-2 control-label" for="body">{lang key='body'}</label>
                    <div class="col col-lg-8">
                        {if !$core.config.comments_allow_wysiwyg}
                            <textarea name="body" rows="6" cols="40" id="comment_form">{$comment.body}</textarea>
                        {else}
                            {ia_wysiwyg name="body" id="comment_form" value=$comment.body}
                        {/if}
                    </div>
                </div>

                <div class="row">
                    <label class="col col-lg-2 control-label" for="input-status">{lang key='status'}</label>
                    <div class="col col-lg-4">
                        <select name="status" id="input-status">
                            <option value="active"{if iaCore::STATUS_ACTIVE == $comment.status} selected="selected"{/if}>{lang key='active'}</option>
                            <option value="inactive"{if iaCore::STATUS_INACTIVE == $comment.status} selected="selected"{/if}>{lang key='inactive'}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions inline">
                <button type="submit" name="save" class="btn btn-primary">{lang key='save_changes'}</button>
            </div>
        </div>
    </form>
{/if}