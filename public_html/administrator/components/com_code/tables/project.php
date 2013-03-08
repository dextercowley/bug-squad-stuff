<?php
/**
 * @version		$Id: project.php 417 2010-06-25 01:01:45Z louis $
 * @package		Joomla.Administrator
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.database.table');

/**
 * Code branch table object.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeTableProject extends JTable
{
	/**
	 * Contructor.
	 *
	 * @param database A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__code_projects', 'project_id', $db);

		$this->access = (int) JFactory::getConfig()->get('access');
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return	string
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_code.project.'.(int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return	string
	 * @since	1.6
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @return	int
	 */
	protected function _getAssetParentId()
	{
		// Initialise variables.
		$assetId = null;
		$db		= $this->getDbo();

		// Build the query to get the asset id for the component.
		$query	= $db->getQuery(true);
		$query->select('id');
		$query->from('#__assets');
		$query->where('name = '.$db->quote('com_code'));

		// Get the asset id from the database.
		$db->setQuery($query);
		if ($result = $db->loadResult()) {
			$assetId = (int) $result;
		}

		// Return the asset id.
		if ($assetId) {
			return $assetId;
		} else {
			return parent::_getAssetParentId();
		}
	}
}