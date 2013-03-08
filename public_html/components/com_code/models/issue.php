<?php
/**
 * @version		$Id: issue.php 410 2010-06-21 02:51:34Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.model');

/**
 * Issue Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelIssue extends JModel
{

	public function getItem($issueId = null)
	{
		$issueId = empty($issueId) ? JRequest::getInt('issue_id') : $issueId;

		$db = JFactory::getDBO();

		$db->setQuery(
			'SELECT a.*' .
			' FROM #__code_tracker_issues AS a' .
			' WHERE a.issue_id = '.(int) $issueId
		);
		$item = $db->loadObject();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

		return $item;
	}

	public function getTags($issueId = null)
	{
		$issueId = empty($issueId) ? JRequest::getInt('issue_id') : $issueId;

		$db = JFactory::getDBO();

		$db->setQuery(
			'SELECT a.*' .
			' FROM #__code_tracker_issue_tag_map AS a' .
			' WHERE a.issue_id = '.(int) $issueId .
			' ORDER BY a.tag ASC'
		);
		$items = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

//		if ($item->cache) {
//			$item->cache = json_decode($item->cache);
//		}

		return $items;
	}
}
