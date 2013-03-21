<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

// Create some shortcuts.
$params		= &$this->item->params;
$n			= count($this->items);
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" >

	<fieldset class="filters">

		<div class="filter-search">
			<label class="filter-search-lbl" for="filter-search"><?php echo JText::_('COM_TRACKERSTATS_RELEASENOTES_FILTER_TITLE').'&#160;'; ?></label>
			<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_TRACKERSTATS_RELEASENOTES_FILTER_TITLE'); ?>" />
		</div>

		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>

		<input type="hidden" name="filter_order" value="" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="limitstart" value="" />
	</fieldset>

	<table class="category">
		<thead>
			<tr>
				<th class="list-title" >
					<?php  echo Jtext::_('COM_TRACKERSTATS_RELEASENOTES_CATEGORY'); ?>
				</th>
				<th class="list-title" >
					<?php  echo Jtext::_('COM_TRACKERSTATS_RELEASENOTES_ISSUE'); ?>
				</th>
				<th class="list-title" >
					<?php  echo Jtext::_('COM_TRACKERSTATS_RELEASENOTES_TITLE'); ?>
				</th>
			</tr>
		</thead>

		<tbody>

		<?php foreach ($this->items as $i => $note) : ?>
				<tr class="cat-list-row<?php echo $i % 2; ?>" >
					<td class="list-title">
						<?php echo $note->category;?>
					</td>
					<td>
						<a href="http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=<?php echo $note->jc_issue_id; ?>">
							<?php echo $this->escape($note->jc_issue_id); ?></a>
					</td>
					<td class="list-title">
						<?php echo $note->title;?>
					</td>

				</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php // Add pagination links ?>

	<div class="pagination">
		 	<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

</form>
