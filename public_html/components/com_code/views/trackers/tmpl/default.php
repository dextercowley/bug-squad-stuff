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
JHtml::script('status.js', 'components/com_code/media/js/');

// Load the CSS stylesheets.
JHtml::stylesheet('default.css', 'components/com_code/media/css/');
?>

<h1>
	<?php echo $this->escape($this->params->get('page_title', JText::_('COM_CODE_TRACKERS'))); ?>
</h1>

<h2>
	<?php echo JText::_('COM_CODE_TRACKERS'); ?>
</h2>
<?php foreach ($this->items as $tracker) : ?>
<div class="trackers branch-<?php echo $tracker->tracker_id; ?>">
	<h3>
		<a href="<?php echo JRoute::_('index.php?option=com_code&view=tracker&tracker_alias='.$tracker->alias); ?>" title="View the <?php echo $tracker->title; ?> tracker.">
			<?php echo $tracker->title; ?></a>
	</h3>
</div>
<div class="clr"></div>
<?php endforeach; ?>
