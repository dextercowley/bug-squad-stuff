<?php
/**
 * @version		$Id: default_unit.php 398 2010-06-13 17:53:03Z louis $
 * @package		Joomla.Site
 * @subpackage	com_code
 * @copyright	Copyright (C) 2009 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
	<p>
		<?php echo round($this->item->ut_pass_pct); ?>% of the tests passed,
		<?php echo round($this->item->ut_fail_pct); ?>% failed, and
		<?php echo round($this->item->ut_error_pct); ?>% of them had errors preventing them from running.
	</p>

<?php if (!empty($this->item->ut_delta['-']) || !empty($this->item->ut_delta['+'])) : ?>
<a class="modal" rel="{handler:'adopt',adopt:'unit-test-delta'}">
	View the test report changes from the last build.</a>
<div style="display:none;">
<div id="unit-test-delta">
<?php if (!empty($this->item->ut_delta['-'])) : ?>
	<h3>
		Fixed tests since the last nightly build.
	</h3>

	<ul>
<?php foreach ($this->item->ut_delta['-'] as $test) : ?>
		<li>
			<h4>
				<?php echo $test->class; ?>
			</h4>
			<ul style="list-style-type: square;">
			<?php foreach ($test->case as $case) : ?>
				<li>
					<?php echo $case; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if (!empty($this->item->ut_delta['+'])) : ?>
	<h3>
		Broken tests since the last nightly build.
	</h3>

	<ul>
<?php foreach ($this->item->ut_delta['+'] as $test) : ?>
		<li>
			<h4>
				<?php echo $test->class; ?>
			</h4>
			<ul style="list-style-type: square;">
			<?php foreach ($test->case as $case) : ?>
				<li>
					<?php echo $case; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</li>
<?php endforeach; ?>
	</ul>
<?php endif; ?>
</div>
</div>
<?php endif; ?>
