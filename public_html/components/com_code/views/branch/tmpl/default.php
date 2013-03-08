<?php
/**
 * @version		$Id: default.php 446 2010-09-29 15:08:51Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load the JavaScript behaviors.
JHtml::_('behavior.mootools');
JHtml::script('status.js', 'components/com_code/media/js/');

// Load the CSS stylesheets.
JHtml::stylesheet('default.css', 'components/com_code/media/css/');
?>

<div id= "branchstatus">
<h1>
	<?php echo $this->item->title; ?> Status
</h1>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_code&view=summary'); ?>">
		View Project Summary &raquo;</a>
</p>

<div id="unit-testing-report" style="float:right;width:210px;text-align:center;">
	<h4>
		Unit Testing Status
	</h4>

	<img src="http://chart.apis.google.com/chart?chs=210x75&amp;chd=t:<?php echo round($this->build->ut_pass_pct); ?>,<?php echo round($this->build->ut_fail_pct); ?>,<?php echo round($this->build->ut_error_pct); ?>&amp;cht=p3&amp;chco=5AA426,E52626,444444&amp;chl=Passes|Failures|Errors" alt="Unit Test Report" />
</div>

<p class="description">
	<?php echo $this->item->description; ?>
</p>
<div class="clr"></div>

<div id="latest-builds">
	<h2>
		Latest Builds
	</h2>
	<table width="100%" cellpadding="4px">
		<thead>
			<tr>
				<th>
					<?php echo JText::_('COM_CODE_BUILDS_BUILD'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_CODE_BUILDS_BUILD_USER'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_CODE_BUILDS_BUILD_DATE'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_CODE_BUILDS_BUILD_UT_PASS'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_CODE_BUILDS_BUILD_CODE_COVERAGE'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->builds as $i => $build) : ?>
			<tr class="<?php echo 'row',($i%2);?>" title="<?php echo $this->escape($build->log); ?>">
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_code&view=build&branch_path='.$this->item->path.'&revision_id='.$build->revision_id); ?>" title="View build <?php echo $build->revision_id; ?> report.">
						<?php echo $build->revision_id; ?></a>
				</td>
				<td>
					<?php echo $build->user_name; ?>
				</td>
				<td>
					<?php echo JHtml::_('date', $build->commit_date, 'M j, Y, G:i'); ?>
				</td>
				<td>
					<?php echo round($build->ut_pass_pct, 2); ?>%
				</td>
				<td>
					<?php echo round($build->methods_covered_pct, 2); ?>%
				</td>
			</tr>
<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>

</div>
<div class="clr"></div>
