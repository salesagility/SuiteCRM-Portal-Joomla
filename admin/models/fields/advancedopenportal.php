<?php
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');
 
/**
 * AdvancedOpenPortal Form Field class for the AdvancedOpenPortal component
 */
class JFormFieldAdvancedOpenPortal extends ListField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Advancedopenportal';
 
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id,sugar_url,sugar_user,sugar_pass');
		$query->from('#__advancedopenportal');
		$db->setQuery((string)$query);
		$sagilitysugars = $db->loadObjectList();
		$options = [];
		if ($sagilitysugars)
		{
			foreach($sagilitysugars as $sagilitysugar)
			{
				$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $sagilitysugar->id, $sagilitysugar->sugar_url, $sagilitysugar->sugar_user,$sagilitysugar->sugar_pass);
			}
		}
        $options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
