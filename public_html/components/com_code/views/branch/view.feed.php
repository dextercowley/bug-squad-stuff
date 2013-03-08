<?php
/**
 * @version		$Id: view.feed.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.view');

/**
 * The Feed Joomla Code branch view.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeViewBranch extends JView
{
	function display()
	{
		$app = JFactory::getApplication();

		$siteEmail = $app->getCfg('mailfrom');

		$doc	= &JFactory::getDocument();
		$params = &$app->getParams();

		$state	= $this->get('State');
		$branch	= $this->get('Item');
		$builds	= $this->get('Builds');

		$doc->title = $branch->title.' Build Log';
		$doc->description = html_entity_decode($this->escape($branch->description));
		$doc->link = JRoute::_('index.php?option=com_code&view=branch&branch_path='.$branch->path);

		foreach ($builds as $row)
		{
			// Build the description.
			$html = array();

			$html[] = '<p>';
			$html[] = $row->log;
			$html[] = '</p>';

			if ($row->changelog) {
				$cl = json_decode($row->changelog);
				$html[] = '<ul>';
				foreach ($cl->paths as $path) {
					$html[] = '<li>';
					switch ($path->action)
					{
						case 'D':
							$html[] = 'Removed: '.$path->path;
							break;
						case 'A':
							$html[] = 'Added: '.$path->path;
							break;
						default:
							$html[] = 'Modified: '.$path->path;
							break;
					}
					$html[] = '</li>';
				}
				$html[] = '</ul>';
			}

			// Create & populate the feed item object.
			$item = new JFeedItem();
			$item->title 		= $branch->title.' Build '.$row->revision_id;
			$item->link 		= JRoute::_('index.php?option=com_code&view=build&revision_id='.$row->revision_id);
			$item->description 	= implode($html);
			$item->date			= $row->commit_date;
			$item->author		= $row->user_name;
			$item->authorEmail	= $siteEmail;
//			$item->category   	= $row->category;

			// loads item info into rss array
			$doc->addItem($item);
		}
	}
}
