<?php
/**
* The Administration class handles administration logic and triggers actions as well as
* requesting the output xml.
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
* @version $Id: Administration.php 9 2014-02-21 16:26:26Z SystemVCS $
*
* @tutorial Questionnaire/PapayaQuestionnaire.pkg
*/

/**
* Include base papaya object class.
*/
require_once(PAPAYA_INCLUDE_PATH.'system/sys_base_object.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireAdministration extends base_object {

  protected $_configuration;
  protected $_connector;
  protected $_pluginLoader;
  protected $_questionCreator;
  protected $_overview;
  protected $_response;

  /**
  * This method initializes the Administration object, i.e. the session paramters and
  * any necessary object that was not set from outside before.
  *
  * @return unknown_type
  */
  public function initialize() {
    $this->sessionParamName = 'PAPAYA_SESS_'.$this->paramName;
    $this->initializeParams();
    $this->sessionParams = $this->getSessionValue($this->sessionParamName);
    $this->initializeSessionParam('question_pool_id', array('question_id', 'question_group_id'));
    $this->initializeSessionParam('question_group_id', array('question_id'));

    if (!isset($this->params['cmd'])) {
      $this->params['cmd'] = '';
    }

    $this->setSessionValue($this->sessionParamName, $this->sessionParams);

    $this->_initializeConnector();
    $this->_initializeDialogObject();
  }

  /**
  * This method initializes the pluginloader
  */
  protected function _initializePluginLoader() {
    if (!isset($this->_pluginLoader)) {
      include_once(PAPAYA_INCLUDE_PATH.'system/base_pluginloader.php');
      $this->setPluginLoader(base_pluginloader::getInstance());
    }
  }

  /**
  * This method initializes the connector object
  */
  protected function _initializeConnector() {
    if (!isset($this->_connector)) {
      $this->_initializePluginLoader();
      $connector =
        $this->_pluginLoader->getPluginInstance('36d94a3fdaf122d8214776b34ffdb012', $this);
      $connector->setConfiguration($this->_configuration);
      $this->setConnector($connector);
    }
  }

  /**
  * This method initializes the dialogs object
  */
  protected function _initializeDialogObject() {
    if (!isset($this->_dialogs)) {
      include_once(dirname(__FILE__).'/Administration/Dialogs.php');
      $dialogObject = new PapayaQuestionnaireAdministrationDialogs;
      $dialogObject->setConfiguration($this->_configuration);
      $this->setDialogObject($dialogObject);
    }
  }

  /**
  * This method determines, which command is to be executed and does so.
  * @return boolean
  */
  public function execute() {
    $result = FALSE;
    switch ($this->params['cmd']) {
    case 'add_pool':
      $result = $this->_processAddPool();
      break;
    case 'copy_pool':
      $result = $this->_processCopyPool();
      break;
    case 'edit_pool':
      $result = $this->_processEditPool();
      break;
    case 'del_pool':
      $result = $this->_processDeletePool();
      break;
    case 'add_group':
      $result = $this->_processAddGroup();
      break;
    case 'edit_group':
      $result = $this->_processEditGroup();
      break;
    case 'del_group':
      $result = $this->_processDeleteGroup();
      break;
    case 'add_question':
      $result = $this->_processAddQuestion();
      break;
    case 'edit_question':
      $result = $this->_processEditQuestion();
      break;
    case 'del_question':
      $result = $this->_processDeleteQuestion();
      break;
    case 'group_up':
      $result = $this->_processMoveGroupUp();
      break;
    case 'group_down':
      $result = $this->_processMoveGroupDown();
      break;
    case 'question_up':
      $result = $this->_processMoveQuestionUp();
      break;
    case 'question_down':
      $result = $this->_processMoveQuestionDown();
      break;
    case 'migrate_answers':
      $this->_processMigrateAnswers();
      break;
    case 'overview':
      $this->_processOverview();
      break;
    case 'overview_extended':
      $this->_processOverview(TRUE);
      break;
    }
    return $result;
  }

  /* Pool related methods */

  /**
  * This method processes adding a pool. It triggers the creation or the dialog generation.
  * @return boolean
  */
  protected function _processAddPool() {
    $result = FALSE;
    if (isset($this->params['submit'])) {
      if ($poolId = $this->_connector->createPool($this->params)) {
        $this->addMsg(
          MSG_INFO,
          sprintf(
            $this->_gt('Added new pool "%s" (#%d).'),
            $this->params['question_pool_name'],
            $poolId
          )
        );
        $result = $poolId;
      } else {
        $this->addMsg(MSG_ERROR, $this->_gt('Failed to add pool.'));
      }
    } else {
      $this->_initializeDialogObject();
      if ($dialog = $this->_dialogs->getPoolDialog()) {
        $result = $this->layout->add($dialog->getDialogXML());
      }
    }
    return $result;
  }

  /**
  * This method processes editing an existing pool. It triggers the creation or the dialog
  * generation.
  *
  * @return boolean
  */
  protected function _processEditPool() {
    $result = FALSE;
    if (isset($this->params['question_pool_id']) &&
        $this->params['question_pool_id'] > 0) {
      if (isset($this->params['submit'])) {
        if ($this->_connector->updatePool($this->params['question_pool_id'], $this->params)) {
          $this->addMsg(
            MSG_INFO,
            sprintf(
              $this->_gt('Updated pool "%s" (#%d).'),
              $this->params['question_pool_name'],
              $this->params['question_pool_id']
            )
          );
          $result = TRUE;
        } else {
          $this->addMsg(MSG_ERROR, $this->_gt('Failed to update pool.'));
        }
      } else {
        $this->_initializeDialogObject();
        $pool = $this->_connector->getPool($this->params['question_pool_id']);
        if ($dialog = $this->_dialogs->getPoolDialog($pool)) {
          $result = $this->layout->add($dialog->getDialogXML());
        }
      }
    }
    return $result;
  }

  /**
  * This method processes copying a pool.
  * @return boolean
  */
  protected function _processCopyPool() {
    $result = FALSE;
    if (isset($this->params['question_pool_id'])) {
      if ($poolId = $this->_connector->copyPool($this->params['question_pool_id'])) {
        $this->addMsg(
          MSG_INFO,
          sprintf(
            $this->_gt('Pool #%d copied to #%d.'),
            $this->params['question_pool_id'],
            $poolId
          )
        );
        $result = $poolId;
      } else {
        $this->addMsg(MSG_ERROR, $this->_gt('Failed to copy pool.'));
      }
    }
    return $result;
  }

  /**
  * This method processes deleting a pool. It triggers the deletion or the confirmation
  * dialog generation.
  * @return boolean
  */
  protected function _processDeletePool() {
    $result = FALSE;
    if (isset($this->params['question_pool_id']) && $this->params['question_pool_id'] > 0) {
      if (isset($this->params['submit'])) {
        if ($this->_connector->deletePool($this->params['question_pool_id'])) {
          $this->addMsg(
            MSG_INFO,
            sprintf(
              $this->_gt('Pool #%d deleted.'),
              $this->params['question_pool_id']
            )
          );
          $result = TRUE;
        } else {
          $this->addMsg(MSG_ERROR, $this->_gt('Failed to delete pool.'));
        }
      } else {
        $this->_initializeDialogObject();
        if ($dialog = $this->_dialogs->getDeletePoolDialog()) {
          $result = $this->layout->add($dialog->getMsgDialog());
        }
      }
    }
    return $result;
  }

  /* Group related methods */

  /**
  * This method processes adding a group. It triggers the creation or the dialog generation.
  * @return boolean
  */
  protected function _processAddGroup() {
    $result = FALSE;
    if (!isset($this->params['question_pool_id'])) {
      $this->addMsg(MSG_ERROR, $this->_gt('Could not add group: no pool selected.'));
    } elseif (isset($this->params['submit'])) {
      if ($groupId = $this->_connector->createGroup($this->params)) {
        $this->addMsg(
          MSG_INFO,
          sprintf(
            $this->_gt('Added new group "%s" (#%d).'),
            $this->params['question_group_name'],
            $groupId
          )
        );
        $result = $groupId;
      } else {
        $this->addMsg(MSG_ERROR, $this->_gt('Failed to add group.'));
      }
    } else {
      $this->_initializeDialogObject();
      if ($dialog = $this->_dialogs->getGroupDialog()) {
        $this->layout->add($dialog->getDialogXML());
        $result = TRUE;
      }
    }
    return $result;
  }

  /**
  * This method processes editing an existing group.
  * @return boolean
  */
  protected function _processEditGroup() {
    $result = FALSE;
    if (!isset($this->params['question_group_id'])) {
      $this->addMsg(MSG_ERROR, $this->_gt('Could not edit group: no group selected.'));
    } elseif (isset($this->params['submit'])) {
      if ($this->_connector->updateGroup($this->params['question_group_id'], $this->params)) {
        $this->addMsg(
          MSG_INFO,
          sprintf(
            $this->_gt('Updated group "%s" (#%d).'),
            $this->params['question_group_name'],
            $this->params['question_group_id'])
        );
        $result = TRUE;
      }
    } else {
      $this->_initializeDialogObject();
      $group = $this->_connector->getGroup($this->params['question_group_id']);
      if ($dialog = $this->_dialogs->getGroupDialog($group)) {
        $this->layout->add($dialog->getDialogXML());
        $result = TRUE;
      }
    }
    return $result;
  }

  /**
  * This method processes deleting a group. It triggers the deletion or the confirmation
  * dialog generation.
  * @return boolean
  */
  protected function _processDeleteGroup() {
    $result = FALSE;
    if (isset($this->params['question_group_id']) && $this->params['question_group_id'] > 0) {
      if (isset($this->params['submit'])) {
        if ($this->_connector->deleteGroup($this->params['question_group_id'])) {
          $this->addMsg(
            MSG_INFO,
            sprintf(
              $this->_gt('Group #%d deleted.'),
              $this->params['question_group_id']
            )
          );
          $result = TRUE;
        } else {
          $this->addMsg(MSG_ERROR, $this->_gt('Failed to delete group.'));
        }
      } elseif ($dialog = $this->_dialogs->getDeleteGroupDialog()) {
        $this->layout->add($dialog->getMsgDialog());
      }
    }
    return $result;
  }

  /* Question related methods */

  /**
  * This method processes adding a question. It triggers the creation or the dialog generation.
  * @return boolean
  */
  protected function _processAddQuestion() {
    $result = FALSE;
    if (!isset($this->params['question_group_id'])) {
      $this->addMsg(MSG_ERROR, $this->_gt('Could not add question: no group selected.'));
    } elseif (isset($this->params['submit'])) {
      $question = $this->_getQuestionObject($this->params);
      if ($questionId = $this->_connector->createQuestion($question)) {
        $this->addMsg(
          MSG_INFO,
          sprintf(
            $this->_gt('Added new question "%s" (#%d).'),
            papaya_strings::truncate($this->params['question_text']),
            $questionId
          )
        );
        $result = $questionId;
      } else {
        $this->addMsg(MSG_ERROR, $this->_gt('Failed to add question.'));
      }
    } else {
      $questionCreator = $this->getQuestionCreator();
      $questionType = (isset($this->params['question_type']))
        ? $this->params['question_type']
        : NULL;
      $question = $questionCreator->getQuestionObject($questionType);
      if ($dialog = $this->_dialogs->getQuestionDialog($question)) {
        $this->layout->add($dialog->getDialogXML());
      }
    }
    return $result;
  }

  /**
  * This method processes editing an existing question.
  * @return boolean
  */
  protected function _processEditQuestion() {
    $result = FALSE;
    if (!isset($this->params['question_id'])) {
      $this->addMsg(MSG_ERROR, $this->_gt('Could not edit question: no question selected.'));
    } elseif (isset($this->params['submit'])) {
      $question = $this->_getQuestionObject($this->params);
      if ($this->_connector->updateQuestion($question)) {
        $this->addMsg(
          MSG_INFO,
          sprintf(
            $this->_gt('Updated question "%s" (#%d).'),
            $this->params['question_text'],
            $this->params['question_id'])
        );
        $result = TRUE;
      }
    } else {
      $this->_initializeDialogObject();
      $question = $this->_connector->getQuestion($this->params['question_id']);
      if ($dialog = $this->_dialogs->getQuestionDialog($question)) {
        $this->layout->add($dialog->getDialogXML());
        $result = TRUE;
      }
    }
    return $result;
  }

  protected function _processMigrateAnswers() {
    $converter = $this->getConverterObject();
    $converter->convertXmlAnswerStructure();
    $this->addMsg(MSG_INFO, 'Conversion of answers finished.');
  }

  public function setConverterObject(PapayaQuestionnaireStorageDatabaseConverter $converter) {
    $this->_converter = $converter;
  }

  public function getConverterObject() {
    if (empty($this->_converter)) {
      include_once(dirname(__FILE__).'/Storage/Database/Converter.php');
      $converter = new PapayaQuestionnaireStorageDatabaseConverter();
      $converter->setConfiguration($this->papaya()->options);
      $this->setConverterObject($converter);
    }
    return $this->_converter;
  }

  protected function _getQuestionObject($params) {
    $questionCreator = $this->getQuestionCreator();
    $questionObject = $questionCreator->getQuestionObject($params['question_type']);
    $questionObject->loadFromData($params);
    return $questionObject;
  }

  /**
  * This method sets the question creator object.
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
  *
  * @return PapayaQuestionnaireQuestionCreator
  */
  public function getQuestionCreator() {
    if (!is_object($this->_questionCreator)) {
      include_once(dirname(__FILE__).'/Question/Creator.php');
      $questionCreator = new PapayaQuestionnaireQuestionCreator;
      $questionCreator->setConfiguration($this->_configuration);
      $this->setQuestionCreator($questionCreator);
    }
    return $this->_questionCreator;
  }

  /**
  * This method processes deleting a question. It triggers the deletion or the confirmation
  * dialog generation.
  * @return boolean
  */
  protected function _processDeleteQuestion() {
    $result = FALSE;
    if (isset($this->params['question_id']) && $this->params['question_id'] > 0) {
      if (isset($this->params['submit'])) {
        if ($this->_connector->deleteQuestion($this->params['question_id'])) {
          $this->addMsg(
            MSG_INFO,
            sprintf(
              $this->_gt('Question #%d deleted.'),
              $this->params['question_id']
            )
          );
          $result = TRUE;
        } else {
          $this->addMsg(MSG_ERROR, $this->_gt('Failed to delete question.'));
        }
      } elseif ($dialog = $this->_dialogs->getDeleteQuestionDialog()) {
        $this->layout->add($dialog->getMsgDialog());
      }
    }
    return $result;
  }

  /**
  * This method processes moving a group up.
  * @return boolean
  */
  protected function _processMoveGroupUp() {
    $result = FALSE;
    if (isset($this->params['question_group_id']) && $this->params['question_group_id'] > 0) {
      $result = $this->_connector->moveGroupUp($this->params['question_group_id']);
    }
    return $result;
  }

  /**
  * This method processes moving a group down.
  * @return boolean
  */
  protected function _processMoveGroupDown() {
    $result = FALSE;
    if (isset($this->params['question_group_id']) && $this->params['question_group_id'] > 0) {
      $result = $this->_connector->moveGroupDown($this->params['question_group_id']);
    }
    return $result;
  }

  /**
  * This method processes moving a question up.
  * @return boolean
  */
  protected function _processMoveQuestionUp() {
    $result = FALSE;
    if (isset($this->params['question_id']) && $this->params['question_id'] > 0) {
      $result = $this->_connector->moveQuestionUp($this->params['question_id']);
    }
    return $result;
  }

  /**
  * This method processes moving a question down.
  * @return boolean
  */
  protected function _processMoveQuestionDown() {
    $result = FALSE;
    if (isset($this->params['question_id']) && $this->params['question_id'] > 0) {
      $result = $this->_connector->moveQuestionDown($this->params['question_id']);
    }
    return $result;
  }

  /**
   * displays an overview of all questionnaires
   */
  protected function _processOverview($extended = FALSE) {
    $overview = $this->getOverviewObject();
    if (isset($this->params['print']) && $this->params['print'] == 1) {
      $html = $overview->getHtml($extended);
      $this->getResponseObject()->content(new PapayaResponseContentString($html));
      $this->getResponseObject()->send();
      $this->getResponseObject()->end();
    } else {
      $params = $this->params;
      $params['print'] = 1;
      $printLink = $this->getLink($params);
      $overview->setPrintLink($printLink);
      $xml = $overview->getXml($extended);
      $this->layout->add($xml);
    }
  }

  /**
   * set the response object
   * @param PapayaResponse $response
   */
  public function setResponseObject($response) {
    $this->_response = $response;
  }

  /**
   * get the response object
   * @return PapayaResponse
   */
  public function getResponseObject() {
    if ($this->_response == NULL) {
      $this->setResponseObject(new PapayaResponse());
    }
    return $this->_response;
  }

  /**
   * set the overview object
   * @param PapayaQuestionnaireOverview $overview
   */
  public function setOverviewObject($overview) {
    $this->_overview = $overview;
  }

  /**
   * get the overview object
   * @return PapayaQuestionnaireOverview
   */
  public function getOverviewObject() {
    if ($this->_overview == NULL) {
      include_once(dirname(__FILE__).'/Overview.php');
      $tablePrefix = $this->papaya()->options->getOption('PAPAYA_DB_TABLEPREFIX');
      $this->setOverviewObject(new PapayaQuestionnaireOverview($tablePrefix));
    }
    return $this->_overview;
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
  * This method sets the connector object
  * @param PapayaQuestionnaireConnector $connector
  * @return boolean
  */
  public function setConnector($connector) {
    $result = FALSE;
    if (is_object($connector)) {
      $this->_connector = $connector;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method sets the plugin loader object
  * @param base_pluginloader $pluginLoader
  * @return boolean
  */
  public function setPluginLoader($pluginLoader) {
    $result = FALSE;
    if ($pluginLoader instanceof base_pluginloader) {
      $this->_pluginLoader = $pluginLoader;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method sets the dialogs object
  * @param PapayaQuestionnaireAdministrationDialogs $dialogObject
  * @return boolean
  */
  public function setDialogObject($dialogObject) {
    $result = FALSE;
    if ($dialogObject instanceof PapayaQuestionnaireAdministrationDialogs) {
      $creator = $this->_connector->getQuestionCreator();
      $questionTypes = $creator->getQuestionTypes();
      $this->_dialogs = $dialogObject;
      $this->_dialogs->setParameters($this->params);
      $this->_dialogs->setParamName($this->paramName);
      $this->_dialogs->setQuestionTypes($questionTypes);
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method sets the output object
  * @param PapayaQuestionnaireAdministrationOutput$outputObject
  * @return boolean
  */
  public function setOutputObject($outputObject) {
    $result = FALSE;
    if ($outputObject instanceof PapayaQuestionnaireAdministrationOutput) {
      $this->_output = $outputObject;
      $this->_output->setParameters($this->params);
      $this->_output->setImages($this->images);
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method sets the layout object
  * @param PapayaTemplateXslt|papaya_xsl $layout
  * @return boolean
  */
  public function setLayoutObject($layout) {
    $result = FALSE;
    if (isset($layout)) {
      $this->layout = $layout;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method initializes the output object if it isn't set already.
  */
  protected function _initializeOutputObject() {
    if (!isset($this->_output)) {
      include_once(dirname(__FILE__).'/Administration/Output.php');
      $this->setOutputObject(new PapayaQuestionnaireAdministrationOutput);
    }
  }

  /**
  * This method triggers the generation of XML lists, the toolbar and adding it to the layout.
  */
  public function getXML() {
    $this->_initializeOutputObject();

    $this->layout->addMenu($this->_output->getToolbar());

    if ($pools = $this->_connector->getPools()) {
      $this->layout->addLeft($this->_output->getPoolList($pools));
    }

    if (isset($this->params['question_pool_id']) &&
        ($groups = $this->_connector->getGroups($this->params['question_pool_id']))) {
      $this->layout->addLeft($this->_output->getGroupList($groups));
    }

    if (isset($this->params['question_group_id']) &&
        ($questions = $this->_connector->getQuestions($this->params['question_group_id']))) {
      $this->layout->addLeft($this->_output->getQuestionList($questions));
    }
  }


}
