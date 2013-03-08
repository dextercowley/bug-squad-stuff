<?php
/**
 * @version		$Id: controller.php 456 2010-10-07 17:56:30Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.controller');

/**
 * Code Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_code
 * @since		1.6
 */
class CodeController extends JController
{
	/**
	 * Display the view
	 */
	function display()
	{
		// Set the default view name and format from the Request.
		$vName = JRequest::getWord('view', 'summary');
		JRequest::setVar('view', $vName);

		$user = & JFactory::getUser();

		$cachable = true;

		$safeurlparams = array(
			'catid' => 'INT',
			'id' => 'INT',
			'cid' => 'ARRAY',
			'year' => 'INT',
			'month' => 'INT',
			'limit' => 'INT',
			'limitstart' => 'INT',
			'showall' => 'INT',
			'return' => 'BASE64',
			'filter' => 'STRING',
			'filter_order' => 'CMD',
			'filter_order_Dir' => 'CMD',
			'filter-search' => 'STRING',
			'print' => 'BOOLEAN',
			'lang' => 'CMD'
		);

		parent :: display($cachable, $safeurlparams);
	}

	public function tracker_change_notification()
	{
		// Verify the request token.
		$token = JRequest::getString('token', null, 'method');
		if ($token != '1q2w3e4r') {
			JError::raiseError(403, 'Access Forbidden');
		}

		// Get some values from the request.
		$trackerId	= JRequest::getInt('tracker_id');
		$issueId	= JRequest::getInt('tracker_item_id');

		// Get the tracker sync model.
		$model = $this->getModel('TrackerSync');

		// Attempt to scan for available builds.
		if (!($success = $model->syncIssue($issueId, $trackerId))) {
			JError::raiseError(500, JText::sprintf('COM_CODE_ISSUE_SYNC_FAILURE', $model->getError()));
		}

		echo JText::_('COM_CODE_ISSUE_SYNC_SUCCESS');
	}
}
