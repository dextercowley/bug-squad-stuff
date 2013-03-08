<?php
/**
 * @version		$Id: default.php 410 2010-06-21 02:51:34Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load the JavaScript behaviors.
JHtml::_('behavior.mootools');
JHtml::_('behavior.modal');
JHtml::script('status.js', 'components/com_code/media/js/');

// Load the CSS stylesheets.
JHtml::stylesheet('default.css', 'components/com_code/media/css/');
?>

<h1 class="devhead">
	<?php echo $this->item->branch_title,' ',JText::sprintf('COM_CODE_NIGHTLY_BUILD_D', JHtml::_('date', $this->item->build_date)); ?>
</h1>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_code&view=summary'); ?>">
		View Project Summary &raquo;</a>
</p>

<?php if (!empty($this->downloads->packages)) : ?>
<h2>
	<?php echo JText::_('Downloads'); ?>
</h2>
<?php foreach ($this->downloads->packages as $package) : ?>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_code&task=nightly.download&file='.$package['file']); ?>">
		<?php echo $package['file']; ?></a>
	| <?php echo $this->formatBytes($package['size']); ?>
	<br />
	md5: <?php echo $package['md5']; ?>
	<br />
	sha1: <?php echo $package['sha1']; ?>
</p>
<?php endforeach; ?>
<?php endif; ?>

<h2>
	<?php echo JText::_('COM_CODE_TEST_REPORTS'); ?>
</h2>

<!-- START UNIT TEST REPORT -->
<div style="float:left;width:45%;padding-right:10px;">
	<h3>
		<?php echo JText::_('COM_CODE_UNIT_TEST_REPORT'); ?>
	</h3>
<?php if ($this->item->ut_tests) : ?>
	<?php echo $this->loadTemplate('unit'); ?>
<?php else : ?>
	<p>
		<?php echo JText::_('COM_CODE_UNIT_TEST_REPORT_UNAVAILABLE'); ?>
	</p>
<?php endif; ?>
</div>
<!-- END UNIT TEST REPORT -->

<!-- START SYSTEM TEST REPORT -->
<div style="float:left;width:45%;padding-left:10px;">
	<h3>
		<?php echo JText::_('COM_CODE_SYSTEM_TEST_REPORT'); ?>
	</h3>
<?php if ($this->item->st_tests) : ?>
	<?php echo $this->loadTemplate('system'); ?>
<?php else : ?>
	<p>
		<?php echo JText::_('COM_CODE_SYSTEM_TEST_REPORT_UNAVAILABLE'); ?>
	</p>
<?php endif; ?>
</div>
<div class="clr"></div>
<!-- END SYSTEM TEST REPORT -->


<?php if (!empty($this->item->changelog)) : ?>
<h2>
	<?php echo JText::_('COM_CODE_DAILY_BUILD_SUMMARY'); ?>
</h2>
<div id="build-meta">
<?php foreach ($this->item->changelog as $changeset) : ?>

<a href="<?php echo JRoute::_('index.php?option=com_code&view=build&branch_path='.$this->item->branch_path.'&revision_id='.$changeset->revision_id); ?>" title="View build <?php echo $changeset->revision_id; ?> report.">
	Revision <?php echo $changeset->revision_id; ?> by <?php echo $changeset->user_name; ?> on <?php echo JHtml::_('date', $changeset->commit_date, JText::_('DATE_FORMAT_LC2')); ?></a>
<p class="log" style="white-space: pre-line;"><?php echo $changeset->log; ?></p>
<?php endforeach; ?>
</div>
<?php endif; ?>
<div class="clr"></div>
