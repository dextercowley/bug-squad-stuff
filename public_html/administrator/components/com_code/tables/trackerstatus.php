<?php
/**
 * @version		$Id: trackerstatus.php 421 2010-06-25 02:50:14Z louis $
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
 * Code tracker issue status table object.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeTableTrackerStatus extends JTable
{
	/**
	 * @var int Primary key
	 */
	public $status_id;

	/**
	 * @var int Primary key
	 */
	public $tracker_id;

	/**
	 * @var int Primary key
	 */
	public $state_id;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $title;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $instructions;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_status_id;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_tracker_id;

	/**
	 * Class constructor.
	 *
	 * @param	object	A database connector object.
	 * @return	void
	 * @since	1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__code_tracker_status', 'status_id', $db);
	}

	public function loadByLegacyId($legacyId)
	{
		// Look up the row based on the legacy id.
		$this->_db->setQuery(
			'SELECT '.$this->_tbl_key .
			' FROM '.$this->_tbl .
			' WHERE jc_status_id = '.(int) $legacyId
		);
		$pk = (int) $this->_db->loadResult();

		if ($pk) {
			return $this->load($pk);
		}
		else {
			return false;
		}
	}
}
