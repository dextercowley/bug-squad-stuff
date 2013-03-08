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
 * The HTML Joomla Code help view.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeViewHelp extends JView
{
	/**
	 * Display the view
	 *
	 * @return	void
	 * @since	1.0
	 */
	public function display($tpl = null)
	{
		// Add the title to the breadcrumbs.
		JFactory::getApplication()->getPathWay()->addItem(JText::_('COM_CODESTATUS_HELP'), JRoute::_('index.php?option=com_code&view=help'));

		parent::display($tpl);
	}
}