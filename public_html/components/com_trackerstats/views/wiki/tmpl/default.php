<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_trackerstats
 * @copyright	Copyright (C) 2011 Mark Dexter and Louis Landry. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

?>

<div class="trackerstats-wiki<?php echo $this->pageclass_sfx;?>">

<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>

<?php echo $this->loadTemplate('charts'); ?>

</div>
