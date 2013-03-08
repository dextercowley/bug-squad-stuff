<?php
/**
 * @version		$Id: view.html.php 410 2010-06-21 02:51:34Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.view');

/**
 * The HTML Joomla Code issue view.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeViewIssue extends JView
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
		$item	= $this->get('Item');
		$tags	= $this->get('Tags');
		$user	= JFactory::getUser();
		$params	= JFactory::getApplication()->getParams('com_code');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('state', $state);
		$this->assignRef('item', $item);
		$this->assignRef('tags', $tags);
		$this->assignRef('user', $user);
		$this->assignRef('params', $params);

		parent::display($tpl);
	}
}