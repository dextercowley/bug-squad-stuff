<?php
/**
 * @version		$Id: summary.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.model');

/**
 * Summary Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelSummary extends JModel
{
	public function getBranches()
	{
		// Initialize variables.
		$items = array();

		// Get the list of active branches.
		$this->_db->setQuery(
			'SELECT a.*, b.*' .
			' FROM #__code_branches AS a' .
			' INNER JOIN #__code_builds AS b ON a.last_build_id = b.build_id' .
			' WHERE a.published = 1' .
			' GROUP BY a.branch_id' .
			' ORDER BY b.branch_id ASC'
		);
		$items = $this->_db->loadObjectList();

		if ($this->_db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

		return $items;
	}
}