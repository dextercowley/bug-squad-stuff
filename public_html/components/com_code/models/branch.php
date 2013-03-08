<?php
/**
 * @version		$Id: branch.php 442 2010-09-16 17:47:54Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.model');
JLoader::register('CodeHelperReports', JPATH_SITE.'/components/com_code/helpers/reports.php');

/**
 * Branch Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelBranch extends JModel
{

	protected $builds;
	protected $pagination;

	public function getItem($branchId = null)
	{
		$branchId = empty($branchId) ? JRequest::getInt('branch_id', 1) : $branchId;

		$db = JFactory::getDBO();

		$db->setQuery(
			'SELECT a.*' .
			' FROM #__code_branches AS a' .
			' WHERE a.branch_id = '.(int) $branchId
		);
		$item = $db->loadObject();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

		return $item;
	}

	public function getLatestBuild($branchId = null)
	{
		$branchId = empty($branchId) ? JRequest::getInt('branch_id', 1) : $branchId;

		$db = JFactory::getDBO();

		$db->setQuery(
			'SELECT a.*' .
			' FROM #__code_builds AS a' .
			' WHERE a.branch_id = '.(int) $branchId .
			' ORDER BY a.commit_date DESC',
			0, 1
		);
		$item = $db->loadObject();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

//		if ($item->cache) {
//			$item->cache = json_decode($item->cache);
//		}

		return $item;
	}


	/**
	 * Get the articles in the category
	 *
	 * @return	mixed	An array of articles or false if an error occurs.
	 */
	function getItems($branchId = null)
	{
		$branchId = empty($branchId) ? JRequest::getInt('branch_id', 1) : $branchId;

		if ($this->builds === null && $branch = $this->getItem()) {
			$model = JModel::getInstance('Builds', 'CodeModel', array('ignore_request' => true));

			$model->setState('options', $this->getState('options'));
			$model->setState('filter.branch_id', $branchId);
			$model->setState('filter.access', $this->getState('filter.access'));
			$model->setState('list.start', $this->getState('list.start'));
			$model->setState('list.ordering', $this->getState('list.ordering'));
			$model->setState('list.direction', $this->getState('list.direction'));
			$model->setState('list.limit', $this->getState('list.limit'));
			$model->setState('list.filter', $this->getState('list.filter'));

			$this->builds = $model->getItems();

			if ($this->builds === false) {
				$this->setError($model->getError());
			}

			$this->pagination = $model->getPagination();
		}

		return $this->builds;

	}

	public function getPagination()
	{
		$this->getItems();

		return $this->pagination;
	}

	public function getLastBuilds($branchId = null)
	{
		$branchId = empty($branchId) ? JRequest::getInt('branch_id', 1) : $branchId;

		$db = JFactory::getDBO();

		$db->setQuery(
			'SELECT a.*' .
			' FROM #__code_builds AS a' .
			' WHERE a.branch_id = '.(int) $branchId .
			' ORDER BY a.commit_date DESC',
			0, 20
		);
		$items = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

//		if ($item->cache) {
//			$item->cache = json_decode($item->cache);
//		}

		return $items;
	}

	public function getBuilds($branchId = null)
	{
		$branchId = empty($branchId) ? JRequest::getInt('branch_id', 1) : $branchId;

		$db = JFactory::getDBO();

		$db->setQuery(
			'SELECT a.*' .
			' FROM #__code_builds AS a' .
			' WHERE a.branch_id = '.(int) $branchId .
			' ORDER BY a.commit_date DESC',
			0, JFactory::getApplication()->getCfg('feed_limit')
		);
		$items = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

//		if ($item->cache) {
//			$item->cache = json_decode($item->cache);
//		}

		return $items;
	}

	public function scanBuilds($path = 'trunk')
	{
		// Get the branch ID based on path.
		$this->_db->setQuery(
			'SELECT branch_id' .
			' FROM #__code_branches' .
			' WHERE path = '.$this->_db->quote($path) .
			' AND project_id = 1'
		);
		$branchId = (int) $this->_db->loadResult();

		// Get the list of build folders and store build reports.
		$builds = $this->_getAvailableBuilds($branchId);
		foreach ($builds as $revisionId => $path)
		{
			if ($this->_storeBuildReports($branchId, $revisionId, $path)) {
				// Remove the path as it is now parsed and stored.
				JFolder::delete($path);
			} else {
				return false;
			}
		}

		// Process delta reports.
		foreach ($builds as $revisionId => $path)
		{
			if (!$this->_storeReportDeltas($revisionId, $branchId)) {
				return false;
			}
		}

		// Get a branch table object and set the updated time.
		$branch = $this->getTable('Branch', 'CodeTable');

		// Load the current branch information.
		if (!$branch->load($branchId)) {
			$this->setError($branch->getError());
			return false;
		}

		// Set the updated date data.
		$branch->updated_date = JFactory::getDate()->toMySQL();

		// Check to see if there is already a build row.
		$this->_db->setQuery(
			'SELECT build_id' .
			' FROM #__code_builds' .
			' WHERE branch_id = '.(int) $branchId .
			' ORDER BY commit_date DESC',
			0, 1
		);
		$lastBuildId = (int) $this->_db->loadResult();

		$branch->last_build_id = $lastBuildId;

		// Run table object sanity checks before storing the row.
		if (!$branch->check()) {
			$this->setError($branch->getError());
			return false;
		}

		// Attempt to store the build row to the database.
		if (!$branch->store()) {
			$this->setError($branch->getError());
			return false;
		}

		return true;
	}

	public function fix($path = 'trunk')
	{
		// Fix the project asset info.
		$project = $this->getTable('Project', 'CodeTable');
		if (!$project->load(1)) {
			$this->setError($project->getError());
			return false;
		}
		$project->store();

		// Get the branches to fix.
		$this->_db->setQuery(
			'SELECT branch_id' .
			' FROM #__code_branches'
		);
		$branches = $this->_db->loadResultArray();

		// Fix the branches.
		foreach ($branches as $branch) {
			$row = $this->getTable('Branch', 'CodeTable');
			if (!$row->load($branch)) {
				$this->setError($row->getError());
				return false;
			}
			$row->store();
		}

		// Get the trackers to fix.
		$this->_db->setQuery(
			'SELECT tracker_id' .
			' FROM #__code_trackers'
		);
		$trackers = $this->_db->loadResultArray();

		// Fix the trackers.
		foreach ($trackers as $tracker) {
			$row = $this->getTable('Tracker', 'CodeTable');
			if (!$row->load($tracker)) {
				$this->setError($row->getError());
				return false;
			}
			$row->store();
		}


		// Fix the build <-> user mapping.
		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 47' .
			' WHERE user_name = "dextercowley"'
		);
		$this->_db->query();

		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 49' .
			' WHERE user_name = "severdia"'
		);
		$this->_db->query();

		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 856' .
			' WHERE user_name = "hackwar"'
		);
		$this->_db->query();

		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 42' .
			' WHERE user_name = "louis"'
		);
		$this->_db->query();

		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 50' .
			' WHERE user_name = "infograf768"'
		);
		$this->_db->query();

		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 48' .
			' WHERE user_name = "pasamio"'
		);
		$this->_db->query();

		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 44' .
			' WHERE user_name = "eddieajau"'
		);
		$this->_db->query();

		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 46' .
			' WHERE user_name = "chdemko"'
		);
		$this->_db->query();

		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 45' .
			' WHERE user_name = "chrisdavenport"'
		);
		$this->_db->query();

		$this->_db->setQuery(
			'UPDATE #__code_builds' .
			' SET user_id = 43' .
			' WHERE user_name = "ian"'
		);
		$this->_db->query();

		return true;


//		// Get the branch ID based on path.
//		$this->_db->setQuery(
//			'SELECT branch_id' .
//			' FROM #__code_branches' .
//			' WHERE path = '.$this->_db->quote($path)
//		);
//		$branchId = (int) $this->_db->loadResult();
//
//		// Clean all of the delta fields.
//		$this->_db->setQuery(
//			'UPDATE #__code_builds' .
//			' SET ut_delta = "", st_delta = ""' .
//			' WHERE branch_id = '. (int) $branchId
//		);
//		$this->_db->query();
//
//		// Get the list of build folders and store build reports.
//		$this->_db->setQuery(
//			'SELECT revision_id' .
//			' FROM #__code_builds' .
//			' WHERE branch_id = '. (int) $branchId
//		);
//		$builds = (array) $this->_db->loadResultArray();
//
//		foreach ($builds as $revisionId)
//		{
//			if (!$this->_storeReportDeltas($revisionId, $branchId)) {
//				return false;
//			}
//		}
//
//		return true;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Set the project id from the request.
		$pk = JRequest::getInt('branch_id');
		$this->setState('branch.id', $pk);

		// Load the component/page options from the application.
		$this->setState('options', $app->getParams('com_code'));

		// Set the access filter to true by default.
		$this->setState('filter.access', 1);

		// Set the state filter.
		//$this->setState('filter.state', 1);

		// Set the optional filter search string text.
		//$this->setState('filter.search', JRequest::getString('filter-search'));

		// Set the tracker issue filter.
		//$this->setState('filter.issue_id', 1);
		//$this->setState('filter.issue_id.include', 1);

		// Set the branch filter.
		//$this->setState('filter.branch_id', 1);
		//$this->setState('filter.branch_id.include', 1);

		// Set the author filter.
		//$this->setState('filter.author_id', 1);
		//$this->setState('filter.author_id.include', 1);

		// Set the date filters.
		//$this->setState('filter.date_filtering', null);
		//$this->setState('filter.start_date_range', null);
		//$this->setState('filter.end_date_range', null);
		//$this->setState('filter.relative_date', null);

		// Load the list options from the request.
		$listId = $pk.':'.JRequest::getInt('Itemid', 0);
		$this->setState('list.start', JRequest::getInt('limitstart', 0));
		$this->setState('list.ordering', $app->getUserStateFromRequest('com_code.branch.'.$listId.'.filter_order', 'filter_order', 'a.commit_date', 'string'));
		$this->setState('list.direction', $app->getUserStateFromRequest('com_code.branch.'.$listId.'.filter_order_Dir', 'filter_order_Dir', 'DESC', 'cmd'));
		$this->setState('list.limit', $app->getUserStateFromRequest('com_code.branch.'.$listId.'.limit', 'limit', $app->getCfg('list_limit'), 'int'));
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('branch.id');
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.state');
		$id .= ':'.$this->getState('filter.issue_id');
		$id .= ':'.$this->getState('filter.issue_id.include');
		$id .= ':'.$this->getState('filter.author_id');
		$id .= ':'.$this->getState('filter.author_id.include');
		$id .= ':'.$this->getState('filter.date_filtering');
		$id .= ':'.$this->getState('filter.start_date_range');
		$id .= ':'.$this->getState('filter.end_date_range');
		$id .= ':'.$this->getState('filter.relative_date');
		$id .= ':'.$this->getState('filter.search');

		return parent::getStoreId($id);
	}

	private function _getAvailableBuilds($branchId)
	{
		$this->_db->setQuery(
			'SELECT a.path' .
			' FROM #__code_branches AS a' .
			' WHERE a.branch_id = '.(int) $branchId
		);
		$branchPath = $this->_db->loadResult();

		if ($this->_db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

		// This will eventually be in a configuration object.
		$path = dirname(JPATH_ROOT).'/builds/commit/'.$branchPath;

		// Get the list of build folders.
		$folders = JFolder::folders($path);

		// Build the list of available builds.
		$builds = array();
		foreach ($folders as $folder) {

			// Ignore invalid builds.
			if (!(int) $folder) {
				continue;
			}

			// Get a list of the files in the build folder.
			$files = JFolder::files($path.'/'.$folder);

			// Check that the proper files exist.
			if (in_array('changelog_report.xml', $files)) {
				$builds[(int) $folder] = $path.'/'.$folder;
			} else {
				JFolder::delete($path.'/'.$folder);
			}
		}

		return $builds;
	}

	private function _storeBuildReports($branchId, $revisionId, $path)
	{
		// Get a build table object and set the build id.
		$build = $this->getTable('Build', 'CodeTable');

		// Check to see if there is already a build row.
		$this->_db->setQuery(
			'SELECT build_id' .
			' FROM #__code_builds' .
			' WHERE revision_id = '.(int) $revisionId
		);
		$buildId = (int) $this->_db->loadResult();

		// If a row already exists for the build, load it.
		if ($buildId) {
			$build->load($buildId);
		}

		$build->branch_id = $branchId;
		$build->published = 1;

		// Populate the changelog based fields.
		$changelogs = CodeHelperReports::getChangelogReport($path);
		$changelog = $changelogs[0];

		$build->changelog = json_encode($changelog);
		$build->revision_id = (int) $revisionId;
		$build->user_name = $changelog->user_name;
		$build->log = $changelog->log;

		// Work out the user id mapping if possible.
		$this->_db->setQuery(
			'SELECT id' .
			' FROM #__users' .
			' WHERE username = '.$this->_db->quote($changelog->user_name)
		);
		$userId = (int) $this->_db->loadResult();
		if ($userId) {
			$build->user_id = $userId;
		}

		// Populate the commit date based on the changelog.
		$date = JFactory::getDate($changelog->commit_date);
		$build->commit_date = $date->toMySQL();

		// Populate the unit testing based fields.
		$report = CodeHelperReports::getUnitTestReport($path);

		$build->ut_report = json_encode($report);
		$build->ut_tests = $report->total_tests;
		$build->ut_assertions = $report->total_assertions;
		$build->ut_failures = $report->total_failures;
		$build->ut_errors = $report->total_errors;
		if (!$report->total_tests) {
			$build->ut_pass_pct  = 0;
			$build->ut_fail_pct  = 0;;
			$build->ut_error_pct = 0;
		} else {
			$build->ut_pass_pct  = ($report->total_tests - $report->total_failures - $report->total_errors) / $report->total_tests * 100;
			$build->ut_fail_pct  = $report->total_failures / $report->total_tests * 100;
			$build->ut_error_pct = $report->total_errors / $report->total_tests * 100;
		}

		// Populate the system testing based fields.
		$report = CodeHelperReports::getSystemTestReport($path);

		$build->st_report = json_encode($report);
		$build->st_tests = $report->total_tests;
		$build->st_assertions = $report->total_assertions;
		$build->st_failures = $report->total_failures;
		$build->st_errors = $report->total_errors;
		if (!$report->total_tests) {
			$build->st_pass_pct  = 0;
			$build->st_fail_pct  = 0;;
			$build->st_error_pct = 0;
		} else {
			$build->st_pass_pct  = ($report->total_tests - $report->total_failures - $report->total_errors) / $report->total_tests * 100;
			$build->st_fail_pct  = $report->total_failures / $report->total_tests * 100;
			$build->st_error_pct = $report->total_errors / $report->total_tests * 100;
		}

		// Populate the code coverage based fields.
		$report = CodeHelperReports::getCodeCoverageReport($path);

		$build->loc = $report->loc;
		$build->loc_covered = $report->loc_covered;
		$build->loc_covered_pct = $report->loc_covered_pct;
		$build->methods = $report->methods;
		$build->methods_covered = $report->methods_covered;
		$build->methods_covered_pct = $report->methods_covered_pct;

		// Run table object sanity checks before storing the row.
		if (!$build->check()) {
			$this->setError($build->getError());
			return false;
		}

		// Attempt to store the build row to the database.
		if (!$build->store()) {
			$this->setError($build->getError());
			return false;
		}

		return true;
	}

	private function _storeReportDeltas($revisionId, $branchId)
	{
		// Get a build table object and set the build id.
		$build = $this->getTable('Build', 'CodeTable');

		// Check to see if there is already a build row.
		$this->_db->setQuery(
			'SELECT build_id' .
			' FROM #__code_builds' .
			' WHERE revision_id = '.(int) $revisionId
		);
		$buildId = (int) $this->_db->loadResult();

		// If a row already exists for the build, load it.
		if ($buildId) {
			$build->load($buildId);
		}
		// If there is no build by revision, just return true.
		else {
			return true;
		}

		// Get the previous build data.
		$this->_db->setQuery(
			'SELECT build_id, revision_id, st_report, ut_report, ut_tests, st_tests' .
			' FROM #__code_builds' .
			' WHERE revision_id < '.(int) $revisionId .
			' AND branch_id = '.(int) $branchId .
			' ORDER BY revision_id DESC',
			0, 1
		);
		$previousBuild = $this->_db->loadObject();

		// If there is no previous build to generate deltas set default.
		if (!$previousBuild) {
			$build->ut_delta = json_encode(array('-' => array(), '+' => array()));
			$build->st_delta = json_encode(array('-' => array(), '+' => array()));
		}
		else {

			// Get the previous build unit test failures.
			$old = json_decode($previousBuild->ut_report);
			$old = (array) $old->failures;

			// Get the current build unit test failures.
			$new = json_decode($build->ut_report);
			$new = (array) $new->failures;

			// Get the unit test delta between the build assuming the previous build had any testss.
			if ($previousBuild->ut_tests) {
				$delta = CodeHelperReports::getReportDelta($new, $old);
				$build->ut_delta = json_encode($delta);
			}
			else {
				$build->ut_delta = json_encode(array('-' => array(), '+' => array()));
			}

			// Get the previous build system test failures.
			$old = json_decode($previousBuild->st_report);
			$old = (array) $old->failures;

			// Get the current build system test failures.
			$new = json_decode($build->st_report);
			$new = (array) $new->failures;

			// Get the system test delta between the build assuming the previous build had any testss.
			if ($previousBuild->st_tests) {
				$delta = CodeHelperReports::getReportDelta($new, $old);
				$build->st_delta = json_encode($delta);
			}
			else {
				$build->st_delta = json_encode(array('-' => array(), '+' => array()));
			}
		}

		// Run table object sanity checks before storing the row.
		if (!$build->check()) {
			$this->setError($build->getError());
			return false;
		}

		// Attempt to store the build row to the database.
		if (!$build->store()) {
			$this->setError($build->getError());
			return false;
		}

		return true;
	}
}
