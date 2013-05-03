<?php
/**
 * @package     com_trackerstats
 *
 * @copyright   Copyright (C) 2013 Mark Dexter. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * JSON controller for Trackerstats -- Returns data array for rendering wiki activity bar charts
 *
 * @since       2.5
 */
class TrackerstatsControllerWiki extends JControllerLegacy
{
	/**
	 * Method to display bar chart data
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function display($cachable = true, $urlparams = false)
	{
		// jSON URL which should be requested
		$json_url = 'http://docs.joomla.org/api.php?action=query&list=allusers&format=json&auexcludegroup=bot&aulimit=100&auprop=editcount&auactiveusers=';
		$ch = curl_init( $json_url );
		$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
				CURLOPT_POSTFIELDS => ''
		);

		// Setting curl options
		curl_setopt_array( $ch, $options );

		// Getting results
		$users =  json_decode(curl_exec($ch)); // Getting jSON result string

		// Convert to array for processing
		$workArray = array();
		$totalEditsArray = array();
		foreach ($users->query->allusers as $user)
		{
			if ($user->name == 'MediaWiki default') continue;
			$workArray[$user->name] = $user->recenteditcount;
			$totalEditsArray[$user->name] = $user->editcount;
		}
		asort($workArray, SORT_NUMERIC);
		// Slice the last 25 entries
		$maxCount = 25;
		$arrayCount = count($workArray);
		if ($arrayCount > $maxCount)
		{
			$sliceStart = $arrayCount - $maxCount;
			$workArray = array_slice($workArray, $sliceStart, $maxCount);
		}

		$people = array();
		$edits = array();
		$i = 0;
		foreach ($workArray as $k => $v)
		{
			if ($v > 0 && $i++ < $maxCount)
			{
				$edits[] = $v;
				$people[] = $k . ' (' . $totalEditsArray[$k] . ' total edits)';
			}
		}
		$label = new stdClass();
		$label->label = 'Wiki Edits';

		header('Content-Type: application/json');

		// Send the response.
		echo json_encode(array(array($edits), $people, array($label), 'Wiki Edits by Contributor in Past 30 Days'));
		JFactory::getApplication()->close();
	}
}
