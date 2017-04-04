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

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $action = $iaCore->requestPath[1];

    $iaComment = $iaCore->factoryPlugin('comments', iaCore::ADMIN, 'comment');
    $packageItems = $iaDb->onefield('item', "`package` = '{$extra}'", null, null, 'items');
    $itemsEnabled = explode(',', $iaCore->get('comments_items_enabled'));

    if ($packageItems) {
        $items = array();
        foreach ($packageItems as $item) {
            if (in_array($item, $itemsEnabled)) {
                $items[] = $item;
            }
        }

        if ($items) {
            $iaDb->setTable($iaComment::getTable());
            switch ($action) {
                case 'activate':
                    $iaDb->update(array('status' => iaCore::STATUS_ACTIVE),
                        "`item` IN ('" . implode("','", $items) . "')");

                    break;

                case 'deactivate':
                    $iaDb->update(array('status' => iaCore::STATUS_INACTIVE),
                        "`item` IN ('" . implode("','", $items) . "')");

                    break;

                case 'uninstall':
                    $iaDb->delete("`item` IN ('" . implode("','", $items) . "')");

                    break;

            }
            $iaDb->resetTable();
        }
    }
}