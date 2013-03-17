<?php
/**
 * @version		$Id: default.php 272 2011-08-11 00:32:05Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	com_trackerstats
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>
<div class="trackerstats-dashboard<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>


<?php echo $this->loadTemplate('charts'); ?>

</div>
