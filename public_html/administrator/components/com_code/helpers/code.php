<?php
/**
 * @version		$Id: code.php 461 2010-10-30 15:58:47Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

/**
 * Code component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @since		1.6
 */
class CodeHelper
{
	public static $extention = 'com_code';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_CODE_SUBMENU_PROJECTS'),
			'index.php?option=com_code&view=projects',
			$vName == 'projects'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_CODE_SUBMENU_BRANCHES'),
			'index.php?option=com_code&view=branches',
			$vName == 'branches'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_CODE_SUBMENU_ABOUT'),
			'index.php?option=com_code&view=about',
			$vName == 'about'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 */
	public static function getActions()
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$assetName	= 'com_code';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public static function publishedOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '*', 'JALL');
		$options[]	= JHtml::_('select.option', '2', 'JARCHIVED');
		$options[]	= JHtml::_('select.option', '1', 'JENABLED');
		$options[]	= JHtml::_('select.option', '0', 'JDISABLED');
		$options[]	= JHtml::_('select.option', '-2', 'JTRASH');

		return $options;
	}

	/**
	 * Determines if the plugin for Code to work is enabled.
	 *
	 * @return	boolean
	 */
	public static function isEnabled()
	{
		$db = JFactory::getDbo();
		$db->setQuery(
			'SELECT enabled' .
			' FROM #__extensions' .
			' WHERE folder = '.$db->quote('system').
			'  AND element = '.$db->quote('code')
		);
		$result = (boolean) $db->loadResult();
		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}
		return $result;
	}
}