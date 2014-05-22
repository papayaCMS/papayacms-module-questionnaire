<?php
/**
* The Basic Dialog class represents a dialog.
*
* It is used by the DialogBuilder class to create dialogs. base_dialog doesn't need to
* be used directly. Thus, the dialog creation can be tested.
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
* @package Commercial
* @subpackage Questionnaire
* @version $Id: Basic.php 5 2014-02-13 15:37:31Z SystemVCS $
*/

/**
* Include papaya base dialog class.
*/
require_once(PAPAYA_INCLUDE_PATH.'system/base_dialog.php');

/**
* TODO use base_dialog or base_msgdialog directly and remove this
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireDialogBasic extends base_dialog {

  /**
  * This method overloads the constructor in order to enable the class to be tested.
  */
  public function __construct() {
  }

  /**
  * This method creates a dialog
  *
  * @param object $owner
  * @param string $paramName
  * @param array $fields
  * @param array $data
  * @param array $hidden
  * @return base_dialog
  */
  public function createDialog($owner, $paramName, $fields, $data, $hidden) {
    parent::__construct($owner, $paramName, $fields, $data, $hidden);
    return $this;
  }

}

