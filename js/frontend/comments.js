$(function()
{
	function commentFormShow()
	{
		$('#add-comment-btn').hide();
		$('#comment-form').slideDown(400);
	}

	function commentFormHide()
	{
		$('#add-comment-btn').show();
		$('#comment-form').slideUp(400);
	}

	$('#add-comment-btn').click(function(e) {
		e.preventDefault();
		commentFormShow();
	});

	$('#leave_comment').click(function(e){
		e.preventDefault();

		var el = $(this);

		el.attr('disabled', 'disabled')
			.after('<img src="' + intelli.config.baseurl + 'plugins/comments/templates/front/img/ajax-loader.gif" id="comments-loader" style="margin-left: 15px;">');

		var author = $('input[name="author"]').val();
		var email = $('input[name="email"]').val();

		if ('undefined' != typeof CKEDITOR && CKEDITOR.instances['comment_body'])
		{
			CKEDITOR.instances.comment_body.updateElement();
		}

		var body = $('textarea[name="comment_body"]').val();
		var url = $('input[name="url"]').val();
		var item_id = $('input[name="item_id"]').val();
		var item = $('input[name="item"]').val();
		var prevent_csrf = $('input[name="prevent_csrf"]').val();
		var captcha = $('#comments-captcha input').val();

		$.post(intelli.config.ia_url + 'comments/read.json', {action: 'add', author: author, item: item, email: email, url: url, body: body, prevent_csrf: prevent_csrf, item_id: item_id, security_code: captcha}, function(data)
		{
			var type = data.error ? 'error' : 'success';

			setTimeout(function()
			{
				var alertBox = $('#comments-alert');
				alertBox.children('ul').removeClass(['alert-success', 'alert-error']).html('');
				el.removeAttr('disabled');
				el.val(_t('leave_comment'));
				$('#comments-loader').remove();

				if (!data.error)
				{
					intelli.notifFloatBox({msg: data.msg, type: 'success', autohide: true});
					commentFormHide();

					if (typeof data.html != undefined)
					{
						$('#comments-container .comments-list').prepend(data.html);
					}
				}
				else
				{
					intelli.notifFloatBox({msg: data.msg, type: 'error', autohide: true});
					$('#comments-captcha .field-captcha img').click();
					$('#comments-captcha .field-captcha input').val('');
				}

			}, 1200);
		});
		return false; 
	});
});