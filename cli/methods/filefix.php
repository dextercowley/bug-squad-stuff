<?php
/**
 * @version		$Id: filefix.php 431 2010-06-26 00:00:04Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.model');
jimport('joomla.database.table');

// Define the component path.
defined('JPATH_COMPONENT') OR define('JPATH_COMPONENT', realpath(JPATH_BASE.'/components/com_code'));

// Set the include paths for com_code models and tables.
JModel::addIncludePath(realpath(JPATH_BASE.'/components/com_code/models'));
JTable::addIncludePath(realpath(JPATH_BASE.'/administrator/components/com_code/tables'));

/**
 * File Fix Method Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class FileFixMethod
{
	private $_log = null;
	private $_time = null;
	private $_qtime = null;

	public function run($limit)
	{
		// Get the tracker sync model.
		$model = JModel::getInstance('TrackerSync', 'CodeModel');

		// Run the syncronization routine.
		$model->filefix();
	}

	private function _log($message)
	{
	        echo $message;
	        $this->_log += $message;
	}
}