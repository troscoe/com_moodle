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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Moodle Component
 *
 * @since  0.0.1
 */
class MoodleViewSessions extends JViewLegacy
{
	/**
	 * Display the sessions view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$params = $app->getParams();
		$this->assignRef('params', $params);

		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
 
			return false;
		}

		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->prepareDocument();

		parent::display($tpl);
    }

    /**
	 * Method to prepares the document
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function prepareDocument()
	{
        $app           = JFactory::getApplication();
		$menus         = $app->getMenu();
		$this->pathway = $app->getPathway();
		$title         = null;
		// Because the application sets a default page title, we need to get it from the menu item itself
		$this->menu = $menus->getActive();

		if ($this->menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $this->menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_MOODLE_SESSIONS'));
		}
	}
}