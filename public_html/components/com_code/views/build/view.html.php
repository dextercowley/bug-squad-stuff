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
 * The HTML Joomla Code build view.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeViewBuild extends JView
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
		$user  = JFactory::getUser();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Add the title to the breadcrumbs.
		JFactory::getApplication()->getPathWay()->addItem($item->branch_title, JRoute::_('index.php?option=com_code&view=branch&branch_id='.$item->branch_id));
		JFactory::getApplication()->getPathWay()->addItem(JText::sprintf('COM_CODE_BUILD_N', $item->revision_id), JRoute::_('index.php?option=com_code&view=build&revision_id='.$item->revision_id));

		$this->assignRef('state', $state);
		$this->assignRef('item', $item);
		$this->assignRef('user', $user);

		parent::display($tpl);
	}
}