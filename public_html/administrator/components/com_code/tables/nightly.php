<?php
/**
 * @version		$Id: nightly.php 398 2010-06-13 17:53:03Z louis $
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
 * Code nightly build table object.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeTableNightly extends JTable
{
	/**
	 * @var int Primary key
	 */
	public $build_id;

	/**
	 * @var	int	Foreign key to #__code_projects.project_id
	 */
	public $project_id;

	/**
	 * @var int Foreign key to #__code_branches.branch_id
	 */
	public $branch_id;

	/**
	 * @var	string	The programming language for which the snippet is written.
	 */
	public $changelog;

	/**
	 * @var	int	The publishing state of the snippet.
	 */
	public $published;

	/**
	 * @var	string	The date when the build was generated.
	 */
	public $build_date;

	/**
	 * @var	int	The total number of unit tests executed.
	 */
	public $ut_tests;

	/**
	 * @var	int	The total number of unit test assertions made.
	 */
	public $ut_assertions;

	/**
	 * @var	int	The total number of unit test failures.
	 */
	public $ut_failures;

	/**
	 * @var	int	The total number of unit test errors.
	 */
	public $ut_errors;

	/**
	 * @var	float	The percentage of unit tests that passed.
	 */
	public $ut_pass_pct;

	/**
	 * @var	float	The percentage of unit tests that failed.
	 */
	public $ut_fail_pct;

	/**
	 * @var	float	The percentage of unit tests that errored out.
	 */
	public $ut_error_pct;

	/**
	 * @var	string	The serialized unit test report.
	 */
	public $ut_report;

	/**
	 * @var	string	The serialized unit test delta with the previous build.
	 */
	public $ut_delta;

	/**
	 * @var	int	The total number of system tests executed.
	 */
	public $st_tests;

	/**
	 * @var	int	The total number of system test assertions made.
	 */
	public $st_assertions;

	/**
	 * @var	int	The total number of system test failures.
	 */
	public $st_failures;

	/**
	 * @var	int	The total number of system test errors.
	 */
	public $st_errors;

	/**
	 * @var	float	The percentage of system tests that passed.
	 */
	public $st_pass_pct;

	/**
	 * @var	float	The percentage of system tests that failed.
	 */
	public $st_fail_pct;

	/**
	 * @var	float	The percentage of system tests that errored out.
	 */
	public $st_error_pct;

	/**
	 * @var	string	The serialized system test report.
	 */
	public $st_report;

	/**
	 * @var	string	The serialized system test delta with the previous build.
	 */
	public $st_delta;

	/**
	 * @var	int		The total number of lines of code.
	 */
	public $loc;

	/**
	 * @var	int		The total number of lines of code covered by unit tests.
	 */
	public $loc_covered;

	/**
	 * @var	float	The percentage of lines of code covered by unit tests.
	 */
	public $loc_covered_pct;

	/**
	 * @var	int		The total number of methods.
	 */
	public $methods;

	/**
	 * @var	int		The total number of methods covered by unit tests.
	 */
	public $methods_covered;

	/**
	 * @var	float	The percentage of methods covered by unit tests.
	 */
	public $methods_covered_pct;

	/**
	 * Class constructor.
	 *
	 * @param	object	A database connector object.
	 * @return	void
	 * @since	1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__code_nightly_builds', 'build_id', $db);
	}
}
