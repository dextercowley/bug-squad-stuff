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
		foreach($items as $item)
		{
			$ticks[] = $item->name;
			$trackerPoints[] = (int) $item->tracker_points;
			$testPoints[] = (int) $item->test_points;
			$codePoints[] = (int) $item->code_points;
		}

// 		$s1 = array(200,600,700,1000);
// 		$s2 = array(460, 210, 690, 820);
// 		$s3 = array(260, 440, 320, 200);
		$data = array($trackerPoints, $testPoints, $codePoints);
// 		$ticks = array('Feb', 'Mar', 'Apr', 'May');
		$label1 = new stdClass();
		$label2 = new stdClass();
		$label3 = new stdClass();
		$label1->label = 'Tracker Points';
		$label2->label = 'Test Points';
		$label3->label = 'Code Points';
		$labels = array($label1, $label2, $label3);
		$title = new stdClass();
		$title->title = "Bug Squad Activity by Person";
		$title->subtitle = "Past 30 Days";
		// assemble array
		$return = (array($data, $ticks, $labels, $title));
		// Check the data.
		if (empty($return))
		{
			$return = array();
		}

		// Use the correct json mime-type
		header('Content-Type: application/json');

		// Send the response.
		echo json_encode($return);
		JFactory::getApplication()->close();
	}
}
