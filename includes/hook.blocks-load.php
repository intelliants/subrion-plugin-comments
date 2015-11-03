<?php
//##copyright##

if (iaView::REQUEST_HTML == $iaView->getRequestType() && $iaView->blockExists('latest_comments'))
{
	$iaItem = $iaCore->factory('item');
	$iaComment = $iaCore->factoryPlugin('comments', iaCore::FRONT, 'comment');

	$array = $iaComment->getLatest($iaCore->get('num_latest_comments'));
	$total = $iaDb->foundRows();

	foreach ($array as $key => $comment)
	{
		if ('members' == $comment['item'])
		{
			$iaUsers = $iaCore->factory('users');
			$itemData = $iaUsers->getInfo($comment['item_id']);
			$itemData['title'] = $itemData['fullname'] ? $itemData['fullname'] : $itemData['username'];
			$array[$key]['item_url'] = IA_URL . 'member/' . $itemData['username'] . '.html';
		}
		else
		{
			$itemPackage = $iaItem->getPackageByItem($comment['item']);
			$itemClass = $iaCore->factoryPackage('item', $itemPackage, iaCore::FRONT, $comment['item']);
			$itemData = $itemClass->getById($comment['item_id']);

			if(isset($itemData['model']))
			{
				$itemData['title'] = $itemData['model'];
			}

			$array[$key]['item_url'] = $itemClass->url('view', $itemData);
		}
		if(isset($itemData['title']))
		{
			$array[$key]['item_title'] = $itemData['title'];
		}

	}

	if ($iaCore->get('text_smiles_to_graphic'))
	{
		$array = iaComment::smilesToImages($array);
	}

	$iaView->assign('num_total_comments', $total);
	$iaView->assign('latest_comments', $array);
}