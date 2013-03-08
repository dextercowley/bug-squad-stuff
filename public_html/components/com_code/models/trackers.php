<?php
/**
 * @version		$Id: trackers.php 410 2010-06-21 02:51:34Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.model');

/**
 * Trackers Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelTrackers extends JModel
{
	public function getItems()
	{
		// Initialize variables.
		$items = array();

		// Get the list of active branches.
		$this->_db->setQuery(
			'SELECT a.*' .
			' FROM #__code_trackers AS a' .
//			' WHERE a.published = 1' .
			' ORDER BY a.title ASC'
		);
		$items = $this->_db->loadObjectList();

		if ($this->_db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

		return $items;
	}
}