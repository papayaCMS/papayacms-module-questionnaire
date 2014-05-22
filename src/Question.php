<?php
/**
* This interface defines a question type to be used with the questionnaire module.
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
* @version $Id: Question.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* @package Commercial
* @subpackage Questionnaire
*/
interface PapayaQuestionnaireQuestion {

  public function setId($id);

  public function getId();

  public function setGroupId($groupId);

  public function getGroupId();

  public function setText($text);

  public function getText();

  public function setType($type);

  public function getType();

  /**
  * This method creates an instance of base_dialog to configure the question with.
  * @return base_dialog
  */
  public function getConfigurationDialog();

  public function getDialogData();

  public function getQuestionConfiguration();


  public function getAnswers();

  /**
  * This method returns the XML necessary to render the questions, i.e. the dialog xml that
  * incorporates the configured answer data.
  *
  * @param integer $questionId
  * @param integer $selectedAnswer
  * @return string
  */
  public function getQuestionXML($questionId, $selectedAnswer = FALSE);

}

?>
