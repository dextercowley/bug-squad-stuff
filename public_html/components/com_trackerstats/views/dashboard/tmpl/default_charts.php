<?php
/**
 * @subpackage	com_trackerstats
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// Code to support edit links for joomaprosubs
// Create a shortcut for params.

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$chartType = $this->state->get('list.activity_type');
$chartPeriod = $this->state->get('list.period');
$startDate = $this->state->get('list.startdate');
$endDate = $this->state->get('list.enddate');
$typeSelected = array('', '', '', '');
$periodSelected = array('', '', '', '', '', '');
$typeSelected[$chartType] = 'selected="selected"';
$periodSelected[$chartPeriod] = 'selected="selected"';


// $jsonSource = $this->baseurl . "/components/com_trackerstats/json/getbarchartdata.php";
$jsonSource = $this->baseurl . '/index.php?option=com_trackerstats&task=barcharts.display&format=json&type=' . $chartType . '&period=' . $chartPeriod;
if ($chartPeriod == 5)
{
	$jsonSource .= '&startdate=' . $startDate . '&enddate=' . $endDate;
}

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
<div id="barchart" style="width:700px; height:600px;" href="<?php echo $jsonSource; ?>"></div>
</br>
<h3>Chart Options</h3>
<form method="get" class="form-inline">
<fieldset>
		<label>Period</label>
		<select id="period" name="period" class="input" size="1" >
			<option value="1" <?php echo $periodSelected[1];?>>7 Days</option>
			<option value="2" <?php echo $periodSelected[2];?>>30 Days</option>
			<option value="3" <?php echo $periodSelected[3];?>>90 Days</option>
			<option value="4" <?php echo $periodSelected[4];?>>1 Year</option>
			<option value="5" <?php echo $periodSelected[5];?>>Custom Period</option>
		</select>

		<label>Type</label>
		<select id="type" name="type" class="input-small" size="1" >
			<option value="0" <?php echo $typeSelected[0];?>>All</option>
			<option value="1" <?php echo $typeSelected[1];?>>Tracker</option>
			<option value="2" <?php echo $typeSelected[2];?>>Test</option>
			<option value="3" <?php echo $typeSelected[3];?>>Code</option>
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
  </form>