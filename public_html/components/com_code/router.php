<?php
/**
 * @version		$Id: router.php 414 2010-06-24 00:43:39Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Function to build a Joomla Code URL route.
 *
 * @param	array	The array of query string values for which to build a route.
 * @return	array	The URL route with segments represented as an array.
 * @since	1.0
 */
function CodeBuildRoute(& $query)
{
	// Declare static variables.
	static $items;
	static $cache = array();

	// Initialize variables.
	$segments = array();

	// Get the relevant menu items if not loaded.
	if (empty($items))
	{
		// Get all relevant menu items.
		$menu	= JFactory::getApplication()->getMenu();
		$items	= $menu->getItems('component', 'com_code');

		// Build an array of found menu item ids.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			// Check to see if we have found the code status summary menu item.
			if (empty($cache['summary']) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'summary')) {
				$cache['summary'] = $items[$i]->id;
			}
		}
	}

	// Only one project for now.
	$segments[] = 'cms';
	unset($query['project_id']);

	if (!empty($query['view']))
	{
		switch ($query['view'])
		{
			case 'help':
				if (!empty($cache['help']))
				{
					unset($query['view']);
					$query['Itemid'] = $cache['help'];
				}
				break;
			case 'build':
				if (!empty($cache['summary']))
				{
					unset($query['view']);
					$query['Itemid'] = $cache['summary'];

					$segments[] = 'builds';
					$segments[] = @$query['branch_path'];
					$segments[] = @$query['revision_id'];
					unset($query['branch_path']);
					unset($query['revision_id']);
				}

				break;
			case 'branch':
				if (!empty($cache['summary']))
				{
					unset($query['view']);
					$query['Itemid'] = $cache['summary'];

					$segments[] = 'builds';
					$segments[] = @$query['branch_path'];
					unset($query['branch_path']);
				}
				break;
			case 'nightly':
				if (!empty($cache['summary']))
				{
					unset($query['view']);
					$query['Itemid'] = $cache['summary'];

					$segments[] = 'history';
					if (!empty($query['date'])) {

						$date = JFactory::getDate($query['date']);

						$segments[] = $date->toFormat('%Y');
						$segments[] = $date->toFormat('%m');
						$segments[] = $date->toFormat('%d');

						unset($query['date']);
					}
				}
				break;
			case 'issue':
				if (!empty($cache['summary']))
				{
					unset($query['view']);
					$query['Itemid'] = $cache['summary'];

					$segments[] = 'trackers';
					$segments[] = @$query['tracker_alias'];
					$segments[] = @$query['issue_id'];
					unset($query['tracker_alias']);
					unset($query['tracker_id']);
					unset($query['issue_id']);
				}

				break;
			case 'tracker':
				if (!empty($cache['summary']))
				{
					unset($query['view']);
					$query['Itemid'] = $cache['summary'];

					$segments[] = 'trackers';
					$segments[] = @$query['tracker_alias'];
					unset($query['tracker_alias']);
					unset($query['tracker_id']);
				}
				break;
			case 'trackers':
				if (!empty($cache['summary']))
				{
					unset($query['view']);
					$query['Itemid'] = $cache['summary'];

					$segments[] = 'trackers';
				}
				break;
			case 'summary':
			default:
				if (!empty($cache['summary']))
				{
					unset($query['view']);
					$query['Itemid'] = $cache['summary'];
				}
				break;
		}
	}
	elseif (!empty($query['task']))
	{
		if (!empty($cache['summary']))
		{
			unset($query['view']);
			$query['Itemid'] = $cache['summary'];
		}
	}

	return $segments;
}

/**
 * Function to parse a Joomla Code URL route.
 *
 * @param	array	The URL route with segments represented as an array.
 * @return	array	The array of variables to set in the request.
 * @version	1.0
 */
function CodeParseRoute($segments)
{
	// Initialize variables.
	$vars = array();

	// If no segments exist then there is no defined project and we do not support that at this time.
	if (empty($segments)) {
		JError::raiseError(404, 'Resource not found.');
	}

	// Get the project from the first segment.
	$projectAlias = array_shift($segments);

	// The only supported project for now is the Joomla! CMS.
	if ($projectAlias != 'cms') {
		JError::raiseError(404, 'Resource not found.');
	}
	$vars['project_id'] = 1;

	// If no further segments exist then we assume the project summary page was requested.
	if (empty($segments)) {
		$vars['view'] = 'summary';
		return $vars;
	}

	// Get the view/task definition from the next segment.
	switch (array_shift($segments))
	{
		// View any code commit build reports.
		case 'builds':

			// Get the sanitized branch path from the request.
			$fullPath = JFilterInput::getInstance()->clean(implode('/', $segments), 'path');

			// Get the sanitized branch path from the request.
			$partPath = JFilterInput::getInstance()->clean(implode('/', array_slice($segments, 0, -1)), 'path');

			// Search the database for the appropriate branch.
			$db = JFactory::getDBO();
			$db->setQuery(
				'SELECT branch_id, path' .
				' FROM #__code_branches' .
				' WHERE path = '.$db->quote($fullPath) .
				' OR path = '.$db->quote($partPath),
				0, 1
			);
			$branch = $db->loadObject();

			// If we have found a branch finish setting up the request.
			if ($branch) {

				// we are looking at a branch
				if ($branch->path == $fullPath) {
					$vars['view'] = 'branch';
					$vars['branch_id'] = (int) $branch->branch_id;
					$vars['branch_path'] = $branch->path;
				}
				// we are looking at a build in a path
				else if (($branch->path == $partPath) && (is_numeric(end($segments)))) {
					$vars['view'] = 'build';
					$vars['branch_id'] = (int) $branch->branch_id;
					$vars['branch_path'] = $branch->path;
					$vars['revision_id'] = (int) end($segments);
				}
				// If the branch isn't found throw a 404.
				else {
					JError::raiseError(404, 'Resource Not Found');
				}
			}
			// If the branch isn't found throw a 404.
			else {
				JError::raiseError(404, 'Resource Not Found');
			}
			break;

		// View trackers and issues.
		case 'trackers':

			// If there is no given tracker name we default to viewing all trackers and return.
			if (empty($segments)) {
				$vars['view'] = 'trackers';
				return $vars;
			}

			// Get the tracker alias from the next segment.
			$trackerAlias = str_replace(':', '-', array_shift($segments));

			// Search the database for the appropriate tracker.
			$db = JFactory::getDBO();
			$db->setQuery(
				'SELECT tracker_id' .
				' FROM #__code_trackers' .
				' WHERE alias = '.$db->quote($trackerAlias),
				0, 1
			);
			$trackerId = (int) $db->loadResult();

			// If the tracker isn't found throw a 404.
			if (!$trackerId) {
				JError::raiseError(404, 'Resource Not Found');
			}

			// We found a valid tracker with that alias so set the id.
			$vars['tracker_id'] = $trackerId;

			// If we have an issue id in the next segment lets set that in the request.
			if (!empty($segments) && is_numeric($segments[0])) {
				$vars['view'] = 'issue';
				$vars['issue_id'] = (int) array_shift($segments);
			}
			// No issue id so we are looking at the tracker itself.
			else {
				$vars['view'] = 'tracker';
			}
			break;

		// View available downloads.
		case 'downloads':

			// /release/package.html
			break;

		// Download the file.
		case 'download':

			// /file/path.ext
			break;

		// View historical build information.
		case 'history':

			$vars['view'] = 'nightly';

			if (count($segments) == 3) {

				// Get the sanitized date path from the request.
				$date = JFilterInput::getInstance()->clean(array_shift($segments), 'int');
				$date .= '-'.JFilterInput::getInstance()->clean(array_shift($segments), 'int');
				$date .= '-'.JFilterInput::getInstance()->clean(array_shift($segments), 'int');

				// Lookup the historical build by date.
				$db = JFactory::getDBO();
				$db->setQuery(
					'SELECT build_id' .
					' FROM #__code_nightly_builds' .
					' WHERE build_date = '.$db->quote($date)
				);
				$buildId = (int) $db->loadResult();

				// If the build isn't found throw a 404.
				if (!$buildId) {
					JError::raiseError(404, 'Resource Not Found');
				}

				$vars['build_id'] = $buildId;
			}
			break;
	}

	return $vars;
}
