<?php
/**
 * @version		$Id: trackersync.php 458 2010-10-07 18:06:31Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies.

jimport('joomla.utilities.arrayhelper');

// Include the GForge connector classes.
require JPATH_COMPONENT.'/helpers/gforge.php';
require JPATH_COMPONENT.'/helpers/gforgelegacy.php';

/**
 * Tracker Synchronization Model for Joomla Code
 *
 * @package		Joomla.Code
 * @subpackage	com_code
 * @since		1.0
 */
class CodeModelTrackerSync extends JModelLegacy
{
	/**
	 * @var    GForge  The GForge SOAP connector object.
	 * @since  1.0
	 */
	protected $gforge;

	/**
	 * @var    GForgeLegacy  The GForge legacy SOAP connector object.
	 * @since  1.0
	 */
	protected $gforgeLegacy;

	/**
	 * @var    array  Associative array of tracker issue status values.
	 * @since  1.0
	 */
	protected $status = array();

	/**
	 * @var    array  Associative array of tracker fields.
	 * @since  1.0
	 */
	protected $fields = array();

	/**
	 * @var    array  Associative array of tracker field data values.
	 * @since  1.0
	 */
	protected $fieldValues = array();

	/**
	 * @var    array  Associative array of processing statistics
	 * @since  1.0
	 */
	protected $processingTotals = array();


	public function filefix()
	{
		// Initialize variables.
		$db = & JFactory::getDBO();

		$db->setQuery(
			'SELECT DISTINCT issue_id' .
			' FROM #__code_tracker_issue_files'
		);
		$issues = (array) $db->loadResultArray();

		foreach ($issues as $issue) {
			$this->_fixFilesForIssue($issue);
		}
	}

	private function _fixFilesForIssue($issueId)
	{
		// Initialize variables.
		$db = & JFactory::getDBO();

		// Get some important issue data.
		$db->setQuery(
			'SELECT DISTINCT issue_id, created_by, created_date, modified_date' .
			' FROM #__code_tracker_issues' .
			' WHERE issue_id = '.(int) $issueId
		);
		$issue = $db->loadObject();

		// Get the list of comments for this issue.
		$db->setQuery(
			'SELECT created_date, created_by, body' .
			' FROM #__code_tracker_issue_responses' .
			' WHERE issue_id = '.(int) $issue->issue_id .
			' ORDER BY created_date DESC'
		);
		$comments = (array) $db->loadObjectList();

		// Get the list of status changes for this issue.
		$db->setQuery(
			'SELECT change_date, change_by' .
			' FROM #__code_tracker_issue_changes' .
			' WHERE issue_id = '.(int) $issue->issue_id .
			' ORDER BY change_date DESC'
		);
		$changes = (array) $db->loadObjectList();

		// Get the list of files for this issue.
		$db->setQuery(
			'SELECT file_id, created_by, name' .
			' FROM #__code_tracker_issue_files' .
			' WHERE issue_id = '.(int) $issue->issue_id .
			' ORDER BY jc_file_id DESC'
		);
		$files = (array) $db->loadObjectList();

		foreach ($files as & $file) {

			$found = false;

			// First we look for a comment.
			foreach ($comments as & $comment) {
				if (empty($comment->used) && ($comment->created_by == $file->created_by)) {
					$found = true;
					$comment->used = true;
					$file->created_date = $comment->created_date;
					break;
				}
			}

			// If not found, next we look for a change.
			if (!$found) {
				foreach ($changes as & $change) {
					if (empty($change->used) && ($change->change_by == $file->created_by)) {
						$found = true;
						$change->used = true;
						$file->created_date = $change->change_date;
						break;
					}
				}
			}

			// Last we look to see if the issue was created by the person who posted the file
			if (!$found) {
				if ($issue->created_by == $file->created_by) {
					$found = true;
					$file->created_date = $issue->created_date;
				}
			}

			if ($found) {
				// Fix the row in the database.
				$this->_db->setQuery(
					'UPDATE #__code_tracker_issue_files' .
					' SET created_date = '.$this->_db->quote($file->created_date) .
					' WHERE file_id = '.(int) $file->file_id
				);

				// Check for an error.
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
			else {
				// Fix the row in the database.
				$this->_db->setQuery(
					'UPDATE #__code_tracker_issue_files' .
					' SET created_date = '.$this->_db->quote($issue->modified_date) .
					' WHERE file_id = '.(int) $file->file_id
				);

				// Check for an error.
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}
	}

	public function test()
	{
		// Get a tracker issue change table object.
		$table = $this->getTable('TrackerIssueChange', 'CodeTable');


		$table->load(42);
		var_dump(unserialize($table->data));
		var_dump($table);

	}

	public function sync()
	{
		$options['format'] = '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}';
		$options['text_file'] = 'gforge_sync.php';
		$log = JLog::addLogger($options, JLog::INFO);
		JLog::add('Starting the GForge Sync', JLog::INFO);
		// Initialize variables.
		$username = JFactory::getConfig()->get('gforgeLogin');
		$password = JFactory::getConfig()->get('gforgePassword');
		$project  = 5; // Joomla project id.

		// Connect to the main SOAP interface.
		$this->gforge = new GForge('http://joomlacode.org/gf');
		$this->gforge->login($username, $password);

		// Connect to the legacy SOAP interface.
		$this->gforgeLegacy = new GForgeLegacy('http://joomlacode.org/gf');
		$this->gforgeLegacy->login($username, $password);

		// Get the tracker data from the SOAP interface.
		$trackers = $this->gforge->getProjectTrackers($project);
		if (empty($trackers)) {
			$this->setError('Unable to get trackers from the server.');
			return false;
		}

		// Sync each tracker.
		$trackers = array_reverse($trackers);
		foreach ($trackers as $tracker)
		{
 			if ($tracker->tracker_id == 8103) {
				$this->_populateTrackerFields($tracker->tracker_id);
				$this->_syncTracker($tracker);
			}
		}

//		$this->_populateTrackerFields($trackers[0]->tracker_id);
//		$this->_syncTracker($trackers[0]);

		return true;
	}

	private function _syncTracker($tracker)
	{
		// Get a tracker table object.
		$table = $this->getTable('Tracker', 'CodeTable');

		// Load any existing data by legacy id.
		$table->loadByLegacyId($tracker->tracker_id);

		// Populate the appropriate fields from the server data object.
		$data = array(
			'item_count' => $tracker->item_total,
			'open_item_count' => $tracker->open_count,
		);

		// Bind the data to the tracker object.
		$table->bind($data);

		// Attempt to store the tracker data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		// Get the tracker item data from the SOAP interface.
		$items = $this->gforge->getTrackerItems($tracker->tracker_id);
		if (empty($items)) {
			$this->setError('Unable to get tracker items from the server for tracker: '.$tracker->summary.'.');
			return false;
		}

		// Date for testing whether to sync or not
		$cutoffDate = new DateTime("now");
		$cutoffDate->sub(new DateInterval('P1Y'));

		$totalCount = count($items);

		// Sync each tracker item.
		for ($i = 0; $i < $totalCount; $i++)
		{
			$total = $i + 1;
			// echo 'Processing row ' . $total . ' of ' . $totalCount . ', tracker_item_id=' . $item->tracker_item_id . " ...";
			$item = $items[$i];
			// Exclude items closed > 1 year
			$closeDate = new DateTime($item->close_date);
			if (isset($item->close_date) && $closeDate < $cutoffDate)
			{
				// echo "Skipping item closed > 1 year\n";
				$skippedCount++;
			}
			else
			{
				// echo "Processing item...";
				$this->_syncTrackerItem($item, $tracker->tracker_id, $tracker->project_id, $table->tracker_id, $table->project_id);
				$processedCount++;
			}

// 			echo "Skipped issues: $skippedCount;  Processed issues: $processedCount;  Total read: $total of $totalCount\n";
		}

		JLog::add('Skipped: ' . $skippedCount . ';  Processed issues: ' . $processedCount . ';  Total: ' . $total);
		$logMessage = 'Issues: ' . $this->processingTotals['issues'] . ';  Changes: ' . $this->processingTotals['changes'] . ';';
		$logMessage .= '  Files: ' . $this->processingTotals['files'] . ';  Messages: ' . $this->processingTotals['messages'] . ' ;';
		$logMessage .= '  Users: ' . $this->processingTotals['users'] . ' ;';
		JLog::add($logMessage);
		return true;
	}

	public function syncIssue($issueId, $trackerId)
	{
		// Initialize variables.
		$username = JFactory::getConfig()->get('gforgeLogin');
		$password = JFactory::getConfig()->get('gforgePassword');
		$project  = 5; // Joomla project id.

		// Connect to the main SOAP interface.
		$this->gforge = new GForge('http://joomlacode.org/gf');
		$this->gforge->login($username, $password);

		// Connect to the legacy SOAP interface.
		$this->gforgeLegacy = new GForgeLegacy('http://joomlacode.org/gf');
		$this->gforgeLegacy->login($username, $password);

		/*
		 * Get the tracker from the GForge server.
		 */
		$tracker = $this->gforge->getTracker($trackerId);

		// If a tracker wasn't found return false.
		if (!is_object($tracker)) {
			return false;
		}

		// Synchronize the tracker fields.
		$this->_populateTrackerFields($tracker->tracker_id);

		// Get a tracker table object.
		$table = $this->getTable('Tracker', 'CodeTable');

		// Load any existing data by legacy id.
		$table->loadByLegacyId($tracker->tracker_id);

		// Populate the appropriate fields from the server data object.
		$data = array(
			'item_count' => $tracker->item_total,
			'open_item_count' => $tracker->open_count,
		);

		// Bind the data to the tracker object.
		$table->bind($data);

		// Attempt to store the tracker data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		// Create the mock item object for use in the
		$item = (object) array('tracker_item_id' => $issueId);

		return $this->_syncTrackerItem($item, $trackerId, $tracker->project_id, $table->tracker_id, $table->project_id);
	}

	private function _syncTrackerItem($item, $legacyTrackerId, $legacyProjectId, $trackerId, $projectId)
	{
		// Build the query to see if the item already exists.
		$this->_db->setQuery(
			'SELECT issue_id, modified_date, status' .
			' FROM #__code_tracker_issues' .
			' WHERE jc_issue_id = '.(int)$item->tracker_item_id
		);

		// Execute the query to find out if the item exists.
		$exists = $this->_db->loadObject();

		/*
		 * Get full data on the tracker item from the GForge server.
		 */
		$item = $this->gforge->getTrackerItem($item->tracker_item_id);

		// If a tracker item wasn't found return false.
		if (!is_object($item)) {
			return false;
		}

		// No need to process an issue that hasn't changed.
		if (!empty($exists->status) && !empty($exists->issue_id) && ($exists->modified_date == $item->last_modified_date)) {
			// echo "Nothing changed: $exists->jc_issue_id\n";
			return true;
		}

		// Get accessory data on the tracker item from the GForge server.
		$changes = $this->gforge->getTrackerItemChanges($item->tracker_item_id);
		$files = $this->gforgeLegacy->getTrackerItemFiles($item->tracker_item_id, $legacyTrackerId, $legacyProjectId);

		/*
		 * Synchronize all users relevant to the tracker item.
		 */

		// Get a list of all of the user ids to look up.
		$usersToLookUp = array(
			$item->submitted_by,
			$item->last_modified_by
		);

		// Add each user id that submitted a response to the list.
		foreach ($item->messages as $message) {
			$usersToLookUp[] = $message->submitted_by;
		}

		// Add each user id that committed a code change to the list.
		foreach ($item->scm_commits as $commit) {
			$usersToLookUp[] = $commit->user_id;
		}

		// Add each user id that is assigned to the list.
		foreach ($item->assignees as $assignee) {
			$usersToLookUp[] = $assignee->assignee;
		}

		// Add each user id that submitted a file to the list.
		foreach ($files as $file) {
			$usersToLookUp[] = $file->submitted_by;
		}

		// Add each user id that made a change to the list.
		foreach ($changes as $change) {
			$usersToLookUp[] = $change->user_id;
		}

		// Remove any duplicates.
		$usersToLookUp = array_values(array_unique($usersToLookUp));

		// Get rid of user id 0
		sort($usersToLookUp);
		if ($usersToLookUp[0] == 0)
		{
			array_shift($usersToLookUp);
		}

		// Get the syncronized user ids.
		$users = $this->_syncUsers($usersToLookUp);
		if ($users === false) {
			return false;
		}

		/*
		 * Synchronize the tracker issue table.
		 */

		// Get a tracker issue table object.
		$table = $this->getTable('TrackerIssue', 'CodeTable');

		// Load any existing data by legacy id.
		$table->loadByLegacyId($item->tracker_item_id);

		// Populate the appropriate fields from the server data object.
		$data = array(
			'tracker_id' => $trackerId,
			'project_id' => $projectId,
			'build_id' => 0,
			'state' => $item->status_id,
			'priority' => $item->priority,
			'created_date' => $item->open_date,
			'created_by' => $users[$item->submitted_by],
			'modified_date' => $item->last_modified_date,
			'modified_by' => @$users[$item->last_modified_by],
			'close_date' => $item->close_date,
			'title' => $item->summary,
			'alias' => '',
			'description' => $item->details,
			'jc_issue_id' => $item->tracker_item_id,
			'jc_tracker_id' => $legacyTrackerId,
			'jc_project_id' => $legacyProjectId,
			'jc_created_by' => $item->submitted_by,
			'jc_modified_by' => $item->last_modified_by
		);

		// Only populate the close by data if necessary.
		if ($item->close_date && @$users[$item->last_modified_by]) {
			$data['close_by'] = $users[$item->last_modified_by];
			$data['jc_close_by'] = $item->last_modified_by;
		}

		if (!isset($item->close_date)) {
			$data['close_date'] = '0000-00-00 00:00:00';
		}

		// Bind the data to the issue object.
		$table->bind($data);

		// Attempt to store the issue data.
		if (!$table->store(true)) {
			$this->setError($table->getError());
			return false;
		}
		$this->processingTotals['issues']++;
		if (!isset($exists->status))
		{
			if (!$this->_addCreateActivities($data))
			{
				return false;
			}
		}

		// Synchronize the assignees associated with the tracker item.
		if (is_array($item->assignees)) {
			if (!$this->_syncTrackerItemAssignments($item->assignees, $users, $table->issue_id, $table->tracker_id, $table->jc_issue_id, $table->jc_tracker_id)) {
				return false;
			}
		}

		// Synchronize the files associated with the tracker item.
		if (is_array($files)) {
			if (!$this->_syncTrackerItemFiles($files, $users, $table->issue_id, $table->tracker_id, $table->jc_issue_id, $table->jc_tracker_id)) {
				return false;
			}
		}

		// Synchronize the messages associated with the tracker item.
		if (is_array($item->messages)) {
			if (!$this->_syncTrackerItemMessages($item->messages, $users, $table->issue_id, $table->tracker_id, $table->jc_issue_id, $table->jc_tracker_id)) {
				return false;
			}
		}

		// Synchronize the changes associated with the tracker item.
		if (is_array($changes)) {
			if (!$this->_syncTrackerItemChanges($changes, $users, $table->issue_id, $table->tracker_id, $table->jc_issue_id, $table->jc_tracker_id)) {
				return false;
			}
		}

		// Synchronize the commits associated with the tracker item.
		if (is_array($item->scm_commits)) {
			if (!$this->_syncTrackerItemCommits($item->scm_commits, $users, $table->issue_id, $table->tracker_id, $table->jc_issue_id, $table->jc_tracker_id)) {
				return false;
			}
		}

		// Synchronize the extra fields for the tracker item.
		if (is_array($item->extra_field_data)) {
			if (!$this->_syncTrackerItemExtraFields($item->extra_field_data, $users, $table->issue_id, $table->tracker_id, $table->jc_issue_id, $table->jc_tracker_id)) {
				return false;
			}
		}

		return true;
	}

	private function _syncTrackerItemExtraFields($fieldValues, $users, $issueId, $trackerId, $legacyIssueId, $legacyTrackerId)
	{
		// Some GForge tracker fields we don't care about as far as tags are concerned.
		$ignore = array(
			'duration',
			'percentcomplete',
			'estimatedeffort',
			'build'
		);

		// Get the list of relevant tags.
		$tags = array();
		foreach ($fieldValues as $value)
		{
			// Ignore some fields we don't care about.
			if (in_array($this->fields[$value->tracker_extra_field_id]['alias'], $ignore)) {
				continue;
			}
			// Special case for status.
			elseif ($this->fields[$value->tracker_extra_field_id]['alias'] == 'status') {

				// Make sure we have a status for it.
				if (isset($this->fieldValues[$value->field_data]) && isset($this->status[$this->fieldValues[$value->field_data]['value_id']])) {
					// Set the status value/name for the issue.
					$this->_db->setQuery(
						'UPDATE #__code_tracker_issues' .
						' SET status = '.(int) $this->status[$this->fieldValues[$value->field_data]['value_id']] .
						' , status_name = '.$this->_db->quote($this->fieldValues[$value->field_data]['name']) .
						' WHERE issue_id = '.(int) $issueId
					);

					// Check for an error.
					if (!$this->_db->query()) {
						$this->setError($this->_db->getErrorMsg());
						return false;
					}
				}
				else {
					//print_r($value);
					//JError::raiseWarning(500, $this->fieldValues[$value->field_data]['value_id'].': '.$this->fieldValues[$value->field_data]['name']);
				}

				continue;
			}

			if (!empty($this->fieldValues[$value->field_data])) {
				$tags[] = $this->fieldValues[$value->field_data]['name'];
			}
		}

		// If there are no tags, move on.
		if (empty($tags)) {
			return true;
		}

		// Make sure the tags we need are synced.
		if (!$tags = $this->_syncTags($tags)) {
			return false;
		}

		// Get the current tag maps for the issue.
		$this->_db->setQuery(
			'SELECT tag_id' .
			' FROM #__code_tracker_issue_tag_map' .
			' WHERE issue_id = '.(int) $issueId
		);
		$existing = (array) $this->_db->loadResultArray();
		JArrayHelper::toInteger($existing);

		// Get the list of tag maps to add and delete.
		$add = array_diff(array_keys($tags), $existing);
		$del = array_diff($existing, array_keys($tags));

		// Delete the necessary tag maps.
		if (!empty($del)) {
			$this->_db->setQuery(
				'DELETE FROM #__code_tracker_issue_tag_map' .
				' WHERE issue_id = '.(int) $issueId .
				' AND tag_id IN ('.implode(',', $del).')'
			);

			// Check for an error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		// Add the necessary tag maps.
		foreach ($add as $tag)
		{
			// Insert the new tag map.
			$this->_db->setQuery(
				'INSERT INTO #__code_tracker_issue_tag_map' .
				' (issue_id, tag_id, tag)' .
				' VALUES' .
				' ('.(int) $issueId.', '.(int) $tag.', '.$this->_db->quote($tags[$tag]).')'
			);

			// Check for an error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	private function _syncTrackerItemAssignments($assignments, $users, $issueId, $trackerId, $legacyIssueId, $legacyTrackerId)
	{
		// Get the list of user assignments.
		$ids = array();
		foreach ($assignments as $assignment)
		{
			// Ignore the nobody user.
			if ($assignment->assignee == 100) {
				continue;
			}

			$ids[] = (int) $assignment->assignee;
		}

		// Remove assignments that don't belong.
		if (empty($ids)) {
			$this->_db->setQuery(
				'DELETE  FROM #__code_tracker_issue_assignments' .
				' WHERE issue_id = '.(int) $issueId
			);
		}
		else {
			$this->_db->setQuery(
				'DELETE  FROM #__code_tracker_issue_assignments' .
				' WHERE issue_id = '.(int) $issueId .
				' AND jc_user_id NOT IN ('.implode(',', $ids).')'
			);
		}

		// Check for an error.
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Look up the existing local assignments.
		$this->_db->setQuery(
			'SELECT jc_user_id' .
			' FROM #__code_tracker_issue_assignments' .
			' WHERE issue_id = '.(int) $issueId
		);
		$existing = (array) $this->_db->loadResultArray();

		// Get the list of assignments to insert as a diff from what we need vs what we have.
		$inserts = array_diff($ids, $existing);

		foreach ($inserts as $insert)
		{
			// Insert the new assignment.
			$this->_db->setQuery(
				'INSERT INTO #__code_tracker_issue_assignments' .
				' (issue_id, user_id, jc_issue_id, jc_user_id)' .
				' VALUES' .
				' ('.(int) $issueId.', '.(int) @$users[$insert].', '.(int) $legacyIssueId.', '.(int) $insert.')'
			);

			// Check for an error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	private function _syncTrackerItemCommits($commits, $users, $issueId, $trackerId, $legacyIssueId, $legacyTrackerId)
	{
		// Synchronize each commit.
		foreach ($commits as $commit)
		{
			// Get a tracker issue commit table object.
			$table = $this->getTable('TrackerIssueCommit', 'CodeTable');

			// Load any existing data by legacy id.
			$table->loadByLegacyId($commit->scm_commit_id);

			// Skip over rows that exist and haven't changed.
			if ($table->commit_id && $table->created_date == $commit->commit_date) {
				continue;
			}

			// Populate the appropriate fields from the server data object.
			$data = array(
				'issue_id' => $issueId,
				'tracker_id' => $trackerId,
				'created_date' => $commit->commit_date,
				'created_by' => $users[$commit->user_id],
				'message' => $commit->message_log,
				'jc_commit_id' => $commit->scm_commit_id,
				'jc_issue_id' => $legacyIssueId,
				'jc_tracker_id' => $legacyTrackerId,
				'jc_created_by' => $commit->user_id
			);

			// Bind the data to the object.
			$table->bind($data);

			// Attempt to store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	private function _syncTrackerItemChanges($changes, $users, $issueId, $trackerId, $legacyIssueId, $legacyTrackerId)
	{
		// Synchronize each change.
		foreach ($changes as $change)
		{
			// Ignore non-status changes for now.
			if ($change->field_name != 'status') {
				continue;
			}

			// Get a tracker issue change table object.
			$table = $this->getTable('TrackerIssueChange', 'CodeTable');

			// Load any existing data by legacy id.
			$table->loadByLegacyId($change->audit_trail_id);

			// Skip over rows that exist and haven't changed.
			if ($table->change_id && $table->change_date == $change->change_date) {
				continue;
			}

			// Populate the appropriate fields from the server data object.
			$data = array(
				'issue_id' => $issueId,
				'tracker_id' => $trackerId,
				'change_date' => $change->change_date,
				'change_by' => $users[$change->user_id],
				'data' => serialize($change),
				'jc_change_id' => $change->audit_trail_id,
				'jc_issue_id' => $legacyIssueId,
				'jc_tracker_id' => $legacyTrackerId,
				'jc_change_by' => $change->user_id
			);

			// Bind the data to the object.
			$table->bind($data);

			// Attempt to store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
			if (!$this->_addActivity(3, $data['jc_issue_id'], $data['jc_change_by'], $data['jc_issue_id'], $data['change_date']))
			{
				return false;
			}
			$this->processingTotals['changes']++;
		}
		return true;
	}

	private function _syncTrackerItemMessages($messages, $users, $issueId, $trackerId, $legacyIssueId, $legacyTrackerId)
	{
		// Synchronize each message.
		foreach ($messages as $message)
		{
			// Get a tracker issue response table object.
			$table = $this->getTable('TrackerIssueResponse', 'CodeTable');

			// Load any existing data by legacy id.
			$table->loadByLegacyId($message->tracker_item_message_id);

			// Skip over rows that exist and haven't changed.
			if ($table->response_id && $table->created_date == $message->adddate) {
				continue;
			}

			// Populate the appropriate fields from the server data object.
			$data = array(
				'issue_id' => $issueId,
				'tracker_id' => $trackerId,
				'created_date' => $message->adddate,
				'created_by' => $users[$message->submitted_by],
				'body' => $message->body,
				'jc_response_id' => $message->tracker_item_message_id,
				'jc_issue_id' => $legacyIssueId,
				'jc_tracker_id' => $legacyTrackerId,
				'jc_created_by' => $message->submitted_by
			);

			// Bind the data to the object.
			$table->bind($data);

			// Attempt to store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
			if (!$this->_addCommentActivity($data))
			{
				return false;
			}
			$this->processingTotals['messages']++;
		}
		return true;
	}

	private function _syncTrackerItemFiles($files, $users, $issueId, $trackerId, $legacyIssueId, $legacyTrackerId)
	{
		// Synchronize each file.
		foreach ($files as $file)
		{
			// Get a tracker issue file table object.
			$table = $this->getTable('TrackerIssueFile', 'CodeTable');

			// Load any existing data by legacy id.
			$table->loadByLegacyId($file->id);

			// Skip over rows that exist and haven't changed.
			if ($table->file_id) {
				continue;
			}

			// Populate the appropriate fields from the server data object.
			$data = array(
				'issue_id' => $issueId,
				'tracker_id' => $trackerId,
				'created_date' => $file->adddate ? $file->adddate : date('Y-m-d'),
				'created_by' => $users[$file->submitted_by],
				'name' => $file->name,
				'description' => $file->description,
				'size' => $file->filesize,
				'type' => $file->filetype,
				'jc_file_id' => $file->id,
				'jc_issue_id' => $legacyIssueId,
				'jc_tracker_id' => $legacyTrackerId,
				'jc_created_by' => $file->submitted_by
			);

			// Bind the data to the object.
			$table->bind($data);

			// Attempt to store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			if (!$this->_addFileActivity($data))
			{
				return false;
			}
			$this->processingTotals['files']++;
			// echo "processing files for jc_issue_id: $data->jc_issue_id\n";
		}
		return true;
	}

	/**
	 * Method to make sure a set of tag values are syncronized with the local system.  This
	 * method will return an associative array of tag_id => tag values.
	 *
	 * @param   array  $values  An array of tag values to make sure exist in the local system.
	 *
	 * @return  array  An array of tag_id => tag values.
	 *
	 * @since   1.0
	 */
	private function _syncTags($values)
	{
		// Initialize variables.
		$tags = array();
		$ors  = array();

		foreach ($values as $k => $value)
		{
			$ors[$k] = $this->_db->quote($value);
		}

		// Build the query to see if the items already exist.
		$this->_db->setQuery(
			'SELECT tag_id, tag' .
			' FROM #__code_tags' .
			' WHERE tag = '.implode(' OR tag = ', $ors)
		);

		// Execute the query to find out if the items exist.
		$exists = (array) $this->_db->loadObjectList();

		// Build out the array of tags based on those that already exist.
		foreach ($exists as $exist) {
			$tags[(int) $exist->tag_id] = $exist->tag;
		}

		// Get the list of tags to store.
		$store = array_diff(array_values($values), array_values($tags));
		if (empty($store)) {
			return $tags;
		}

		// Store the values.
		foreach ($store as $value)
		{
			// Insert the new tag.
			$this->_db->setQuery(
				'INSERT INTO #__code_tags' .
				' (tag)' .
				' VALUES' .
				' ('.$this->_db->quote($value).')'
			);

			// Check for an error.
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			$tags[(int) $this->_db->insertid()] = $value;
		}

		return $tags;
	}

	/**
	 * Method to make sure a set of legacy user ids are syncronized with the GForge server.  This
	 * method will return an associative array of legacy => local user id values.
	 *
	 * @param   array  $ids  An array of legacy GForge user ids.
	 *
	 * @return  array  An array of legacy => local user ids.
	 *
	 * @since   1.0
	 */
	private function _syncUsers($ids)
	{
		// Initialize variables.
		$users = array();

		// Ensure the ids are integers.
		JArrayHelper::toInteger($ids);

		// Build the query to see if the items already exist.
		$this->_db->setQuery(
			'SELECT user_id, jc_user_id' .
			' FROM #__code_users' .
			' WHERE jc_user_id IN ('.implode(',', $ids).')'
		);

		// Execute the query to find out if the items exist.
		$exists = (array) $this->_db->loadObjectList();

		// Build out the array of users based on those that already exist.
		foreach ($exists as $exist) {
			$users[$exist->jc_user_id] = (int) $exist->user_id;
		}

		// Get the list of user ids for user objects to extract data from the server.
		$get = array_diff($ids, array_keys($users));
		if (empty($get)) {
			return $users;
		}

		// Get the list of user objects from the server.
		$got = $this->gforge->getUsersById($get);
		if (empty($got)) {
			$this->setError('Unable to get users from the server.');
			return false;
		}

		// Sync each tracker item.
		foreach ($got as $user)
		{
			// Get a user table object.
			$table = $this->getTable('User', 'CodeTable');

			// Load any existing data by email address.
			$table->loadByEmail($user->email);

			// Populate the appropriate fields from the server data object.
			$data = array(
				'jc_user_id' => $user->user_id,
				'username' => $user->unix_name,
				'email' => $user->email,
				'registerDate' => $user->create_date,
				'first_name' => $user->firstname,
				'last_name' => $user->lastname
			);

			// Do a little state conversion.
			if ($user->status == 2) {
				$data['block'] = 1;
			}

			// Bind the data to the user object.
			$table->bind($data);

			// Attempt to store the user data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
			$this->processingTotals['users']++;
			$users[$table->jc_user_id] = (int) $table->id;
			// echo "adding user=" . $table->jc_user_id . "\n";
		}

		return $users;
	}

	private function _populateTrackerFields($trackerId)
	{
		$fields = $this->gforge->getTrackerFields($trackerId);

		foreach ($fields as $field)
		{
			if (empty($this->fields[$field->tracker_extra_field_id])) {
				$this->fields[$field->tracker_extra_field_id] = array(
					'field_id' => $field->tracker_extra_field_id,
					'name' => $field->field_name,
					'alias' => $field->alias,
					'tracker_id' => $field->tracker_id
				);

				if ($field->alias == 'status') {
					$this->_populateTrackerStatus($this->fields[$field->tracker_extra_field_id], $trackerId);
				}
			}

			$this->_populateTrackerFieldValues($this->fields[$field->tracker_extra_field_id], $trackerId);
		}
	}

	private function _populateTrackerStatus($field, $legacyTrackerId)
	{
		// Get a tracker table object.
		$tracker = $this->getTable('Tracker', 'CodeTable');
		$tracker->loadByLegacyId($legacyTrackerId);

		$values = $this->gforge->getTrackerFieldValues($field['field_id']);
		foreach ($values as $value)
		{
			// Get a tracker issue file table object.
			$table = $this->getTable('TrackerStatus', 'CodeTable');

			// Load any existing data by legacy id.
			$table->loadByLegacyId($value->element_id);

			// Skip over rows that exist and haven't changed.
			if ($table->status_id && ($table->title == $value->element_name) && ($table->state_id == $value->status_id)) {
				$this->status[(int) $value->element_id] = (int) $table->status_id;
				continue;
			}

			// Populate the appropriate fields from the server data object.
			$data = array(
				'tracker_id' => $tracker->tracker_id,
				'state_id' => $value->status_id,
				'title' => $value->element_name,
				'jc_tracker_id' => $legacyTrackerId,
				'jc_status_id' => $value->element_id
			);

			// Bind the data to the object.
			$table->bind($data);

			// Attempt to store the data.
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}

			$this->status[(int) $value->element_id] = (int) $table->status_id;
		}

		return true;
	}

	private function _populateTrackerFieldValues($field)
	{
		$values = $this->gforge->getTrackerFieldValues($field['field_id']);

		foreach ($values as $value)
		{
			if (empty($this->fieldValues[$value->element_id])) {
				$this->fieldValues[$value->element_id] = array(
					'value_id' => $value->element_id,
					'field_id' => $value->tracker_extra_field_id,
					'name' => $value->element_name
				);
			}
		}
	}

	private function _addActivity($type, $xref, $userId, $issueId, $date)
	{
		$db = JFactory::getDbo();

		$query = 'INSERT IGNORE INTO #__code_activity_detail SET activity_type = ' . (int) $type .
		', activity_xref_id = ' . (int) $xref .
		', jc_user_id = ' . (int) $userId .
		', jc_issue_id = ' . (int) $issueId .
		', activity_date = ' . $db->quote($date);

		$db->setQuery($query);
		if (!$db->query())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		// echo "added activity type: $type:$userId:$issueID:$date\n";
		return true;
	}

	private function _addCreateActivities($data)
	{
		if (!$this->_addActivity(1, $data['jc_issue_id'], $data['jc_created_by'], $data['jc_issue_id'], $data['created_date']))
		{
			return false;
		}
		if (strpos($data['description'], "/pull/") !== false)
		{
			if (!$this->_addActivity(7, $data['jc_issue_id'], $data['jc_created_by'], $data['jc_issue_id'], $data['created_date']))
			{
				return false;
			}
		}
		return true;
	}

	private function _addFileActivity($data)
	{
		if (strpos($data['name'], 'diff') !== false || strpos($data['name'], 'patch') !== false)
		{
			if (!$this->_addActivity(5, $data['jc_file_id'], $data['jc_created_by'], $data['jc_issue_id'], $data['created_date']))
			{
				return false;
			}
		}
		return true;
	}

	private function _addCommentActivity($data)
	{
		if (!$this->_addActivity(2, $data['jc_response_id'], $data['jc_created_by'], $data['jc_issue_id'], $data['created_date']))
		{
			return false;
		}
		if (strpos($data['body'], "/pull/") !== false || strpos($data['body'], "/compare/") !== false || strpos($data['body'], ".diff") !== false)
		{
			if (!$this->_addActivity(6, $data['jc_response_id'], $data['jc_created_by'], $data['jc_issue_id'], $data['created_date']))
			{
				return false;
			}
		}
		if (strpos($data['body'], "@test") !== false)
		{
			if (!$this->_addActivity(4, $data['jc_response_id'], $data['jc_created_by'], $data['jc_issue_id'], $data['created_date']))
			{
				return false;
			}
		}
		return true;
	}


}