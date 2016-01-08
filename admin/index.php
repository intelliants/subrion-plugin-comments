<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2016 Intelliants, LLC <http://www.intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

$iaComment = $iaCore->factoryPlugin('comments', iaCore::ADMIN, 'comment');

$iaDb->setTable('comments');

if ($iaView->getRequestType() == iaView::REQUEST_JSON)
{
	switch ($pageAction)
	{
		case iaCore::ACTION_READ:

			switch ($_GET['get'])
			{
				default:
					$params = array();
					if (isset($_GET['text']) && $_GET['text'])
					{
						$stmt = '(`body` LIKE :text)';
						$iaDb->bind($stmt, array('text' => '%' . $_GET['text'] . '%'));

						$params[] = $stmt;
					}

					$output = $iaComment->gridRead($_GET,
						array('body', 'member_id', 'item', 'item_id', 'date', 'status'),
						array('status' => 'equal'),
						$params
					);
			}

			break;

		case iaCore::ACTION_EDIT:
			$output = $iaComment->gridUpdate($_POST);

			break;

		case iaCore::ACTION_DELETE:
			$output = $iaComment->gridDelete($_POST);
	}

	$iaView->assign($output);
}

if ($iaView->getRequestType() == iaView::REQUEST_HTML)
{
	if ($pageAction == iaCore::ACTION_EDIT)
	{
		if (!isset($iaCore->requestPath[0]) || empty($iaCore->requestPath[0]))
		{
			return iaView::errorPage(iaView::ERROR_NOT_FOUND);
		}
		$error = false;
		$messages = array();
		$id = (int)$iaCore->requestPath[0];
		$comment = $iaComment->getById($id);

		if (empty($comment))
		{
			return iaView::errorPage(iaView::ERROR_NOT_FOUND);
		}

		if (isset($_POST['save']))
		{
			iaUtil::loadUTF8Functions('ascii', 'validation', 'bad', 'utf8_to_ascii');

			$insert = array(
				'id'      => $id,
				'author'  => iaSanitize::html(iaUtil::checkPostParam('author')),
				'url'     => iaSanitize::html(iaUtil::checkPostParam('url')),
				'status'  => iaUtil::checkPostParam('status', $comment),
				'body'    => iaUtil::checkPostParam('body', $comment),
				'email'   => iaUtil::checkPostParam('email', $comment)
			);

			if (utf8_is_valid($insert['author']))
			{
				$insert['author'] = utf8_bad_replace($insert['author']);
			}

			if ($iaCore->get('comments_allow_wysiwyg'))
			{
				$insert['body'] = iaUtil::safeHTML($insert['body']);
			}
			else
			{
				$insert['body'] = iaSanitize::html(nl2br($insert['body']));
			}

			$insert['status'] = in_array($insert['status'], array(iaCore::STATUS_ACTIVE, iaCore::STATUS_INACTIVE)) ? $insert['status'] : 'inactive';

			if (!iaValidate::isEmail($insert['email']))
			{
				$error = true;
				$messages[] = iaLanguage::get('error_comment_email');
			}

			$comment['body'] = $insert['body'];
			$comment['email'] = $insert['email'];
			$comment['author'] = $insert['author'];
			$comment['url'] = $insert['url'];
			if (!$error)
			{
				$iaDb->update($insert);
				$iaView->setMessages(iaLanguage::get('changes_saved'), iaView::SUCCESS);
				iaUtil::go_to(IA_SELF . '?id=' . $id);
			}
			else
			{
				$iaView->setMessages($messages, iaView::ERROR);
			}
		}

		$iaView->title(iaLanguage::get('edit_comment'));
		iaBreadcrumb::replaceEnd(iaLanguage::get('edit_comment'), IA_SELF);

		$iaView->assign('comment', $comment);
		$iaView->assign('items_list', explode(',', $iaCore->get('comments_items_enabled')));

		$iaView->display('index');
	}
	else
	{
		$iaView->grid('_IA_URL_plugins/comments/js/admin/comments');
	}
}

$iaDb->resetTable();