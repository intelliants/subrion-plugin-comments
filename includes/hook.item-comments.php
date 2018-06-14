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

if (empty($item)) {
    return;
}

$enabledItems = $iaCore->get('comments_items_enabled');

if (empty($enabledItems)) {
    return;
}

$enabledItems = explode(',', $enabledItems);

if (empty($enabledItems) || !is_array($enabledItems)) {
    return;
}

if (in_array($item, $enabledItems) && $iaCore->get('comments_enabled')) {
    $iaComment = $iaCore->factoryModule('comment',  'comments');

    $comments = $iaComment->getByItem($item, $listing);

    if ($iaCore->get('text_smiles_to_graphic')) {
        $comments = iaComment::smilesToImages($comments);
    }

    $iaView->assign('comments', $comments);
    $iaView->assign('comments_item', $item);

    // add custom styles for decoration
    $iaView->add_css('_IA_URL_modules/comments/templates/front/css/style');
}
