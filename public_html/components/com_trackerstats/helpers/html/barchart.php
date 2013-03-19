<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * HTML utility class for creating a sortable table list
 *
 * @package     com_trackerstats
 * @subpackage  HTML
 * @since       2.5
 */
abstract class JHtmlBarchart
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Method to load the Barchart script to display a bar chart using jQuery and jqPlot
	 *
	 * @param   string   $containerID             DOM id of the element where the chart will be rendered
	 * @param   string   $urlId                   DOM id of the element whose href attribute has the URL to the JSON data
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public static function barchart($containerId, $urlId)
	{
		// Only load once
		if (isset(self::$loaded[__METHOD__]))
		{
			return;
		}

		// Depends on jQuery UI
		$document = JFactory::getDocument();
		$document->addScript('components/com_trackerstats/media/js/jquery-1.9.1.min.js', 'text/javascript', false);
		$document->addScript('components/com_trackerstats/media/js/jquery-noconflict.js', 'text/javascript', false);
		$document->addScript('components/com_trackerstats/media/js/jquery.jqplot.min.js', 'text/javascript', false);
		$document->addScript('components/com_trackerstats/media/js/jqplot.barRenderer.min.js', 'text/javascript', true);
		$document->addScript('components/com_trackerstats/media/js/jqplot.categoryAxisRenderer.min.js', 'text/javascript', true);
		$document->addScript('components/com_trackerstats/media/js/jqplot.pointLabels.min.js', 'text/javascript', true);
		$document->addScript('components/com_trackerstats/media/js/barchart.js', 'text/javascript', true);
		$document->addStyleSheet( JURI::root( true ).'/components/com_trackerstats/media/css/jquery.jqplot.min.css' );

		// Attach sortable to document
		JFactory::getDocument()->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
					var barchart = new $.JQPLOTBarchart('" . $containerId . "','" . $urlId . "');
					});
			})(jQuery);
			"
		);
		JFactory::getDocument()->addScriptDeclaration("
			(function ($){
				$(document).ready(function (){
    			$('button.dataUpdate').click(function() {
					$('#" . $containerId . "').empty();
					// add the form variables to the URL
					var period = $('#period').val();
					var type = $('#type').val();
					var href = $('#" . $urlId . "').attr('href');
					href = href + '&period=' + period + '&activity_type=' + type;
					$('#" . $urlId . "').attr('href', href);
					var barChart = new $.JQPLOTBarchart('" . $containerId . "','" . $urlId . "');
				});
				});
			})(jQuery);
			"
		);
		// Set static array
		self::$loaded[__METHOD__] = true;
		return;
	}
}
