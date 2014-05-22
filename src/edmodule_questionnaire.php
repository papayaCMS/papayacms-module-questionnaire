<?php
/**
* Questionnaire administration module wrapper (edmodule)
*
* @copyright 2002-2010 by papaya Software GmbH - All rights reserved.
* @link http://www.papaya-cms.com/
* @license papaya Commercial License (PCL)
*
* Redistribution of this script or derivated works is strongly prohibited!
* The Software is protected by copyright and other intellectual property
* laws and treaties. papaya owns the title, copyright, and other intellectual
* property rights in the Software. The Software is licensed, not sold.
*
* @version $Id: edmodule_questionnaire.php 8 2014-02-20 12:06:02Z SystemVCS $
* @package Commercial
* @subpackage Questionnaire
*/

/**
* Basic module class
*/
require_once(PAPAYA_INCLUDE_PATH.'system/base_module.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class edmodule_questionnaire extends base_module {

  /**
  * Module permissions
  * @var array
  */
  public $permissions = array(
    1 => 'Manage',
  );

  /**
  * Execute module
  *
  * @access public
  */
  public function execModule() {
    if ($this->hasPerm(1, TRUE)) {
      $adminObject = $this->getAdministrationObject();

      $adminObject->setConfiguration($this->papaya()->options);
      $adminObject->module = $this;
      $adminObject->images = $this->images;
      $adminObject->layout = $this->layout;
      $adminObject->authUser = $this->authUser;
      $adminObject->initialize();
      $adminObject->execute();
      $adminObject->getXML();
    }
  }

  /**
  * This method initializes the administration object
  * @return PapayaQuestionnaireAdministration
  */
  public function getAdministrationObject() {
    include_once(dirname(__FILE__).'/Administration.php');
    return new PapayaQuestionnaireAdministration;
  }

}
?>
