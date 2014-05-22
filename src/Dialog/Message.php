<?php
/**
* The Message Dialog class represents a message dialog.
*
* It is used by the DialogBuilder class to create dialogs. base_msgdialog doesn't need to
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
* @version $Id: Message.php 5 2014-02-13 15:37:31Z SystemVCS $
*/

/**
* Include papaya base dialog class.
*/
require_once(PAPAYA_INCLUDE_PATH.'system/base_msgdialog.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireDialogMessage extends base_msgdialog {

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
  * @param array $hidden
  * @param string $msg
  * @param string $type
  * @return base_dialog
  */
  public function createDialog($owner, $paramName, $hidden, $msg, $type) {
    parent::__construct($owner, $paramName, $hidden, $msg, $type);
    return $this;
  }

}
