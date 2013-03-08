<?php
/**
 * @version		$Id: reports.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper to create report objects from XML build reports.
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeHelperReports
{
	public static function getChangelogReport($path)
	{
		// Initialize variables.
		$report = array();

		// Make sure the file exists.
		if (!file_exists($path.'/changelog_report.xml')) {
			return $report;
		}

		// Load the XML file.
		$xml = simplexml_load_file($path.'/changelog_report.xml');

		// Verify there is a log entry in the changelog.
		if (empty($xml->logentry)) {
			return $report;
		}

		// Add each log entry to the report.
		foreach ($xml->logentry as $node) {

			// Initialize revision object.
			$item = new stdClass();
			$item->paths = array();

			// Get the revision id from the log.
			$item->revision_id = (int) $node['revision'];

			// Get changeset metadata from the log entry.
			$item->user_name = trim((string) $node->author);
			$item->commit_date = trim((string) $node->date);
			$item->log = trim((string) $node->msg);

			// Extract path data from the log entry.
			$paths = $node->xpath('descendant::path');
			if (!empty($paths)) {
				foreach ($paths as $path) {
					$item->paths[] = array('action'=>(string) $path['action'], 'path'=>trim((string) $path));
				}
			}

			$report[] = $item;
		}

		return $report;
	}

	public static function getUnitTestReport($path)
	{
		// Initialize variables.
		$item = new stdClass();
		$item->total_tests		= 0;
		$item->total_assertions	= 0;
		$item->total_failures	= 0;
		$item->total_errors		= 0;
		$item->time				= 0;
		$item->failures			= array();

		// Make sure the file exists.
		if (!file_exists($path.'/unit_test_report.xml')) {
			return $item;
		}

		// Instantiate the XML parser and parse the XML output.
		$xml = new XMLReader();
		$xml->open('file://'.$path.'/unit_test_report.xml');

		// Create a DOM document object for translating into SimpleXML objects.
		$doc = new DOMDocument;

		// Move to the first <testsuite /> node.
		while ($xml->read() && $xml->name !== 'testsuite');

		// Set the master values for the file.
		$item->total_tests		= (int) $xml->getAttribute('tests');
		$item->total_assertions	= (int) $xml->getAttribute('assertions');
		$item->total_failures	= (int) $xml->getAttribute('failures');
		$item->total_errors		= (int) $xml->getAttribute('errors');
		$item->time				= (float) $xml->getAttribute('time');

		// Move to the next <testsuite /> node, the child node of our master.
		while ($xml->read() && $xml->name !== 'testsuite');

		// Now that we are in the right place, process each individual <testsuite /> node iteratively.
		while ($xml->name === 'testsuite')
		{
			// Skip <testsuite /> nodes with no actual tests.
			if (!$xml->getAttribute('tests') || (!$xml->getAttribute('failures') && !$xml->getAttribute('errors'))) {
			    $xml->next('testsuite');
			    continue;
			}

		    // Get a SimpleXML element from the XML node.
		    $node = simplexml_import_dom($doc->importNode($xml->expand(), true));

			// Get all the failure test cases within the element.
			if ($xml->getAttribute('failures')) {
				$failures = $node->xpath('descendant::testcase[failure]/parent::testsuite');
				if (!empty($failures)) {
					foreach ($failures as $failure)
					{
						$entry = new stdClass();
						$entry->class = (string) $failure['name'];

						$cases = $failure->xpath('child::testcase[failure]');
						foreach ($cases as $case)
						{
							$entry->case[] = (string) $case['name'];
						}

						$item->failures[] = $entry;

					}
				}
			}

		    // Go to the next <testsuite /> node.
		    $xml->next('testsuite');
		}

		return $item;
	}

	public static function getSystemTestReport($path)
	{
		// Initialize variables.
		$item = new stdClass();
		$item->total_tests		= 0;
		$item->total_assertions	= 0;
		$item->total_failures	= 0;
		$item->total_errors		= 0;
		$item->time				= 0;
		$item->failures			= array();

		// Make sure the file exists.
		if (!file_exists($path.'/system_test_report.xml')) {
			return $item;
		}

		// Instantiate the XML parser and parse the XML output.
		$xml = new XMLReader();
		$xml->open('file://'.$path.'/system_test_report.xml');

		// Create a DOM document object for translating into SimpleXML objects.
		$doc = new DOMDocument;

		// Move to the first <testsuite /> node.
		while ($xml->read() && $xml->name !== 'testsuite');

		// Set the master values for the file.
		$item->total_tests		= (int) $xml->getAttribute('tests');
		$item->total_assertions	= (int) $xml->getAttribute('assertions');
		$item->total_failures	= (int) $xml->getAttribute('failures');
		$item->total_errors		= (int) $xml->getAttribute('errors');
		$item->time				= (float) $xml->getAttribute('time');

		// Move to the next <testsuite /> node, the child node of our master.
		while ($xml->read() && $xml->name !== 'testsuite');

		// Now that we are in the right place, process each individual <testsuite /> node iteratively.
		while ($xml->name === 'testsuite')
		{
			// Skip <testsuite /> nodes with no actual tests.
			if (!$xml->getAttribute('tests') || (!$xml->getAttribute('failures') && !$xml->getAttribute('errors'))) {
			    $xml->next('testsuite');
			    continue;
			}

		    // Get a SimpleXML element from the XML node.
		    $node = simplexml_import_dom($doc->importNode($xml->expand(), true));

			// Get all the failure test cases within the element.
			if ($xml->getAttribute('failures')) {
				$failures = $node->xpath('descendant::testcase[child::failure]/parent::testsuite');
				if (!empty($failures)) {
					foreach ($failures as $failure)
					{
						$entry = new stdClass();
						$entry->class = (string) $failure['name'];

						$cases = $failure->xpath('child::testcase[failure]');
						foreach ($cases as $case)
						{
							$entry->case[] = (string) $case['name'];
						}

						$item->failures[] = $entry;
					}
				}
			}

		    // Go to the next <testsuite /> node.
		    $xml->next('testsuite');
		}

		return $item;
	}

	public static function getCodeCoverageReport($path)
	{
		// Initialize variables.
		$report = new stdClass();
		$report->loc					= 0;
		$report->loc_covered			= 0;
		$report->loc_covered_pct		= 0.0;
		$report->methods				= 0;
		$report->methods_covered		= 0;
		$report->methods_covered_pct	= 0.0;

		// Make sure the file exists.
		if (!file_exists($path.'/code_coverage_report.xml')) {
			return $report;
		}

		// Load the XML file.
		$xml = simplexml_load_file($path.'/code_coverage_report.xml');

		// Verify there is data in the coverage report.
		if (empty($xml->project) || !isset($xml->project[0]->metrics[0])) {
			return $report;
		}

		// Get the project metrics element.
		$metrics = $xml->project[0]->metrics[0];

		$report->loc = (int) $metrics['loc'];
		$report->loc_covered = ((int) $metrics['loc'] - (int) $metrics['ncloc']);
		$report->loc_covered_pct = $report->loc_covered / $report->loc * 100;
		$report->methods = (int) $metrics['methods'];
		$report->methods_covered = (int) $metrics['coveredmethods'];
		$report->methods_covered_pct = $report->methods_covered / $report->methods * 100;

		return $report;
	}

	public static function getReportDelta($new, $old)
	{
		// Initialize variables.
		$delta = array('-' => array(), '+' => array());
		$new = (array) $new;
		$old = (array) $old;

		// Process fixed tests.
		foreach($old as $oldSuite)
		{
			// Create the temporary suite array.
			$tmp = array('class' => $oldSuite->class, 'case' => array());
			$found = false;

			foreach ($new as $s)
			{
				if ($oldSuite->class == $s->class) {
					$found = true;

					foreach ($oldSuite->case as $c)
					{
						if (!in_array($c, $s->case)) {
							$tmp['case'][] = $c;
						}
					}
				}
			}

			if (!$found) {
				$delta['-'][] = $oldSuite;
			}
			elseif (!empty($tmp['case'])) {
				$delta['-'][] = $tmp;
			}
		}

		// Process broken tests.
		foreach($new as $newSuite)
		{
			// Create the temporary suite array.
			$tmp = array('class' => $newSuite->class, 'case' => array());
			$found = false;

			foreach ($old as $s)
			{
				if ($newSuite->class == $s->class) {
					$found = true;

					foreach ($newSuite->case as $c)
					{
						if (!in_array($c, $s->case)) {
							$tmp['case'][] = $c;
						}
					}
				}
			}

			if (!$found) {
				$delta['+'][] = $newSuite;
			}
			elseif (!empty($tmp['case'])) {
				$delta['+'][] = $tmp;
			}
		}

		return $delta;
	}
}
