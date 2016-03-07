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

// Create some shortcuts.
$params    = &$this->item->params;
$n         = count($this->items);
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

?>
<div class="session-list<?php echo $this->pageclass_sfx;?>">
	<div>
		<div class="<?php echo $className .'-session' . $this->pageclass_sfx;?>">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<h1>
					<?php echo $this->escape($this->params->get('page_heading')); ?>
				</h1>
			<?php endif; ?>

			<?php if (empty($this->items)) : ?>
				<p><?php echo "No sessions"; ?></p>
			<?php else : ?>
				<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
					<fieldset class="filters btn-toolbar clearfix">
						<?php if ($this->params->get('filter_field') != 'hide') :?>
							<div class="btn-group">
								<label class="filter-search-lbl element-invisible" for="filter-search">
									<?php echo JText::_('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL') . '&#160;'; ?>
								</label>
								<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL'); ?>" />
							</div>
						<?php endif; ?>
						<?php if ($this->params->get('show_pagination_limit')) : ?>
							<div class="btn-group pull-right">
								<label for="limit" class="element-invisible">
									<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
								</label>
								<?php echo $this->pagination->getLimitBox(); ?>
							</div>
						<?php endif; ?>

						<input type="hidden" name="filter_order" value="" />
						<input type="hidden" name="filter_order_Dir" value="" />
						<input type="hidden" name="limitstart" value="" />
						<input type="hidden" name="task" value="" />
					</fieldset>


					<table class="sessions table table-striped table-bordered table-hover">
						<?php
						$headerName    			= '';
						$headerDate    			= '';
						$headerLength	     	= '';
						$headerStatus   		= '';
						?>
						<!--<?php //if ($this->params->get('show_headings')) : ?>-->
							<?php
							$headerCourse   = 'headers="sessionslist_header_name"';
							$headerDate   = 'headers="sessionslist_header_date"';
							$headerLength   = 'headers="sessionslist_header_length"';
							$headerStatus   = 'headers="sessionslist_header_status"';
							?>
						<thead>
							<tr>
								<th id="sessionslist_header_name">
									<?php echo JHtml::_('grid.sort', 'COM_MOODLE_SESSION_NAME', 'name', $listDirn, $listOrder); ?>
								</th>
								<?php foreach ($this->items[0]->fields as $i => $field) : ?>
									<th id="sessionslist_header_<?php echo $field['shortname']; ?>">
										<?php echo JHtml::_('grid.sort', 'COM_MOODLE_SESSION_'.strtoupper($field['shortname']), 'field.'.$field['shortname'], $listDirn, $listOrder); ?>
									</th>
								<?php endforeach; ?>
								<th id="sessionslist_header_date">
									<?php echo JHtml::_('grid.sort', 'COM_MOODLE_SESSION_DATE', 'date', $listDirn, $listOrder); ?>
								</th>
								<th id="sessionslist_header_length">
									<?php echo JHtml::_('grid.sort', 'COM_MOODLE_SESSION_LENGTH', 'length', $listDirn, $listOrder); ?>
								</th>
								<th id="sessionslist_header_status">
									<?php echo JHtml::_('grid.sort', 'COM_MOODLE_SESSION_STATUS', 'a.status', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<!--<?php //endif; ?>-->
						<tbody>

							<?php foreach ($this->items as $i => $session) : ?>
								<tr class="sessions-list-row<?php echo $i % 2; ?>" >
									<td <?php echo $headerName; ?> class="list-name">
										<a href="#">
											<?php echo $this->escape($session->name); ?>
										</a>
									</td>
									<?php foreach ($session->fields as $i => $field) : ?>
									<td <?php echo 'headers="sessionslist_header_'.$field['shortname'] ?> class="list-<?php echo $field['shortname']; ?>">
										<?php echo $this->escape($field['data']); ?>
									</td>
									<?php endforeach; ?>
									<td <?php echo $headerDate; ?> class="list-date">
										<?php
										echo JHtml::_(
											'date', $session->date,
											$this->escape($this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))
										); ?>
									</td>

									<td <?php echo $headerLength; ?> class="list-length">
										<?php echo $session->length . ' day(s)'; ?>
									</td>
									<td <?php echo $headerStatus; ?> class="list-status">
										<?php echo $session->id ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<?php // Add pagination links ?>
				<?php if (!empty($this->items)) : ?>
					<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
					<div class="pagination">

						<?php if ($this->params->def('show_pagination_results', 1)) : ?>
							<p class="counter pull-right">
								<?php echo $this->pagination->getPagesCounter(); ?>
							</p>
						<?php endif; ?>

						<?php echo $this->pagination->getPagesLinks(); ?>
					</div>
					<?php endif; ?>




				</form>
			<?php endif; ?>
		</div>
	</div>
</div>