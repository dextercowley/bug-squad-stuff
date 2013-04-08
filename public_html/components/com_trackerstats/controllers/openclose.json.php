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
class TrackerstatsControllerOpenclose extends JControllerLegacy
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
		$model = $this->getModel('Openclose', 'TrackerstatsModel');
		$items = $model->getIssueCounts();
		$state = $model->getState();

		$periodType = $state->get('list.period');

		$periodTitle = array(1 => 'Weeks', 2 => 'Months', 3 => 'Quarters');
		$axisLabels = array('None', 'Week', '30 Days', '90 Days');
		$periodText = $periodTitle[$periodType];
		$axisLableText = $axisLabels[$periodType];

		$title = "Issues Opened and Closed for Past Four $periodText";

		$ticks = array();
		$counts = array();

		$counts['Opened'][] = (int) $items[0]->opened4;
		$counts['Opened'][] = (int) $items[0]->opened3;
		$counts['Opened'][] = (int) $items[0]->opened2;
		$counts['Opened'][] = (int) $items[0]->opened1;

		$counts['Closed'][] = (int) $items[1]->closed4;
		$counts['Closed'][] = (int) $items[1]->closed3;
		$counts['Closed'][] = (int) $items[1]->closed2;
		$counts['Closed'][] = (int) $items[1]->closed1;

		$counts['Fixed'][] = (int) $items[1]->fixed4;
		$counts['Fixed'][] = (int) $items[1]->fixed3;
		$counts['Fixed'][] = (int) $items[1]->fixed2;
		$counts['Fixed'][] = (int) $items[1]->fixed1;

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
		$types = array_keys($counts);
		$label1->label = $types[0];
		$label2->label = $types[1];
		$label3->label = $types[2];
		$data = array($counts[$types[0]], $counts[$types[1]], $counts[$types[2]]);
		$labels = array($label1, $label2, $label3);


		// assemble array
		$return = array($data, $ticks, $labels, $title);

		// Use the correct json mime-type
		header('Content-Type: application/json');

		// Send the response.
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
}
