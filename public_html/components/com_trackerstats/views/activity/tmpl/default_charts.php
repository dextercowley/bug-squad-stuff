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
$typeSelected = array('', '', '', '');
$periodSelected = array('', '', '', '');
$typeSelected[$chartType] = 'selected="selected"';
$periodSelected[$chartPeriod] = 'selected="selected"';


// $jsonSource = $this->baseurl . "/components/com_trackerstats/json/getbarchartdata.php";
$jsonSource = $this->baseurl . '/index.php?option=com_trackerstats&task=activity.display&format=json&type=' . $chartType . '&period=' . $chartPeriod;
JHtml::_('barchart.barchart', 'barchart', 'barchart', false);
?>

<h2>Total Bug Squad Activity By Type</h2>
<div id="barchart" style="width:600px; height:300px;" href="<?php echo $jsonSource; ?>"></div>

</br>
<h3>Chart Options</h3>

<form method="get" class="form-inline">
<fieldset>
		<label>Period</label>
		<select id="period" name="period" class="input-small" size="1" >
			<option value="1" <?php echo $periodSelected[1];?>>7 Days</option>
			<option value="2" <?php echo $periodSelected[2];?>>30 Days</option>
			<option value="3" <?php echo $periodSelected[3];?>>90 Days</option>
		</select>
		<label>Type</label>
		<select id="type" name="type" class="input-small" size="1" >
			<option value="0" <?php echo $typeSelected[0];?>>All</option>
			<option value="1" <?php echo $typeSelected[1];?>>Tracker</option>
			<option value="2" <?php echo $typeSelected[2];?>>Test</option>
			<option value="3" <?php echo $typeSelected[3];?>>Code</option>
		</select>
		<button class="button" id="dataUpdate" >Update Chart</button>
</fieldset>
</form>