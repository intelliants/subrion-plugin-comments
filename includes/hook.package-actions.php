<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$action = $iaCore->requestPath[1];

	$iaComment = $iaCore->factoryPlugin('comments', iaCore::ADMIN, 'comment');
	$packageItems = $iaDb->onefield('item', "`package` = '{$extra}'", null, null, 'items');
	$itemsEnabled = explode(',', $iaCore->get('comments_items_enabled'));

	if ($packageItems)
	{
		$items = array();
		foreach ($packageItems as $item)
		{
			if (in_array($item, $itemsEnabled))
			{
				$items[] = $item;
			}
		}

		if ($items)
		{
			$iaDb->setTable($iaComment::getTable());
			switch ($action)
			{
				case 'activate':
					$iaDb->update(array('status' => iaCore::STATUS_ACTIVE), "`item` IN ('" . implode("','", $items) . "')");

					break;

				case 'deactivate':
					$iaDb->update(array('status' => iaCore::STATUS_INACTIVE), "`item` IN ('" . implode("','", $items) . "')");

					break;

				case 'uninstall':
					$iaDb->delete("`item` IN ('" . implode("','", $items) . "')");

					break;

			}
			$iaDb->resetTable();
		}
	}
}