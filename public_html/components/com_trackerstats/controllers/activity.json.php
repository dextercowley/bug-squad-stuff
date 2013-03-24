<?php
/**
 * @package     com_trackerstats
 *
 * @copyright   Copyright (C) 2013 Mark Dexter. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * JSON controller for Trackerstats -- Returns data array for rendering bar charts
 *
 * @since       2.5
 */
class TrackerstatsControllerActivity extends JControllerLegacy
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
		$model = $this->getModel('Activity', 'TrackerstatsModel');
		$items = $model->getItems();
		$state = $model->getState();

		$periodType = $state->get('list.period');
		$activityType = $state->get('list.activity_type');

		$periodTitle = array(1 => 'Weeks', 2 => 'Months', 3 => 'Quarters');
		$axisLabels = array('None', 'Week', '30 Days', '90 Days');
		$periodText = $periodTitle[$periodType];
		$axisLableText = $axisLabels[$periodType];

		$activityTypes = array('All', 'Tracker', 'Test', 'Code');
		$activityText = $activityTypes[$activityType];
		$title = "$activityText Points for Past Four $periodText";

		$ticks = array();
		$points = array();

		// Build series arrays in reverse order for the chart
		foreach ($items as $item)
		{
			$group = $item->activity_group;
			$points[$group][] = (int) $item->p4;
			$points[$group][] = (int) $item->p3;
			$points[$group][] = (int) $item->p2;
			$points[$group][] = (int) $item->p1;
		}
		$endDate = $items[0]->end_date;
		$periodDays = array(7,7,30,90);
		$dayInterval = $periodDays[$periodType];

		$ticks[] = date('d M', strtotime($endDate . '-' . (($dayInterval * 4) - 1) . ' day')) . ' - ' .
			date('d M', strtotime($endDate . '-' . ($dayInterval * 3) . ' day'));
		$ticks[] = date('d M', strtotime($endDate . '-' . (($dayInterval * 3) - 1) . ' day')) . ' - ' .
				date('d M', strtotime($endDate . '-' . ($dayInterval * 2) . ' day'));
		$ticks[] = date('d M', strtotime($endDate . '-' . (($dayInterval * 2) - 1) . ' day')) . ' - ' .
				date('d M', strtotime($endDate . '-' . ($dayInterval * 1) . ' day'));
		$ticks[] = date('d M', strtotime($endDate . '-' . (($dayInterval * 1) - 1) . ' day')) . ' - ' .
				date('d M', strtotime($endDate . '-' . ($dayInterval * 0) . ' day'));

		$data = array();
		$label1 = new stdClass();
		$label2 = new stdClass();
		$label3 = new stdClass();
		$types = array_keys($points);
		$label1->label = $types[0] . ' Points';
		if ($activityType === 0)
		{
			$label2->label = $types[1] . ' Points';
			$label3->label = $types[2] . ' Points';
			$data = array($points[$types[0]], $points[$types[1]], $points[$types[2]]);
			$labels = array($label1, $label2, $label3);
		}
		else
		{
			$data = array($points[$types[0]]);
			$labels = array($label1);
		}

		// assemble array
		$return = array($data, $ticks, $labels, $title);

		// Use the correct json mime-type
		header('Content-Type: application/json');

		// Send the response.
		echo json_encode($return);


	}
}
