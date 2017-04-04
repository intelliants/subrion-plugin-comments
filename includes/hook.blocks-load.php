<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2017 Intelliants, LLC <https://intelliants.com>
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

if (iaView::REQUEST_HTML == $iaView->getRequestType() && $iaView->blockExists('latest_comments')) {
    $iaItem = $iaCore->factory('item');
    $iaComment = $iaCore->factoryPlugin('comments', iaCore::FRONT, 'comment');

    $array = $iaComment->getLatest($iaCore->get('num_latest_comments'));
    $total = $iaDb->foundRows();

    foreach ($array as $key => $comment) {
        if ('members' == $comment['item']) {
            $iaUsers = $iaCore->factory('users');
            $itemData = $iaUsers->getInfo($comment['item_id']);
            $itemData['title'] = $itemData['fullname'] ? $itemData['fullname'] : $itemData['username'];
            $array[$key]['item_url'] = IA_URL . 'member/' . $itemData['username'] . '.html';
        } else {
            $itemPackage = $iaItem->getPackageByItem($comment['item']);
            $itemClass = $iaCore->factoryModule('item', $itemPackage, iaCore::FRONT, $comment['item']);
            $itemData = $itemClass->getById($comment['item_id']);

            if (isset($itemData['model'])) {
                $itemData['title'] = $itemData['model'];
            }

            $array[$key]['item_url'] = $itemClass->url('view', $itemData);
        }
        if (isset($itemData['title'])) {
            $array[$key]['item_title'] = $itemData['title'];
        }

    }

    if ($iaCore->get('text_smiles_to_graphic')) {
        $array = iaComment::smilesToImages($array);
    }

    $iaView->assign('num_total_comments', $total);
    $iaView->assign('latest_comments', $array);
}