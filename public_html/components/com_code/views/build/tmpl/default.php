<?php
/**
 * @version		$Id: default.php 398 2010-06-13 17:53:03Z louis $
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

<h1 class="devhead">
	<?php echo $this->item->branch_title,' ',JText::sprintf('COM_CODE_BUILD_N', $this->item->revision_id); ?>
</h1>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_code&view=branch&branch_path='.$this->item->branch_path); ?>">
		View Branch Summary &raquo;</a>
</p>

<div id="build-meta">
<p>Committed by <strong><?php echo $this->item->user_name; ?></strong> on <strong><?php echo JHtml::_('date', $this->item->commit_date, JText::_('DATE_FORMAT_LC2')); ?></strong>.</p>


</div>
<div class="clr"></div>
<div id="commit-log">
<h2>Commit Log</h2>
<p>
	The commit log is a record of commitments of code to the code repository, either to the trunk or to a branch. It provides
	a record of the changes and their purpose.
</p>
<div class="clr"></div>
<pre class="log" style="white-space: pre-line;"><?php echo $this->item->log; ?></pre>
</div>
<div class="clr"></div>
<div id="unit-testing-report">
<h2>Unit Testing Report</h2>

<p>

	Units are the smallest units of software, such as methods and classes. Unit tests isolate these units and evaluate whether
	they behave as intended. They work by setting a strict set of requirements for the unit and assessing whether they are met.
	Each test may have one or more assertions, which are the specifications that need to be met, although ideally there is one
	assertion per test. Failure to meet one specification in a test represents a failed test even if other specifications are
	met.

</p>
<div class="clr"></div>

<div class="testgraph">
<img src="http://chart.apis.google.com/chart?chs=250x100
&amp;chd=t:<?php echo round($this->item->ut_pass_pct); ?>,<?php echo round($this->item->ut_fail_pct); ?>,<?php echo round($this->item->ut_error_pct); ?>
&amp;cht=p3
&amp;chco=5AA426,E52626,444444
&amp;chl=Passes|Failures|Errors"
alt="Unit Test Report"  />
</div>
<table class="unit-test-report">
	<thead>
		<tr>
			<th>
				<a title="The number of tests run.">
					Tests</a>
			</th>
			<th>
				<a title="The number of comparisions between results and specifications (some tests may have multiple assertions) Failure on one assertion results in failure of the test as a whole.">
					Assertions</a>
			</th>
			<th>
				<a title="The number of tests passed (instances in which specifications were met).">
					Passes</a>
			</th>
			<th>
				<a title="The number of failures  (instances in which the specifications were not met).">
					Failures</a>
			</th>
			<th>
				<a title="The number of error messages (which may or may not indicate a failure).">
					Errors</a>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $this->item->ut_tests; ?></td>
			<td><?php echo $this->item->ut_assertions; ?></td>
			<td><?php echo $this->item->ut_tests - $this->item->ut_failures - $this->item->ut_errors; ?></td>
			<td><?php echo $this->item->ut_failures; ?></td>
			<td><?php echo $this->item->ut_errors; ?></td>
		</tr>
	</tbody>
</table>


<?php if (!empty($this->item->ut_delta['-'])) : ?>
	<h3>
		Fixed tests since the last build.
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
		Broken tests since the last build.
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
<div class="clr"></div>
