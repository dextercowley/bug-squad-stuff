<?php
/**
 * @version		$Id: build.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');

/**
 * Build Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelBuild extends JModel
{

	public function getItem($revisionId = null)
	{
		$revisionId = empty($revisionId) ? JRequest::getInt('revision_id') : $revisionId;

		$db = JFactory::getDBO();

		$db->setQuery(
			'SELECT a.*, b.title AS branch_title, b.path AS branch_path' .
			' FROM #__code_builds AS a' .
			' LEFT JOIN #__code_branches AS b ON a.branch_id = b.branch_id' .
			' WHERE a.revision_id = '.(int) $revisionId
		);
		$item = $db->loadObject();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

		if ($item->changelog) {
			$item->changelog = json_decode($item->changelog);
		}

		if ($item->ut_delta) {
			$item->ut_delta = (array) json_decode($item->ut_delta);
		}

		if ($item->st_delta) {
			$item->st_delta = (array) json_decode($item->st_delta);
		}

		return $item;
	}
}