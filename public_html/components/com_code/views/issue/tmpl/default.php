<?php
/**
 * @version		$Id: default.php 430 2010-06-25 23:27:52Z louis $
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
	<?php //echo $this->item->title; ?>
</h1>
<p>
	<a href="<?php echo JRoute::_('index.php?option=com_code&view=summary'); ?>">
		View Project Summary &raquo;</a>

	<a href="http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&amp;tracker_item_id=<?php //echo $this->item->jc_issue_id; ?>">
		View on JoomlaCode.org &raquo;</a>
</p>

<pre class="description" style="white-space: pre-line;"><?php //echo $this->item->description; ?></pre>
<div class="clr"></div>

<?php if (!empty($this->tags)) : ?>
<span>Filed Under:</span>
<ul>
<?php foreach ($this->tags as $tag) : ?>
	<li><?php echo $tag->tag; ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<div class="clr"></div>
