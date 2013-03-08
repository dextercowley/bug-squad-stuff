<?php
/**
 * @version		$Id: view.html.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.view');

/**
 * The HTML Joomla Code summary view.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeViewSummary extends JView
{
	/**
	 * Display the view
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function display($tpl = null)
	{
		$state	= $this->get('State');
		$items	= $this->get('Branches');
		$user	= JFactory::getUser();
		$params	= JFactory::getApplication()->getParams('com_code');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state', $state);
		$this->assignRef('items', $items);
		$this->assignRef('user', $user);
		$this->assignRef('params', $params);

		parent::display($tpl);
	}
}