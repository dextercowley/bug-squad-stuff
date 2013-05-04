<?php
/**
 * @subpackage	com_trackerstats
 * @copyright	Copyright (C) 2011 Mark Dexter. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// Code to support edit links for joomaprosubs
// Create a shortcut for params.

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

// Get the user object.
$user = JFactory::getUser();
// Check if user is allowed to add/edit based on trackerstats permissions.
$canEdit = $user->authorise('core.edit', 'com_trackerstats');

$listOrder	= '';
$listDirn	= '';
$listFilter = '';
// $jsonSource = $this->baseurl . "/components/com_trackerstats/json/getbarchartdata.php";
$jsonSource = $this->baseurl . '/index.php?option=com_trackerstats&amp;task=barcharts.display&amp;format=json';
JHtml::_('barchart.barchart', 'barchart', 'barchart', true);
JFactory::getDocument()->addScriptDeclaration("
	(function ($){
		$(document).ready(function (){
			$('#hidedates').hide();
			$('#period').change(function() {
				if ($(this).val() == 5)
				{
					$('#hidedates').show();
				}
				else
				{
					$('#hidedates').hide();
				}
			});
		});
	})(jQuery);
		"
);
?>

<h2>Bug Squad Activity</h2>
<div id="barchart" style="width:700px; height:600px;" data-href="<?php echo $jsonSource; ?>"></div>
</br>
<h3>Chart Options</h3>
<div class="form-inline">
<fieldset>
		<label>Period</label>
		<select id="period" name="period" class="input" size="1" >
			<option value="1" selected="selected">7 Days</option>
			<option value="2">30 Days</option>
			<option value="3">90 Days</option>
			<option value="4">1 Year</option>
			<option value="5">Custom Period</option>
		</select>

		<label>Type</label>
		<select id="type" name="type" class="input-small" size="1" >
			<option value="0" selected="selected">All</option>
			<option value="1">Tracker</option>
			<option value="2">Test</option>
			<option value="3">Code</option>
		</select>

		<button class="dataUpdate button" id="dataUpdate" >Update Chart</button>
	</br>
	</br>
	<div id="hidedates" class="form-inline">
	<label>Start Date</label>
	<input id="start_date" class="datepicker input-small" type="text" />
	<label>End Date</label>
	<input id="end_date" class="datepicker input-small" type="text" />
	</div>

  </fieldset>
  </div>