<?php
/**
* The abstract Question class provides methods, shared by most Question types.
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
* @version $Id: Abstract.php 5 2014-02-13 15:37:31Z SystemVCS $
* @tutorial Questionnaire/PapayaQuestionnaireQuestionAbstract.cls
*/

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireQuestionAbstract {

  private $_configuration;

  /**
  * Holds the configuration fields, similar to editFields.
  *
  * getConfigurationDialog uses this as the dialog fields
  * @var array
  */
  protected $_configurationFields = array();

  protected $_answerFields = array();

  protected $_paramName;

  /**
  * Holds the hidden parameters that need to be passed as well.
  *
  * @var array
  */
  protected $_hiddenParameters = array();

  /**
  * Holds an instance of the PapayaQuestionnaireConnector
  * @var PapayaQuestionnaireConnector
  */
  protected $_connector;

  /**
  * Holds an instance of base_simpletemplate
  * @var base_simpletemplate
  */
  protected $_template;

  protected $_answers = array();

  protected $_questionConfig = array();

  protected $_id = 0;

  protected $_groupId;

  protected $_questionText;

  protected $_questionType;

  protected $_identifier;

  /**
  * This method sets the paramName to be used.
  *
  * @param string $paramName
  */
  public function setParamName($paramName) {
    $this->_paramName = $paramName;
  }

  /**
  * This method sets the connector object
  * @param PapayaQuestionnaireConnector $connector
  * @return boolean
  */
  public function setConnector($connector) {
    $result = FALSE;
    if ($connector instanceof PapayaQuestionnaireConnector) {
      $this->_connector = $connector;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method retrieves the connector and initializes it if necessary.
  * @return PapayaQuestionnaireConnector
  */
  public function getConnector() {
    if (!isset($this->_connector) || !($this->_connector instanceof PapayaQuestionnaireConnector)) {
      include_once(dirname(__FILE__).'/../Connector.php');
      $connector = new PapayaQuestionnaireConnector;
      $connector->setConfiguration($this->_configuration);
      $this->setConnector($connector);
    }
    return $this->_connector;
  }

  /**
  * This method sets the dialog builder object
  *
  * @param PapayaQuestionnaireDialogBuilder $dialogBuilder
  */
  public function setDialogBuilder($dialogBuilder) {
    if (is_object($dialogBuilder)) {
      $this->_dialogBuilder = $dialogBuilder;
    }
  }

  /**
  * This method initializes the dialog builder in case it wasn't set.
  */
  public function getDialogBuilder() {
    if (!isset($this->_dialogBuilder)) {
      include_once(dirname(__FILE__).'/../Dialog/Builder.php');
      $this->setDialogBuilder(new PapayaQuestionnaireDialogBuilder);
    }
    return $this->_dialogBuilder;
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

  public function setQuestionConfigurationValue($key, $value) {
    $this->_questionConfig[$key] = $value;
  }

  /**
  * This method adds a hidden parameter to the dialog that will be submitted as well.
  *
  * @param string $key
  * @param string $value
  */
  public function addHiddenParameter($key, $value) {
    $this->_hiddenParameters[$key] = $value;
  }

  /**
  * This method creates the configuration dialog, an instance of base_dialog.
  *
  * @param array $data
  * @return base_dialog $dialog
  */
  public function getConfigurationDialog() {
    include_once(PAPAYA_INCLUDE_PATH.'system/base_dialog.php');
    $dialogBuilder = $this->getDialogBuilder();
    $data = $this->getDialogData();
    $dialog = $dialogBuilder->createDialog(
      $this, $this->_paramName, $this->_configurationFields, $data, $this->_hiddenParameters);
    return $dialog;
  }

  public function getDialogData() {
    foreach ($this->_configurationFields as $field => $configuration) {
      $value = $this->getConfigurationOption($field);
      if ($value !== FALSE) {
        $result[$field] = $value;
      } elseif (isset($this->_answerFields[$field])) {
        if (!isset($i)) {
          $i = 1;
        }
        $result['answer_'.$i] = $this->getAnswerText($i);
        $result['answer_value_'.$i] = $this->getAnswerValue($i);
        $i++;
      }
    }
    return $result;
  }

  public function loadFromData($data) {
    foreach ($data as $key => $value) {
      switch ($key) {
      case 'question_id':
        $this->setId((int)$value);
        break;
      case 'question_position':
        $this->setPosition($value);
        break;
      case 'question_group_id':
        $this->setGroupId((int)$value);
        break;
      case 'question_identifier':
        $this->setIdentifier($value);
        break;
      case 'question_type':
        $this->setType($value);
        break;
      case 'question_text':
        $this->setText($value);
        break;
      case 'question_answer_data':
        $this->setConfigurationData($value);
        break;
      default:
        if (isset($this->_configurationFields[$key])) {
          $this->setQuestionConfigurationValue($key, $value);
        }
        if (isset($this->_answerFields[$key])) {
          $this->setAnswerText(substr($key, strlen('answer_')), $value);
        } elseif (in_array($key, $this->_answerFields)) {
          $this->setAnswerValue(substr($key, strlen('answer_value_')), $value);
        }
        break;
      }
    }
  }

  public function getConfigurationDataXML() {
    $configurationXML = PapayaUtilStringXml::serializeArray($this->_questionConfig);
    return $configurationXML;
  }

  public function setConfigurationData($data) {
    if (is_string($data) && $data != '') {
      $configurationData = PapayaUtilStringXml::unserializeArray($data);
      $this->loadFromData($configurationData);
    } elseif (is_array($data)) {
      $this->_questionConfig = $data;
    }
  }

  public function getQuestionConfiguration() {
    return $this->_questionConfig;
  }

  public function getConfigurationOption($key) {
    $result = FALSE;
    if (isset($this->_questionConfig[$key])) {
      $result = $this->_questionConfig[$key];
    }
    return $result;
  }

  public function getAnswerText($position) {
    $result = FALSE;
    if (isset($this->_answerTexts[$position])) {
      $result = $this->_answerTexts[$position];
    }
    return $result;
  }

  public function getAnswerValue($position) {
    $result = FALSE;
    if (isset($this->_answerValues[$position])) {
      $result = $this->_answerValues[$position];
    }
    return $result;
  }

  public function getAnswerOptionId($position) {
    $result = FALSE;
    if (isset($this->_answerOptionIds[$position])) {
      $result = $this->_answerOptionIds[$position];
    }
    return $result;
  }

  public function getAnswers() {
    $result = array();
    foreach ($this->_answerTexts as $position => $text) {
      if ($text != '') {
        $result[$position]['text'] = $text;
        if (isset($this->_answerValues[$position])) {
          $result[$position]['value'] = $this->_answerValues[$position];
        }
        $result[$position]['answer_id'] = $this->getAnswerOptionId($position);
      }
    }
    return $result;
  }

  public function setAnswers($answers) {
    $i = 1;
    foreach ($answers as $answerOptionId => $answer) {
      $this->setAnswerOptionId($i, $answerOptionId);
      $this->setAnswerText($i, $answer['text']);
      $this->setAnswerValue($i, $answer['value']);
      $i++;
    }
    $this->_answers = $answers;
  }

  public function unsetAnswers() {
    $this->_answers = array();
    $this->_answerTexts = array();
    $this->_answerValues = array();
    $this->_answerOptionIds = array();
  }

  public function setAnswerText($position, $text) {
    $this->_answerTexts[$position] = $text;
  }

  public function setAnswerValue($position, $value) {
    $this->_answerValues[$position] = $value;
  }

  public function setAnswerOptionId($position, $id) {
    $this->_answerOptionIds[$position] = $id;
  }

  public function setId($id) {
    if ($id > 0) {
      $this->_id = (int)$id;
    }
  }

  public function getId() {
    return $this->_id;
  }

  public function setGroupId($groupId) {
    if ($groupId > 0) {
      $this->_groupId = (int)$groupId;
    }
  }

  public function getGroupId() {
    return $this->_groupId;
  }

  public function setText($text) {
    $this->_questionText = $text;
  }

  public function getText() {
    return $this->_questionText;
  }

  public function setType($type) {
    $this->_questionType = $type;
  }

  public function getType() {
    return $this->_questionType;
  }

  public function setPosition($pos) {
    $this->_position = $pos;
  }

  public function getPosition() {
    return $this->_position;
  }

  public function setIdentifier($identifier) {
    $this->_identifier = $identifier;
  }

  public function getIdentifier() {
    return $this->_identifier;
  }

  /**
  * This method replaces the template placeholders in the content with their corresponding values
  * @param string $content
  * @param array $replacements
  * @return string
  */
  public function replaceTemplateValues($content, $replacements) {
    $templateObject = $this->_getTemplateObject();
    return $templateObject->parse($content, $replacements);
  }

  /**
  * This method retrieves the simpletemplate instance and initializes it if necessary
  * @return base_simpletemplate
  */
  protected function _getTemplateObject() {
    if (!(
        is_object($this->_template) &&
        $this->_template instanceof base_simpletemplate)) {
      include_once(PAPAYA_INCLUDE_PATH.'system/base_simpletemplate.php');
      $this->setTemplateObject(new base_simpletemplate);
    }
    return $this->_template;
  }

  /**
  * This method sets the simpletemplate object to be used
  * @param bas_simpletemplate $simpleTemplate
  * @return boolean
  */
  public function setTemplateObject($simpleTemplate) {
    $result = FALSE;
    if ($simpleTemplate instanceof base_simpletemplate) {
      $this->_template = $simpleTemplate;
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method sets the configuration fields; necessary for testing
  *
  * @param array $fields configuration fields
  */
  public function setConfigurationFields(array $fields) {
    $this->_configurationFields = $fields;
  }

  /**
  * This method sets the answer fields; necessary for testing
  *
  * @param array $fields answer fields
  */
  public function setAnswerFields(array $fields) {
    $this->_answerFields = $fields;
  }
}
