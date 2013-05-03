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

$listOrder	= '';
$listDirn	= '';
$listFilter = '';
// $jsonSource = $this->baseurl . "/components/com_trackerstats/json/getbarchartdata.php";
$jsonSource = $this->baseurl . '/index.php?option=com_trackerstats&task=openclose.display&format=json';
JHtml::_('barchart.barchart', 'barchart', 'barchart', false, false, 20);
?>

<h2>Open and Close Activity</h2>
<div id="barchart" style="width:700px; height:300px;" href="<?php echo $jsonSource; ?>"></div>

</br>
<div>
<p>Note: An issue in the tracker may be closed in one of two ways. It may be fixed with a code change, or it may be closed because it was
a duplicate issue or not considered to be a bug.</p>
<p>Fixed issues are issues for which a code change was made to fix the issue. Other Closed issues are issues that were closed
because they were duplicate reports or not bugs.</p></div>
<h3>Chart Options</h3>

<div class="form-inline">
<fieldset>
		<label>Period</label>
		<select id="period" name="period" class="input-small" size="1" >
			<option value="1" selected="selected">7 Days</option>
			<option value="2">30 Days</option>
			<option value="3">90 Days</option>
		</select>
		<button class="button" id="dataUpdate" >Update Chart</button>
</fieldset>
</div>