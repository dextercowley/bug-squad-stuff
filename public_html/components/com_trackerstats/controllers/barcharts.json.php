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
class TrackerstatsControllerBarcharts extends JControllerLegacy
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
		$model = $this->getModel('Dashboard', 'TrackerstatsModel');
		$items = $model->getItems();
		$state = $model->getState();

		$periodType = $state->get('list.period');
		$activityType = $state->get('list.activity_type');

		$periodTitle = array(1 => '7 Days', 2 => '30 Days', 3 => '90 Days', 4 => '12 Months');
		$periodText = $periodTitle[$periodType];

		$activityTypes = array('All', 'Tracker', 'Test', 'Code');
		$activityText = $activityTypes[$activityType];

// 		$title = new stdClass();
		$title = "$activityText Points for Past $periodText";

		$ticks = array();
		$trackerPoints = array();
		$testPoints = array();
		$codePoints = array();

		// Build series arrays in reverse order for the chart
		$i = count($items);
		while ($i > 0 )
		{
			$i--;
			$ticks[] = $items[$i]->name;
			$trackerPoints[] = (int) $items[$i]->tracker_points;
			$testPoints[] = (int) $items[$i]->test_points;
			$codePoints[] = (int) $items[$i]->code_points;
		}

		$data = array($trackerPoints);
		$label1 = new stdClass();
		$label2 = new stdClass();
		$label3 = new stdClass();
		$label1->label = 'Tracker Points';
		$label2->label = 'Test Points';
		$label3->label = 'Code Points';

		switch ($activityText)
		{
			case 'Tracker':
				$data = array($trackerPoints);
				$labels = array($lable1);
				break;

			case 'Test':
				$data = array($testPoints);
				$labels = array($label2);
				break;

			case 'Code':
				$data = array($codePoints);
				$labels = array($label3);
				break;

			case 'All':
			default:
				$data = array($trackerPoints, $testPoints, $codePoints);
				$labels = array($label1, $label2, $label3);
				break;
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
