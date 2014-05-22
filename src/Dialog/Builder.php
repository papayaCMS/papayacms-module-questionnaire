<?php
/**
* The Dialogs class provides means of creating dialogs without using base_dialog and
* base_msgdialog directly.
*
* This makes it possible to test the dialog creation.
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
* @version $Id: Builder.php 5 2014-02-13 15:37:31Z SystemVCS $
*/

/**
* TODO use base_dialog or base_msgdialog directly and remove this
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireDialogBuilder {

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
    include_once(dirname(__FILE__).'/Basic.php');
    $dialog = new PapayaQuestionnaireDialogBasic;
    return $dialog->createDialog($owner, $paramName, $fields, $data, $hidden);
  }

  /**
  *
  * @param object $owner
  * @param string $paramName
  * @param array $hidden
  * @param string $msg
  * @param string $type
  * @return base_msgdialog
  */
  public function createMsgDialog($owner, $paramName, $hidden, $msg, $type) {
    include_once(dirname(__FILE__).'/Message.php');
    $dialog = new PapayaQuestionnaireDialogMessage;
    return $dialog->createDialog($owner, $paramName, $hidden, $msg, $type);
  }

}

