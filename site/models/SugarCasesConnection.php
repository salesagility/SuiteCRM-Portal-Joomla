<?php
/**
 *
 * @package Advanced OpenPortal
 * @copyright SalesAgility Ltd http://www.salesagility.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program; if not, see http://www.gnu.org/licenses
 * or write to the Free Software Foundation,Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301  USA
 *
 * @author Salesagility Ltd <support@salesagility.com>
 */
include_once 'components/com_advancedopenportal/SugarRestClient.php';
include_once 'components/com_advancedopenportal/models/SugarContact.php';
include_once 'components/com_advancedopenportal/models/advancedopenportal.php';


class SugarCasesConnection {

    /**
     * @return array
     */
    private static function getCaseFields(){
        return JFactory::getCache()->call(array(SugarRestClient::getInstance(),'getApplicationLanguage'),'ApplicationLanguage');
    }

    /**
     * @return array
     */
    public static function getTypes(){
        if (!self::isAllowedType()){
            return array();
        }
        $fields = self::getCaseFields();
        return $fields['case_type_dom'];
    }

    /**
     * @return array
     */
    public static function getPriorities(){
        if (!self::isAllowedPriority()){
            return array();
        }
        $fields = self::getCaseFields();
        return $fields['case_priority_dom'];
    }

    /**
     * @return array
     */
    public static function getStatuses(){
        $fields = self::getCaseFields();
        return $fields['case_status_dom'];
    }

    /**
     * @return array
     */
    public static function getStates(){
        $fields = self::getCaseFields();
        return $fields['case_state_dom'];
    }

    /**
     * @return bool
     */
    public static function currentUserIsValidPortalUser(){
        $user =& JFactory::getUser();
        return !empty($user->id) && $user->getParam("sugarid");
    }

    /**
     * @return bool
     */
    public static function currentUserIsBlocked(){
        $user =& JFactory::getUser();
        return $user->getParam("aop_block");
    }

    /**
     * @return bool
     */
    public static function isAllowedClosing(){
        return AdvancedOpenPortalModelAdvancedOpenPortal::getSettings()->allow_case_closing;
    }

    /**
     * @return bool
     */
    public static function isAllowedReopening(){
        return AdvancedOpenPortalModelAdvancedOpenPortal::getSettings()->allow_case_reopen;
    }

    /**
     * @return bool
     */
    public static function isAllowedPriority(){
        return AdvancedOpenPortalModelAdvancedOpenPortal::getSettings()->allow_priority;
    }

    /**
     * @return bool
     */
    public static function isAllowedType(){
        return AdvancedOpenPortalModelAdvancedOpenPortal::getSettings()->allow_type;
    }

    /**
     * @return string
     */
    public static function currentSugarContactId()
    {
        return JFactory::getUser()->getParam("sugarid");
    }

    /**
     * @return SugarObject
     */
    public static function currentSugarContact()
    {
        return SugarContact::fromID(self::currentSugarContactId());
    }
}