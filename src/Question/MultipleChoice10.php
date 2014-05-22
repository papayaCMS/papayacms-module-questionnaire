<?php
/**
* The Single Choice 10 Question class provides a question with up to 10 possible answers
* whereof one may be chosen.
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
* @version $Id: MultipleChoice10.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* Include Question interface
*/
require_once(dirname(__FILE__).'/../Question.php');

/**
* Include single choice 5 class
*/
require_once(dirname(__FILE__).'/SingleChoice5.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class        PapayaQuestionnaireQuestionMultipleChoice10
  extends    PapayaQuestionnaireQuestionAbstract
  implements PapayaQuestionnaireQuestion {

  /**
  * Configuration of this type of question
  * @var array
  */
  protected $_configurationFields = array(
    'Settings',
    'alignment' => array('Alignment', 'isNoHTML', TRUE, 'combo',
      array('horizontal' => 'Horizontal', 'vertical' => 'Vertical')),
    'type' => array('Type', 'isNoHTML', TRUE, 'combo',
      array('combo' => 'Dropdown multiselect', 'checkbox' => 'Checkboxes')),
    'mandatory' => array('Answer mandatory', 'isNum', TRUE, 'yesno', '', '', 0),
    'Answers',
    'answer_1' => array('Answer 1', 'isNoHTML', TRUE, 'input', 1000),
    'answer_2' => array('Answer 2', 'isNoHTML', TRUE, 'input', 1000),
    'answer_3' => array('Answer 3', 'isNoHTML', TRUE, 'input', 1000),
    'answer_4' => array('Answer 4', 'isNoHTML', TRUE, 'input', 1000),
    'answer_5' => array('Answer 5', 'isNoHTML', TRUE, 'input', 1000),
    'answer_6' => array('Answer 6', 'isNoHTML', TRUE, 'input', 1000),
    'answer_7' => array('Answer 7', 'isNoHTML', TRUE, 'input', 1000),
    'answer_8' => array('Answer 8', 'isNoHTML', TRUE, 'input', 1000),
    'answer_9' => array('Answer 9', 'isNoHTML', TRUE, 'input', 1000),
    'answer_10' => array('Answer 10', 'isNoHTML', TRUE, 'input', 1000),
    'Values',
    'answer_value_1' => array('Answer value 1', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_1'),
    'answer_value_2' => array('Answer value 2', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_2'),
    'answer_value_3' => array('Answer value 3', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_3'),
    'answer_value_4' => array('Answer value 4', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_4'),
    'answer_value_5' => array('Answer value 5', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_5'),
    'answer_value_6' => array('Answer value 6', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_6'),
    'answer_value_7' => array('Answer value 7', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_7'),
    'answer_value_8' => array('Answer value 8', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_8'),
    'answer_value_9' => array('Answer value 9', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_9'),
    'answer_value_10' => array('Answer value 10', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_10'),
    'Prechecked',
    'answer_checked_1' => array('Answer 1 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_2' => array('Answer 2 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_3' => array('Answer 3 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_4' => array('Answer 4 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_5' => array('Answer 5 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_6' => array('Answer 6 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_7' => array('Answer 7 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_8' => array('Answer 8 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_9' => array('Answer 9 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_10' => array('Answer 10 checked', 'isNum', TRUE, 'yesno', '', '', 0),
  );

  protected $_answerFields = array(
    'answer_1' => 'answer_value_1',
    'answer_2' => 'answer_value_2',
    'answer_3' => 'answer_value_3',
    'answer_4' => 'answer_value_4',
    'answer_5' => 'answer_value_5',
    'answer_6' => 'answer_value_6',
    'answer_7' => 'answer_value_7',
    'answer_8' => 'answer_value_8',
    'answer_9' => 'answer_value_9',
    'answer_10' => 'answer_value_10',
  );

  protected $_allowXHTML = FALSE;

  protected $_preselectAnswers = TRUE;

  public function usePreselection() {
    $this->_preselectAnswers = TRUE;
  }

  public function useNoPreselection() {
    $this->_preselectAnswers = FALSE;
  }

  /**
  * (non-PHPdoc)
  * @see papaya-lib/modules/commercial/Questionnaire/PapayaQuestionnaireQuestion#getQuestionXML()
  */
  public function getQuestionXML($questionId, $selectedAnswer = FALSE, $replacements = array()) {
    $result = '';
    if ($connector = $this->getConnector()) {

      $mandatory = ($this->getConfigurationOption('mandatory')) ? ' mandatory="mandatory"' : '';

      $result .= sprintf(
        '<question id="%d" identifier="%s" key="%d-%d" alignment="%s" type="%s"%s>'.LF,
        papaya_strings::escapeHTMLChars($questionId),
        papaya_strings::escapeHTMLChars($this->getIdentifier()),
        $this->getGroupId(),
        $this->getId(),
        papaya_strings::escapeHTMLChars($this->getConfigurationOption('alignment')),
        papaya_strings::escapeHTMLChars($this->getConfigurationOption('type')),
        $mandatory
      );
      $result .= sprintf(
        '<text>%s</text>'.LF,
        papaya_strings::escapeHTMLChars(
          $this->replaceTemplateValues($this->getText(), $replacements)));
      $result .= $this->_getAnswersXML($selectedAnswer);
      $result .= '</question>'.LF;
    }
    return $result;
  }

  protected function _getAnswersXML($selectedAnswer) {
    $result = '';
    $result .= '<answers>'.LF;
    $answerPosition = 1;
    foreach ($this->_answerFields as $answerId => $answerValueKey) {
      if (($optionId = $this->getAnswerOptionId($answerPosition)) &&
          ($answerText = $this->getAnswerText($answerPosition)) &&
          ($answerValue = $this->getAnswerValue($answerPosition))) {
        if (isset($selectedAnswer[$optionId]) || (
            $this->_preselectAnswers && !$selectedAnswer &&
            $this->isAnswerPrechecked($answerPosition))) {
          $selected = ' selected="selected"';
        } else {
          $selected = '';
        }
        $text = ($this->_allowXHTML)
          ? base_object::getXHTMLString($answerText)
          : papaya_strings::escapeHTMLChars($answerText);
        $result .= sprintf(
          '<answer id="%s" value="%s" %s>%s</answer>'.LF,
          papaya_strings::escapeHTMLChars($answerId),
          papaya_strings::escapeHTMLChars($optionId),
          $selected,
          $text);
      }
      $answerPosition++;
    }
    $result .= '</answers>'.LF;
    return $result;
  }

  public function isAnswerPrechecked($position) {
    return (bool)$this->getConfigurationOption('answer_checked_'.$position);
  }

}
