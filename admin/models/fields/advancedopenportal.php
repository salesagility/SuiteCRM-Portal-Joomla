<?php
// No direct access to this file
defined('_JEXEC') or die;

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * AdvancedOpenPortal Form Field class for the AdvancedOpenPortal component
 */
class JFormFieldAdvancedOpenPortal extends JFormFieldList
{
    /**
     * The field type.
     *
     * @var    string
     */
    protected $type = 'Advancedopenportal';

    /**
     * Method to get a list of options for a list input.
     *
     * @return  array    An array of JHtml options.
     */
    protected function getOptions()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id,sugar_url,sugar_user,client_idclient_secret');
        $query->from('#__advancedopenportal');
        $db->setQuery((string)$query);
        $sagilitysugars = $db->loadObjectList();
        $options = [];
        if ($sagilitysugars) {
            foreach ($sagilitysugars as $sagilitysugar) {
                $options[] = JHtml::_(
                    'select.option',
                    $sagilitysugar->id,
                    $sagilitysugar->sugar_url,
                    $sagilitysugar->sugar_user,
                    $sagilitysugar->sugar_pass,
                    $sagilitysugar->client_id,
                    $sagilitysugar->client_secret
                );
            }
        }
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
