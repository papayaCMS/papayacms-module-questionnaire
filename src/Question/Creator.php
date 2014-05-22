<?php
/**
* The Creator class provides means of generating question objects by type.
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
* @version $Id: Creator.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireQuestionCreator extends PapayaObject {

  protected $_configuration;

  /**
  * List of known question types
  * @var array
  */
  private $_questionTypes = array(
    'single_choice_5' => array(
      'name' => 'Single Choice 5',
      'file' => 'SingleChoice5.php',
      'class' => 'PapayaQuestionnaireQuestionSingleChoice5',
    ),
    'single_choice_10' => array(
      'name' => 'Single Choice 10',
      'file' => 'SingleChoice10.php',
      'class' => 'PapayaQuestionnaireQuestionSingleChoice10',
    ),
    'multiple_choice_10' => array(
      'name' => 'Multiple Choice 10',
      'file' => 'MultipleChoice10.php',
      'class' => 'PapayaQuestionnaireQuestionMultipleChoice10',
    ),
    'multiple_choice_10_rt' => array(
      'name' => 'Multiple Choice 10 Richtext',
      'file' => 'MultipleChoice10Richtext.php',
      'class' => 'PapayaQuestionnaireQuestionMultipleChoice10Richtext',
    ),
    'multiple_choice_many_rt' => array(
      'name' => 'Multiple Choice 25 Richtext',
      'file' => 'MultipleChoice25Richtext.php',
      'class' => 'PapayaQuestionnaireQuestionMultipleChoice25Richtext',
    ),
  );

  /**
  * This method sets the configuration object
  * @param PapayaConfiguration $configuration
  */
  public function setConfiguration($configuration) {
    $this->_configuration = $configuration;
  }

  public function getConfiguration() {
    if (empty($this->_configuration)) {
      $this->setConfiguration($this->papaya()->options);
    }
    return $this->_configuration;
  }

  public function registerQuestionType($identifier, $name, $file, $class) {
    $result = FALSE;
    if (!isset($this->_questionTypes[$identifier])) {
      $this->_questionTypes[$identifier] = array(
        'name' => $name,
        'file' => $file,
        'class' => $class,
      );
      $result = TRUE;
    }
    return $result;
  }

  /**
  * This method retrieves a question object for a given type
  * @param string $questionType
  * @return object
  */
  public function getQuestionObject($questionType) {
    $result = FALSE;
    if (isset($this->_questionTypes[$questionType])) {
      if (is_file($this->_questionTypes[$questionType]['file'])) {
        include_once($this->_questionTypes[$questionType]['file']);
      } elseif (is_file(dirname(__FILE__).'/'.$this->_questionTypes[$questionType]['file'])) {
        include_once(dirname(__FILE__).'/'.$this->_questionTypes[$questionType]['file']);
      } else {
        return FALSE;
      }
      if (class_exists($this->_questionTypes[$questionType]['class'])) {
        $class = $this->_questionTypes[$questionType]['class'];
        $questionObject = new $class;
        $questionObject->setConfiguration($this->getConfiguration());
        $questionObject->setType($questionType);
        $result = $questionObject;
      }
    }
    return $result;
  }

  /**
  * This method retrieves the list of known questions as identifier => name
  * @return array
  */
  public function getQuestionTypes() {
    $result = array();
    foreach ($this->_questionTypes as $identifier => $type) {
      $result[$identifier] = $type['name'];
    }
    return $result;
  }

}
