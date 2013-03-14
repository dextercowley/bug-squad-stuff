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
JHtml::core();

// Get the user object.
$user = JFactory::getUser();
// Check if user is allowed to add/edit based on trackerstats permissions.
$canEdit = $user->authorise('core.edit', 'com_trackerstats');

$listOrder	= '';
$listDirn	= '';
$listFilter = '';
?>

<?php if (empty($this->items) /*&& ($listFilter == '')*/) : ?>
	<p> <?php echo JText::_('COM_TRACKERSTATS_NO_STATS'); ?></p>
<?php else : ?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>"
	method="post" name="adminForm" id="adminForm">
	<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
	<div class="filter-search">
		<label class="filter-search-lbl" for="filter-search">
		<?php echo JText::_('COM_TRACKERSTATS_FILTER_LABEL').'&#160;'; ?></label>
		<input type="text" name="filter-search" id="filter-search"
			value="<?php echo $this->escape($this->state->get('list.filter')); ?>"
			class="inputbox" onchange="document.adminForm.submit();"
			title="<?php echo JText::_('COM_TRACKERSTATS_FILTER_SEARCH_DESC'); ?>" />
	</div>
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	</fieldset>

	<table id="chart">
		<thead><tr>
			<th>Total</th>
		</tr></thead>
	<tbody>
	<?php foreach ($this->items as $i => $item) : ?>
		<tr>
		<td>
			<?php echo $item->total_points; ?>
		</td>
		</tr>
	<?php endforeach; ?>
</tbody>
<tfoot>
        <tr>
        <?php foreach ($this->items as $i => $item) : ?>
            <td><?php echo $item->name;?></td>
         <?php endforeach; ?>
        </tr>
</tfoot>
</table>
<div class="pagination">
	<p class="counter">
	<?php echo $this->pagination->getPagesCounter(); ?>
	</p>
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<div>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</div>
</form>
<?php endif; ?>