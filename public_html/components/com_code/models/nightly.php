<?php
/**
 * @version		$Id: nightly.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');
JLoader::register('CodeHelperReports', JPATH_SITE.'/components/com_code/helpers/reports.php');

/**
 * Nightly Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelNightly extends JModel
{

	public function getDownloads()
	{
		// Build the path to the nightly packages.
		$path = dirname(JPATH_ROOT).'/packages/nightly';

		// Load the manifest xml file.
		if (is_file($path.'/manifest.xml')) {
			$manifest = simplexml_load_file($path.'/manifest.xml');
		}

		// Check for errors.
		if (empty($manifest)) {
			return false;
		}

		// Build the return object.
		$download = new stdClass;
		$download->date = (string) $manifest['date'];
		$download->type = (string) $manifest['build'];

		foreach ($manifest->children() as $package) {
			if (is_file((string) $package['path'])) {
				$download->packages[] = array(
					'size' => (int) $package['size'],
					'md5' => (string) $package['md5'],
					'sha1' => (string) $package['sha1'],
					'file' => basename((string) $package['path'])
				);
			}
		}

		return $download;
	}

	public function getItem($buildId = null)
	{
		$buildId = empty($buildId) ? JRequest::getInt('build_id') : $buildId;

		$db = JFactory::getDBO();

		$db->setQuery(
			'SELECT a.*, b.title AS branch_title, b.path AS branch_path' .
			' FROM #__code_nightly_builds AS a' .
			' LEFT JOIN #__code_branches AS b ON a.branch_id = b.branch_id' .
			' WHERE a.project_id = 1' .
			($buildId ? ' AND a.build_id = '.(int) $buildId : '') .
			' AND a.published = 1' .
			' ORDER BY build_date DESC',
			0, 1
		);
		$item = $db->loadObject();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, 'Unable to access resource.');
		}

		if ($item->changelog) {
			$item->changelog = json_decode($item->changelog);
		}

		if ($item->ut_delta) {
			$item->ut_delta = (array) json_decode($item->ut_delta);
		}

		if ($item->st_delta) {
			$item->st_delta = (array) json_decode($item->st_delta);
		}

		return $item;
	}

	public function scanBuilds($path = 'trunk')
	{
		// Get the list of build folders.
		$builds = $this->_getAvailableBuilds();

		foreach ($builds as $date => $path) {

			// Get a build table object and set the build id.
			$build = $this->getTable('Nightly', 'CodeTable');

			// Check to see if there is already a build row.
			$this->_db->setQuery(
				'SELECT build_id' .
				' FROM #__code_nightly_builds' .
				' WHERE build_date = '.$this->_db->quote($date)
			);
			$buildId = (int) $this->_db->loadResult();

			// If a row already exists for the build, load it.
			if ($buildId) {
				$build->load($buildId);
			}

			// Set some build data for the nightly.
			$build->project_id = 1;
			$build->branch_id = 1;
			$build->published = 1;
			$build->build_date = $date;

			// Populate the changelog report.
			$changelog = CodeHelperReports::getChangelogReport($path);
			$build->changelog = json_encode($changelog);

			// Populate the unit testing report.
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
		}

		// Generate and store the test report deltas.
		foreach ($builds as $date => $path) {
			if (!$this->_storeReportDeltas($date, 1)) {
				return false;
			}
		}

		// Get the latest build date.
		$this->_db->setQuery(
			'SELECT build_date' .
			' FROM #__code_nightly_builds' .
			' WHERE branch_id = 1' .
			' ORDER BY build_date DESC',
			0, 1
		);
		$lastBuildDate = $this->_db->loadResult();

		if ($lastBuildDate) {
			list($lastBuildDate, $time) = explode(' ', $lastBuildDate);
		}

		// Build the path to the nightly packages.
		$path = dirname(JPATH_ROOT).'/builds/nightly/'.$lastBuildDate;

		// If the lastest build packages exist deploy the packages.
		if (is_dir($path)) {
			if (!$this->_deployPackages($path, $lastBuildDate)) {
				return false;
			}
		}

		// Clean up processed builds.
		foreach ($builds as $path) {
			JFolder::delete($path);
		}

		return true;
	}

	private function _getAvailableBuilds()
	{
		// This will eventually be in a configuration object.
		$path = dirname(JPATH_ROOT).'/builds/nightly';

		// Get the list of build folders.
		$folders = JFolder::folders($path);

		// Build the list of available builds.
		$builds = array();
		foreach ($folders as $folder) {

			// Get a list of the files in the build folder.
			$files = JFolder::files($path.'/'.$folder);

			// Check that the proper files exist.
			if (in_array('changelog_report.xml', $files)) {
				$builds[(string) $folder] = $path.'/'.$folder;
			} else {
				JFolder::delete($path.'/'.$folder);
			}
		}

		return $builds;
	}

	private function _storeReportDeltas($date, $branchId)
	{
		// Get a build table object and set the build id.
		$build = $this->getTable('Nightly', 'CodeTable');

		// Check to see if there is already a build row.
		$this->_db->setQuery(
			'SELECT build_id' .
			' FROM #__code_nightly_builds' .
			' WHERE build_date = '.$this->_db->quote($date)
		);
		$buildId = (int) $this->_db->loadResult();

		// If a row already exists for the build, load it.
		if ($buildId) {
			$build->load($buildId);
		}
		// If there is no build by revision, just return true.
		else {
			var_dump($this->_db);die;
			return true;
		}

		// Get the previous build data.
		$this->_db->setQuery(
			'SELECT build_id, st_report, ut_report, ut_tests, st_tests' .
			' FROM #__code_nightly_builds' .
			' WHERE build_date < '.$this->_db->quote($date) .
			' AND branch_id = '.(int) $branchId .
			' ORDER BY build_date DESC',
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
				$build->st_delta = json_encode(array('-' => array(), '+' => array()));
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

	private function _deployPackages($path, $date)
	{
		/*
		 * UNCOMPRESS AND DEPLOY _documentation.zip
		 */
		$destination = JPATH_SITE.'/api/nightly';
		$tmpDestination = JFactory::getApplication()->getCfg('tmp_path').'/_doc';

		// Just in case.
		JFolder::create($destination);

		if (file_exists($path.'/_documentation.zip') && JArchive::extract($path.'/_documentation.zip', $tmpDestination)) {
			JFolder::delete($destination);
			JFolder::move($tmpDestination, $destination);
		}
		else {
			// return false;
		}

		// Deploy and generate the manifest XML for the packages.

		// Come from configuration eventually.
		$destination = dirname(JPATH_ROOT).'/packages/nightly';

		// Just in case.
		JFolder::create($destination);

		$xml[] = '<?xml version="1.0"?>';
		$xml[] = '<packages build="nightly" date="'.$date.'">';

		if (file_exists($path.'/trunk.bz2') && JFile::copy($path.'/trunk.bz2', $destination.'/nightly.bz2')) {

			// Calculate file hashes.
			$md5 = md5_file($destination.'/nightly.bz2');
			$sha1 = sha1_file($destination.'/nightly.bz2');
			$size = filesize($destination.'/nightly.bz2');

			$xml[] = '	<package size="'.$size.'" md5="'.$md5.'" sha1="'.$sha1.'" path="'.$destination.'/nightly.bz2" />';
		}
		if (file_exists($path.'/trunk.gz') && JFile::copy($path.'/trunk.gz', $destination.'/nightly.gz')) {

			// Calculate file hashes.
			$md5 = md5_file($destination.'/nightly.gz');
			$sha1 = sha1_file($destination.'/nightly.gz');
			$size = filesize($destination.'/nightly.gz');

			$xml[] = '	<package size="'.$size.'" md5="'.$md5.'" sha1="'.$sha1.'" path="'.$destination.'/nightly.gz" />';
		}
		if (file_exists($path.'/trunk.zip') && JFile::copy($path.'/trunk.zip', $destination.'/nightly.zip')) {

			// Calculate file hashes.
			$md5 = md5_file($destination.'/nightly.zip');
			$sha1 = sha1_file($destination.'/nightly.zip');
			$size = filesize($destination.'/nightly.zip');

			$xml[] = '	<package size="'.$size.'" md5="'.$md5.'" sha1="'.$sha1.'" path="'.$destination.'/nightly.zip" />';
		}

		$xml[] = '</packages>';

		JFile::write($destination.'/manifest.xml', implode("\n", $xml));

		return true;
	}
}