<?php
/**
 * @version		$Id: tracker.php 417 2010-06-25 01:01:45Z louis $
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
 * Code tracker table object.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeTableTracker extends JTable
{
	/**
	 * @var int Primary key
	 */
	public $tracker_id;

	/**
	 * @var	int	Foreign key to #__users.id
	 */
	public $project_id;

	/**
	 * @var	string	The URI path to the branch.
	 */
	public $title;

	/**
	 * @var	string	The name of the branch.
	 */
	public $alias;

	/**
	 * @var	string	A description of the branch purpose.
	 */
	public $summary;

	/**
	 * @var	int	The publishing state of the branch.
	 */
	public $description;

	/**
	 * @var	string	The date/time when the branch was last updated.
	 */
	public $state;

	/**
	 * @var	string	The date/time when the branch was created.
	 */
	public $access;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $item_count;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $open_item_count;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $created_date;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $created_by;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $modified_date;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $modified_by;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_tracker_id;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_project_id;

	/**
	 * Class constructor.
	 *
	 * @param	object	A database connector object.
	 * @return	void
	 * @since	1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__code_trackers', 'tracker_id', $db);

		$this->access = (int) JFactory::getConfig()->get('access');
	}

	public function loadByLegacyId($legacyId)
	{
		// Look up the user id based on the legacy id.
		$this->_db->setQuery(
			'SELECT '.$this->_tbl_key .
			' FROM '.$this->_tbl .
			' WHERE jc_tracker_id = '.(int) $legacyId
		);
		$issueId = (int) $this->_db->loadResult();

		if ($issueId) {
			return $this->load($issueId);
		}
		else {
			return false;
		}
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
		return 'com_code.tracker.'.(int) $this->$k;
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

		// This is a tracker under a project.
		if ($this->project_id > 0) {
			// Build the query to get the asset id for the parent project.
			$query	= $db->getQuery(true);
			$query->select('asset_id');
			$query->from('#__code_projects');
			$query->where('project_id = '.(int) $this->project_id);

			// Get the asset id from the database.
			$db->setQuery($query);
			if ($result = $db->loadResult()) {
				$assetId = (int) $result;
			}
		}
		// This is a tracker that needs to parent with the extension.
		elseif ($assetId === null) {
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
		}

		// Return the asset id.
		if ($assetId) {
			return $assetId;
		} else {
			return parent::_getAssetParentId();
		}
	}
}
