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

class iaComment extends abstractPlugin
{
	protected static $_table = 'comments';


	public function get($columns = null, $stmt = null, $start = 0, $limit = 0, $order = 't1.`date` DESC')
	{
		$stmtFields = $columns ? $columns : 't1.' . iaDb::ALL_COLUMNS_SELECTION;

		if (is_array($columns))
		{
			$stmtFields = '';
			foreach ($columns as $key => $field)
			{
				$stmtFields .= is_int($key)
					? 't1.`' . $field . '`'
					: sprintf('%s `%s`', is_numeric($field) ? $field : 't1.`' . $field . '`', $key);
				$stmtFields .= ', ';
			}
			$stmtFields = substr($stmtFields, 0, -2);
		}

		$sql = "SELECT " . $stmtFields . ", t2.`username` `username`, "
			. "IF (t1.`member_id` > 0, IF (t2.`fullname` != '', t2.`fullname`, t2.`username`), t1.`author`) `author`, "
			. "t2.`avatar` `author_avatar` "
			. "FROM `" . self::getTable(true) . "` t1 "
			. "LEFT JOIN `" . $this->iaDb->prefix . "members` t2 "
			. "ON t1.`member_id` = t2.`id` "
			. "WHERE " . ($stmt ? $stmt : '1 = 1')
			. " ORDER BY " . $order
			. ($limit ? " LIMIT " . $start . ", " . $limit : '');

		return $this->iaDb->getAll($sql);
	}

	public function getById($commentId)
	{
		$stmt = "t1.`id` = '{$commentId}' ";
		$comment = $this->get(null, $stmt, 0, 1);
		$comment = isset($comment[0]) ? $comment[0] : false;

		return $comment;
	}

	public function getByItem($itemName, $itemId, $start = 0, $limit = 0)
	{
		$stmt = "t1.`item` = '{$itemName}' "
			. " AND t1.`item_id` = '{$itemId}'"
			. " AND t1.`status` = '" . iaCore::STATUS_ACTIVE . "'";

		return $this->get(null, $stmt, $start, $limit);
	}

	public function getLatest($limit = 0)
	{
		$stmt = "t1.`status` = '" . iaCore::STATUS_ACTIVE . "'";

		return $this->get(null, $stmt, 0, $limit);
	}

	public static function smilesToImages(&$comments)
	{
		$map = array(
			':)' => '<img alt=":)" src="' . IA_URL . 'plugins/comments/templates/front/img/smiles/smile.png">',
			'=)' => '<img alt="=)" src="' . IA_URL . 'plugins/comments/templates/front/img/smiles/smile.png">',
			':(' => '<img alt=":(" src="' . IA_URL . 'plugins/comments/templates/front/img/smiles/sad.png">',
			'=(' => '<img alt="=(" src="' . IA_URL . 'plugins/comments/templates/front/img/smiles/sad.png">',
			':D' => '<img alt=":D" src="' . IA_URL . 'plugins/comments/templates/front/img/smiles/happy.png">',
		);

		$keys = array_keys($map);
		$values = array_values($map);

		foreach ($comments as &$entry)
		{
			$entry['body'] = str_replace($keys, $values, $entry['body']);
		}

		return $comments;
	}
}