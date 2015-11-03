<?php
//##copyright##

if (empty($item))
{
	return;
}

$enabledItems = $iaCore->get('comments_items_enabled');

if (empty($enabledItems))
{
	return;
}

$enabledItems = explode(',', $enabledItems);

if (empty($enabledItems) || !is_array($enabledItems))
{
	return;
}

if (in_array($item, $enabledItems) && $iaCore->get('comments_enabled'))
{
	$iaComment = $iaCore->factoryPlugin('comments', iaCore::FRONT, 'comment');

	$comments = $iaComment->getByItem($item, $listing);

	if ($iaCore->get('text_smiles_to_graphic'))
	{
		$comments = iaComment::smilesToImages($comments);
	}

	$iaView->assign('comments', $comments);
	$iaView->assign('comments_item', $item);

	// add custom styles for decoration
	$iaView->add_css('_IA_URL_plugins/comments/templates/front/css/style');
}