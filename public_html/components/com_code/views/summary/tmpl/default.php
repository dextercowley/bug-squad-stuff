<?php
/**
 * @version		$Id: default.php 454 2010-09-29 21:13:29Z louis $
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

<h1>
	<?php echo $this->escape($this->params->get('page_title', JText::_('COM_CODE_STATUS_SUMMARY'))); ?>
</h1>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_code&view=nightly'); ?>" title="View the nightly build report.">
		Nightly Build Report</a>
</p>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_code&view=nightly&date=yesterday'); ?>" title="View yesterday's nightly build report.">
		Yesterday's Nightly Build Report</a>
</p>
<?php if ($this->user->authorise('core.admin')) : ?>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_code&view=trackers'); ?>" title="View the project trackers.">
		View Trackers</a>
</p>
<?php endif; ?>

<h2>
	<?php echo JText::_('COM_CODE_DEVELOPMENT_STREAMS'); ?>
</h2>
<?php foreach ($this->items as $branch) : ?>
<div class="branch-summary branch-<?php echo $branch->branch_id; ?>">
	<h3>
		<a href="<?php echo JRoute::_('index.php?option=com_code&view=branch&branch_path='.$branch->path); ?>" title="View the <?php echo $branch->title; ?> status page.">
			<?php echo $branch->title; ?></a>
	</h3>


	<div style="float:right;width:210px;text-align:center;">
		<h4>
			Unit Testing Status
		</h4>

		<img src="http://chart.apis.google.com/chart?chs=210x75&amp;chd=t:<?php echo round($branch->ut_pass_pct); ?>,<?php echo round($branch->ut_fail_pct); ?>,<?php echo round($branch->ut_error_pct); ?>&amp;cht=p3&amp;chco=5AA426,E52626,444444&amp;chl=Passes|Failures|Errors" alt="Unit Test Report" />
	</div>

	<p class="summary">
		<?php echo $branch->summary; ?>
	</p>

	<p>
		<a href="<?php echo JRoute::_('index.php?option=com_code&view=build&branch_path='.$branch->path.'&revision_id='.$branch->revision_id); ?>" title="View build <?php echo $branch->revision_id; ?> report.">
		Latest build [<?php echo $branch->revision_id; ?>]</a> committed by <strong><?php echo $branch->user_name; ?></strong> on
		<strong><?php echo JHtml::_('date', $branch->commit_date, JText::_('DATE_FORMAT_LC2')); ?></strong>.
	</p>
	<p>
		More information can be found on the development stream
		<a href="<?php echo JRoute::_('index.php?option=com_code&view=branch&branch_path='.$branch->path); ?>" title="View the <?php echo $branch->title; ?> status page.">
		status page</a>.
	</p>
</div>
<div class="clr"></div>
<?php endforeach; ?>
