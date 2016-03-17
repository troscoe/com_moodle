<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_moodle
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JFormHelper::loadFieldClass('list');
 
/**
 * HelloWorld Form Field class for the HelloWorld component
 *
 * @since  0.0.1
 */
class JFormFieldRegion extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var         string
	 */
	protected $type = 'Region';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array  An array of JHtml options.
	 */
	public function getOptions()
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('possiblevalues');
		$query->from('#__moodle_facetoface_session_field AS a');
		$query->where('shortname = "region"');

		$db->setQuery((string) $query);
		$regions = $db->loadObjectList();

		if ($regions)
		{
			foreach ($regions as $region)
			{
				$pieces = explode("##SEPARATOR##", $region->possiblevalues);
				foreach ($pieces as $piece) {
					$object = new StdClass;
					$object->value = $piece;
					$object->text = $piece;
					$options[] = $object;
				}
			}
		}
 
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
 
		return $options;
	}
}