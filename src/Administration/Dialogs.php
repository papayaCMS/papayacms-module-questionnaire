<?php
/**
* The Dialogs class provides means of creating administration dialogs.
*
* It is intended to separate the dialogs from the business logic in the Administration class.
* If you need another dialog, extend this class. Write a method that generates the dialog
* using the DialogCreator in order to keep it testable.
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
* @version $Id: Dialogs.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* Include base papaya object class.
*/
require_once(PAPAYA_INCLUDE_PATH.'system/sys_base_object.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireAdministrationDialogs extends base_object {

  private $_configuration = NULL;

  /**
  * Holds the question creator instance
  * @var PapayaQuestionnaireQuestionCreator
  */
  private $_questionCreator;

  /**
  * Holds the dialog builder instance
  * @var PapayaQuestionnaireDialogBuilder
  */
  private $_dialogBuilder;

  /**
  * This method sets the submitted parameters
  *
  * @param array $parameters
  */
  public function setParameters($parameters) {
    $this->params = $parameters;
  }

  /**
  * This method sets the parameter name
  *
  * @param string $paramName
  */
  public function setParamName($paramName) {
    $this->paramName = $paramName;
  }

  /**
  * This method sets the configuration object
  * @param PapayaConfiguration $configuration
  */
  public function setConfiguration($configuration) {
    if ($configuration instanceof base_options ||
        $configuration instanceof PapayaConfiguration) {
      $this->_configuration = $configuration;
    }
  }

  /**
  * This method sets the available question types
  *
  * @param array $questionTypes
  * @return boolean
  */
  public function setQuestionTypes($questionTypes) {
    $result = FALSE;
    if (is_array($questionTypes)) {
      $this->_questionTypes = $questionTypes;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method sets the question creator object
  * @param PapayaQuestionnaireQuestionCreator $questionCreator
  * @return boolean
  */
  public function setQuestionCreator($questionCreator) {
    $result = FALSE;
    if ($questionCreator instanceof PapayaQuestionnaireQuestionCreator) {
      $this->_questionCreator = $questionCreator;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method retrieves the question creator object and initializes it if necessary
  * @return PapayaQuestionnaireQuestionCreator
  */
  public function getQuestionCreator() {
    if (!isset($this->_questionCreator) ||
        !$this->_questionCreator instanceof PapayaQuestionnaireQuestionCreator) {
      include_once(dirname(__FILE__).'/../Question/Creator.php');
      $creator = new PapayaQuestionnaireQuestionCreator;
      $creator->setConfiguration($this->_configuration);
      $this->setQuestionCreator($creator);
    }
    return $this->_questionCreator;
  }

  /**
  * This method sets the dialog builder
  *
  * @param PapayaQuestionnaireDialogBuilder $dialogBuilder
  * @return boolean
  */
  public function setDialogBuilder($dialogBuilder) {
    $result = FALSE;
    if ($dialogBuilder instanceof PapayaQuestionnaireDialogBuilder) {
      $this->_dialogBuilder = $dialogBuilder;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method retrieves the dialog builder and initializes it if necessary.
  * @return PapayaQuestionnaireDialogBuilder
  */
  public function getDialogBuilder() {
    if (!isset($this->_dialogBuilder) ||
        !$this->_dialogBuilder instanceof PapayaQuestionnaireDialogBuilder) {
      include_once(dirname(__FILE__).'/../Dialog/Builder.php');
      $this->setDialogBuilder(new PapayaQuestionnaireDialogBuilder);
    }
    return $this->_dialogBuilder;
  }

  /**
  * This method genereates a dialog to add a pool
  *
  * @return base_dialog
  */
  public function getPoolDialog($data = array()) {
    $hidden = array(
      'cmd' => $this->params['cmd'],
      'submit' => 1,
    );
    $fields = array(
      'question_pool_name' => array('Pool name', 'isNoHTML', TRUE, 'input', 200, '', ''),
      'question_pool_identifier' => array('Pool identifier', 'isNoHTML', FALSE, 'input', 8, '', ''),
    );
    $dialogBuilder = $this->getDialogBuilder();
    return $dialogBuilder->createDialog($this, $this->paramName, $fields, $data, $hidden);
  }


  /**
  * This method genereates a dialog to confirm deleteion of a pool
  *
  * @return base_dialog
  */
  public function getDeletePoolDialog() {
    $hidden = array(
      'cmd' => $this->params['cmd'],
      'question_pool_id' => $this->params['question_pool_id'],
      'submit' => 1
    );
    $msg = sprintf(
      $this->_gt('Do you really want to delete pool #%d including groups and questions?'),
      $this->params['question_pool_id']);

    $dialogBuilder = $this->getDialogBuilder();
    return $dialogBuilder->createMsgDialog($this, $this->paramName, $hidden, $msg, 'warning');
  }


  /**
  * This method genereates a dialog to add a group
  *
  * @return base_dialog
  */
  public function getGroupDialog($data = array()) {
    $hidden = array(
      'cmd' => $this->params['cmd'],
      'question_pool_id' => $this->params['question_pool_id'],
      'submit' => 1,
    );
    if (isset($data['question_group_id']) && $data['question_group_id'] > 0) {
      $hidden['question_group_id'] = $data['question_group_id'];
    }
    $fields = array(
      'question_group_name' => array('Group name', 'isNoHTML', TRUE, 'input', 250, '', ''),
      'question_group_subtitle' => array('Group subtitle', 'isNoHTML', TRUE, 'input', 250, '', ''),
      'question_group_identifier' =>
        array('Group identifier', 'isNoHTML', TRUE, 'input', 8, '', ''),
      'question_group_text' =>
        array('Group text', 'isSomeText', FALSE, 'richtext', 10, '', ''),
      'question_group_min_answers' =>
        array('Minimum answers', 'isNum', FALSE, 'input', 10, '', 0),
    );

    $dialogBuilder = $this->getDialogBuilder();
    $dialog = $dialogBuilder->createDialog($this, $this->paramName, $fields, $data, $hidden);
    return $dialog;
  }

  /**
  * This method genereates a dialog to confirm deleteion of a group
  *
  * @return base_dialog
  */
  public function getDeleteGroupDialog() {
    $hidden = array(
      'cmd' => $this->params['cmd'],
      'question_group_id' => $this->params['question_group_id'],
      'submit' => 1
    );
    $msg = sprintf(
      $this->_gt('Do you really want to delete group #%d and its questions?'),
      $this->params['question_group_id']);

    $dialogBuilder = $this->getDialogBuilder();
    return $dialogBuilder->createMsgDialog($this, $this->paramName, $hidden, $msg, 'warning');
  }

  /**
  * This method genereates a dialog to add a question
  *
  * @return PapayaQuestionnaireQuestion
  */
  public function getQuestionDialog(PapayaQuestionnaireQuestion $question = NULL) {
    $dialog = FALSE;
    if (isset($this->params['step']) && $this->params['step'] == 1 && $question) {
      $question->setParamName($this->paramName);
      $question->addHiddenParameter('submit', 1);
      $question->addHiddenParameter('cmd', $this->params['cmd']);
      $question->addHiddenParameter('question_group_id', $this->params['question_group_id']);
      $question->addHiddenParameter('question_text', $this->params['question_text']);
      $question->addHiddenParameter('question_type', $this->params['question_type']);
      $question->addHiddenParameter('question_identifier', $this->params['question_identifier']);
      $question->addHiddenParameter('question_id', $question->getId());
      $dialog = $question->getConfigurationDialog();
    } else {
      $hidden = array(
        'cmd' => $this->params['cmd'],
        'question_group_id' => $this->params['question_group_id'],
        'step' => 1,
      );
      $exists = FALSE;
      if ($question && $question->getId() > 0) {
        $exists = TRUE;
        $hidden['question_id'] = $question->getId();
      }

      $text = ($exists) ? $question->getText() : '';
      $identifier = ($exists) ? $question->getIdentifier() : '';
      $type = ($exists) ? $question->getType() : '';

      $fields = array(
        'question_text' => array(
          'Question',
          'isNoHTML',
          TRUE,
          'input',
          200,
          '',
          $text
        ),
        'question_identifier' => array(
          'Question identifier',
          'isNoHTML',
          TRUE,
          'input',
          8,
          '',
          $identifier
        ),
        'question_type' => array(
          'Question type',
          'isNoHTML',
          TRUE,
          'combo',
          $this->_questionTypes,
          '',
          $type
        ),
      );

      $dialogBuilder = $this->getDialogBuilder();
      $data = array();
      $dialog = $dialogBuilder->createDialog($this, $this->paramName, $fields, $data, $hidden);
      $dialog->buttonTitle = 'continue';
    }
    return $dialog;
  }

  /**
  * This method genereates a dialog to confirm deletion of a question
  *
  * @return base_dialog
  */
  public function getDeleteQuestionDialog() {
    $hidden = array(
      'cmd' => $this->params['cmd'],
      'question_id' => $this->params['question_id'],
      'submit' => 1
    );
    $msg = sprintf(
      $this->_gt('Do you really want to delete question #%d?'),
      $this->params['question_id']);

    $dialogBuilder = $this->getDialogBuilder();
    $dialog = $dialogBuilder->createMsgDialog($this, $this->paramName, $hidden, $msg, 'warning');
    $dialog->buttonTitle = 'Delete';
    return $dialog;
  }

}

?>
