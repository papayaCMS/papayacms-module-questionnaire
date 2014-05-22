<?php
/**
* The Single Choice 5 Question class provides a question with 5 possible answers
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
* @version $Id: SingleChoice5.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* Include Question interface
*/
require_once(dirname(__FILE__).'/../Question.php');

/**
* Include abstract question class
*/
require_once(dirname(__FILE__).'/Abstract.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class        PapayaQuestionnaireQuestionSingleChoice5
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
      array('combo' => 'Combobox (Dropdown)', 'radio' => 'Radiobuttons')),
    'mandatory' => array('Answer mandatory', 'isNum', TRUE, 'yesno', '', '', 0),
    'Answers',
    'answer_1' => array('Answer 1', 'isNoHTML', TRUE, 'input', 1000),
    'answer_2' => array('Answer 2', 'isNoHTML', TRUE, 'input', 1000),
    'answer_3' => array('Answer 3', 'isNoHTML', TRUE, 'input', 1000),
    'answer_4' => array('Answer 4', 'isNoHTML', TRUE, 'input', 1000),
    'answer_5' => array('Answer 5', 'isNoHTML', TRUE, 'input', 1000),
    'Values',
    'answer_value_1' => array('Answer value 1', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_1'),
    'answer_value_2' => array('Answer value 2', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_2'),
    'answer_value_3' => array('Answer value 3', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_3'),
    'answer_value_4' => array('Answer value 4', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_4'),
    'answer_value_5' => array('Answer value 5', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_5'),
  );

  protected $_answerFields = array(
    'answer_1' => 'answer_value_1',
    'answer_2' => 'answer_value_2',
    'answer_3' => 'answer_value_3',
    'answer_4' => 'answer_value_4',
    'answer_5' => 'answer_value_5',
  );

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
      $result .= '<answers>'.LF;
      $answerPosition = 1;
      foreach ($this->_answerFields as $answerId => $answerValueKey) {
        if (($optionId = $this->getAnswerOptionId($answerPosition)) &&
            ($answerText = $this->getAnswerText($answerPosition)) &&
            ($answerValue = $this->getAnswerValue($answerPosition))) {
          if ($selectedAnswer == $optionId) {
            $selected = ' selected="selected"';
          } else {
            $selected = '';
          }
          $result .= sprintf(
            '<answer id="%s" value="%s" %s>%s</answer>'.LF,
            papaya_strings::escapeHTMLChars($answerId),
            papaya_strings::escapeHTMLChars($optionId),
            $selected,
            papaya_strings::escapeHTMLChars($answerText));
        }
        $answerPosition++;
      }
      $result .= '</answers>'.LF;
      $result .= '</question>'.LF;
    }
    return $result;
  }

  /**
  * This method checks, whether answering a given question is mandatory.
  *
  * @param integer $questionId
  * @return boolean
  */
  public function isAnswerMandatory() {
    return (isset($this->_questionConfig['mandatory']) && $this->_questionConfig['mandatory']);
  }

}
