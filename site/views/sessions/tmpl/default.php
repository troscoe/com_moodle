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
		<div class="<?php echo 'page-header ' . $className .'-session' . $this->pageclass_sfx;?>">
			<?php if ($this->params->get('show_page_heading')) : ?>
				<h2>
					<?php echo $this->escape($this->params->get('page_heading')); ?>
				</h2>
			<?php endif; ?>

			<?php if (empty($this->items)) : ?>
				<p><?php echo "No sessions"; ?></p>
			<?php else : ?>
				<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
					<fieldset class="filters btn-toolbar clearfix">
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
							</tr>
						</thead>
						<!--<?php //endif; ?>-->
						<tbody>
							<?php foreach ($this->items as $i => $session) : ?>
								<tr class="sessions-list-row<?php echo $i % 2; ?>" >
									<td <?php echo $headerName; ?> class="list-name">
										<?php

										$cat_id = $session->cat_id;
									    $cat_name = $session->cat_name;
									    $course_id = $session->course_id;

										$cat_slug = JFilterOutput::stringURLSafe ($session->cat_name);
									    $course_slug = JFilterOutput::stringURLSafe ($session->course_name);

									    $url = JRoute::_("index.php?option=com_joomdle&view=detail&cat_id=$cat_id:$cat_slug&course_id=$course_id:$course_slug&Itemid=$itemid&session_id=$session->id");
										?>
										<a href="<?php echo $url; ?>">
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