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
		$ticks = array();
		$trackerPoints = array();
		$testPoints = array();
		$codePoints = array();
		$title = new stdClass();

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
		$data = array($trackerPoints, $testPoints, $codePoints);
		$label1 = new stdClass();
		$label2 = new stdClass();
		$label3 = new stdClass();
		$label1->label = 'Tracker Points';
		$label2->label = 'Test Points';
		$label3->label = 'Code Points';
		$labels = array($label1, $label2, $label3);

		$title = "Points for Past 30 Days";

		// assemble array
		$return = (array($data, $ticks, $labels, $title));

		// Use the correct json mime-type
		header('Content-Type: application/json');

		// Send the response.
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
}
