<?php
/**
 * @version		$Id: trackerissueresponse.php 398 2010-06-13 17:53:03Z louis $
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
 * Code tracker issue message table object.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeTableTrackerIssueResponse extends JTable
{
	/**
	 * @var int Primary key
	 */
	public $response_id;

	/**
	 * @var int Primary key
	 */
	public $change_id;

	/**
	 * @var int Primary key
	 */
	public $issue_id;

	/**
	 * @var int Primary key
	 */
	public $tracker_id;

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
	public $body;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_response_id;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_issue_id;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_tracker_id;

	/**
	 * @var	int	Foreign key to #__code_builds.build_id
	 */
	public $jc_created_by;

	/**
	 * Class constructor.
	 *
	 * @param	object	A database connector object.
	 * @return	void
	 * @since	1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__code_tracker_issue_responses', 'response_id', $db);
	}

	public function loadByLegacyId($legacyId)
	{
		// Look up the user id based on the legacy id.
		$this->_db->setQuery(
			'SELECT '.$this->_tbl_key .
			' FROM '.$this->_tbl .
			' WHERE jc_response_id = '.(int) $legacyId
		);
		$issueId = (int) $this->_db->loadResult();

		if ($issueId) {
			return $this->load($issueId);
		}
		else {
			return false;
		}
	}
}
