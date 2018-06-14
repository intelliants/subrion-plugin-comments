<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
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
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_JSON == $iaView->getRequestType() && isset($_POST['action'])) {
    $error = false;
    $messages = array();
    $output = array();

    $iaComment = $iaCore->factoryModule('comment',  'comments');

    if (iaCore::ACTION_ADD == $_POST['action']) {
        iaCore::util();

        $items = explode(',', $iaCore->get('comments_items_enabled'));
        $min_chars = $iaCore->get('comment_min_chars');
        $max_chars = $iaCore->get('comment_max_chars');
        $comment = array(
            'author' => iaUtil::checkPostParam('author'),
            'item' => iaUtil::checkPostParam('item'),
            'item_id' => (int)iaUtil::checkPostParam('item_id'),
            'email' => iaUtil::checkPostParam('email'),
            'url' => iaUtil::checkPostParam('url'),
            'body' => iaUtil::checkPostParam('body'),
            'member_id' => (int)iaUsers::getIdentity()->id,
            'ip' => $iaCore->util()->getIp(),
            'status' => $iaCore->get('comments_approval') ? iaCore::STATUS_ACTIVE : iaCore::STATUS_INACTIVE,
        );

        iaUtil::loadUTF8Functions('ascii', 'validation', 'bad');

        // CHECK: item
        if (!in_array($comment['item'], $items)) {
            $error = true;
            $messages[] = iaLanguage::get('error_item_not_allowed');
        }

        if (!iaUsers::hasIdentity()) {
            // CHECK: author
            if (empty($comment['author'])) {
                $error = true;
                $messages[] = iaLanguage::get('error_comment_author');
            } else {
                $comment['author'] = iaSanitize::html(utf8_bad_replace($comment['author']));
            }

            // CHECK: email
            if (!iaValidate::isEmail($comment['email'])) {
                $error = true;
                $messages[] = iaLanguage::get('error_comment_email');
            }

            // CHECK: captcha
            if (!iaValidate::isCaptchaValid()) {
                $error = true;
                $messages[] = iaLanguage::get('confirmation_code_incorrect');
            }
        }

        // CHECK: body
        if (!utf8_is_valid($comment['body'])) {
            $comment['body'] = utf8_bad_replace($comment['body']);
        }

        if ($iaCore->get('comments_allow_wysiwyg')) {
            $comment['body'] = iaUtil::safeHTML($comment['body']);
        } else {
            $comment['body'] = nl2br(iaSanitize::html($comment['body']));
        }

        $len = utf8_is_ascii($comment['body']) ? strlen($comment['body']) : utf8_strlen($comment['body']);

        if (empty($comment['body'])) {
            $error = true;
            $messages[] = iaLanguage::get('error_comment');
        } elseif ($min_chars > 0 && $len < $min_chars) {
            $error = true;
            $messages[] = iaLanguage::getf('error_min_comment', array('length' => $min_chars));
        } elseif ($max_chars > 0 && $len > $max_chars) {
            $error = true;
            $messages[] = iaLanguage::getf('error_max_comment', array('length' => $max_chars));
        }

        if (!$error) {
            $comment['date'] = date(iaDb::DATETIME_FORMAT);

            $iaDb->setTable('comments');
            $id = $iaDb->insert($comment);

            if ($iaCore->get('comments_approval')) {
                $iaSmarty = $iaCore->factory('smarty');

                $output['date'] = strftime($iaCore->get('date_format'), time());

                $iaSmarty->assign('comment', $iaComment->getById($id));
                $iaSmarty->assign('img', IA_TPL_URL . 'img/');

                $output['html'] = $iaSmarty->fetch(IA_MODULES . 'comments/templates/front/comment.tpl');

                $messages[] = iaLanguage::get('comment_added');
            } else {
                $messages[] = iaLanguage::get('comment_added') . ' ' . iaLanguage::get('comment_waits_approve');
            }

            $iaDb->resetTable();
        }
    }

    $output['error'] = $error;
    $output['msg'] = $messages;

    $iaView->assign($output);
}
