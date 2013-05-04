<?php
/**
 * @package     com_trackerstats
 *
 * @copyright   Copyright (C) 2013 Mark Dexter. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * JSON controller for Trackerstats -- Returns data array for rendering snapshot history bar charts
 *
 * @since       2.5
 */
class TrackerstatsControllerSnapshot extends JControllerLegacy
{
	/**
	 * Method to display bar chart data
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel('Snapshot', 'TrackerstatsModel');
		$items = $model->getItems();
		$state = $model->getState();

		$periodType = $state->get('list.period');

		$periodTitle = array(1 => 'Days', 2 => 'Weeks', 3 => 'Months');
		$axisLabels = array('None', 'Day', '7 Days', '30 Days');
		$periodText = $periodTitle[$periodType];
		$axisLableText = $axisLabels[$periodType];

		$title = "Total Open Issues by Status for Past Four $periodText";

		$ticks = array();
		$counts = array();

		foreach ($items as $item)
		{
			$ticks[] = $item->snapshot_day;
			$objectArray = json_decode($item->status_counts);
			foreach ($objectArray as $object)
			{
				$counts[$object->status_name][$item->snapshot_day] = (int) $object->num_issues;
			}
		}

		$dataByStatus = array_values($counts);
		$data = array();
		foreach ($dataByStatus as $dataForOneStatus)
		{
			$data[] = array_values($dataForOneStatus);
		}
		$types = array_keys($counts);
		$labels = array();
		foreach($types as $type)
		{
			$object = new stdClass();
			$object->label = $type;
			$labels[] = $object;
		}

		// assemble array
		$return = array($data, $ticks, $labels, $title);

		// Use the correct json mime-type
		header('Content-Type: application/json');

		// Send the response.
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
}
