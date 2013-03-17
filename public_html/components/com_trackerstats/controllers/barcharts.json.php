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
		$return = array();

		$s1 = array(200,600,700,1000);
		$s2 = array(460, 210, 690, 820);
		$s3 = array(260, 440, 320, 200);
		$data = array($s1,$s2,$s3);
		$ticks = array('Feb', 'Mar', 'Apr', 'May');
		$label1 = new stdClass();
		$label2 = new stdClass();
		$label3 = new stdClass();
		$label1->label = 'Hotel';
		$label2->label = 'Event Registration';
		$label3->label = 'Airfare';
		$labels = array($label1, $label2, $label3);
		$title = new stdClass();
		$title->title = "New Title with new JS Method";
		$title->subtitle = "Test Subtitle";
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
