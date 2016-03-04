<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_moodle
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="session-list<?php echo $this->pageclass_sfx;?>">
	<div>
		<div class="<?php echo $className .'-session' . $this->pageclass_sfx;?>">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<h1>
					<?php echo $this->escape($this->params->get('page_heading')); ?>
				</h1>
			<?php endif; ?>
		</div>
	</div>
</div>