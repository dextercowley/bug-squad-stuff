<?php
/**
 * @version		$Id: default.php 414 2010-06-24 00:43:39Z louis $
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

<div id= "tracker">
<h1>
	<?php echo $this->item->title; ?> Tracker
</h1>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_code&view=summary'); ?>">
		View Project Summary &raquo;</a>
</p>

<p class="description">
	<?php echo $this->item->description; ?>
</p>
<div class="clr"></div>

<div id="latest-issues">
	<h2>
		Latest Issues
	</h2>
	<table width="100%" cellpadding="4px">
		<thead>
			<tr>
				<th>
					<?php echo JText::_('Title'); ?>
				</th>
				<th>
					<?php echo JText::_('Priority'); ?>
				</th>
				<th>
					<?php echo JText::_('Created'); ?>
				</th>
				<th>
					<?php echo JText::_('Modified'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
<?php foreach ($this->items as $i => $issue) : ?>
			<tr class="<?php echo 'row',($i%2);?>" title="<?php echo $this->escape($issue->title); ?>">
				<td width="50%">
					<a href="<?php echo JRoute::_('index.php?option=com_code&view=issue&tracker_alias='.$this->item->alias.'&issue_id='.$issue->issue_id); ?>" title="View issue <?php echo $issue->issue_id; ?> report.">
						<?php echo $issue->title; ?></a>
				</td>
				<td>
					<?php echo $issue->priority; ?>
				</td>
				<td>
					<?php echo JHtml::_('date', $issue->created_date, 'j M Y, G:s'); ?>
					<br />
					by <?php echo $issue->created_user_name; ?>
				</td>
				<td>
					<?php echo JHtml::_('date', $issue->modified_date, 'j M Y, G:s'); ?>
					<br />
					by <?php echo $issue->modified_user_name; ?>
				</td>
			</tr>
<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<?php echo $this->page->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>

</div>
<div class="clr"></div>
