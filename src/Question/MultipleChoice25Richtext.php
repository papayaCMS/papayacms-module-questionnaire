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
* @version $Id: MultipleChoice25Richtext.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* Include Question interface
*/
require_once(dirname(__FILE__).'/../Question.php');

/**
* Include single choice 5 class
*/
require_once(dirname(__FILE__).'/MultipleChoice10Richtext.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class        PapayaQuestionnaireQuestionMultipleChoice25Richtext
  extends    PapayaQuestionnaireQuestionMultipleChoice10Richtext
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
    'answer_1' => array('Answer 1', 'isSomeText', TRUE, 'input', 1000),
    'answer_2' => array('Answer 2', 'isSomeText', TRUE, 'input', 1000),
    'answer_3' => array('Answer 3', 'isSomeText', TRUE, 'input', 1000),
    'answer_4' => array('Answer 4', 'isSomeText', TRUE, 'input', 1000),
    'answer_5' => array('Answer 5', 'isSomeText', TRUE, 'input', 1000),
    'answer_6' => array('Answer 6', 'isSomeText', TRUE, 'input', 1000),
    'answer_7' => array('Answer 7', 'isSomeText', TRUE, 'input', 1000),
    'answer_8' => array('Answer 8', 'isSomeText', TRUE, 'input', 1000),
    'answer_9' => array('Answer 9', 'isSomeText', TRUE, 'input', 1000),
    'answer_10' => array('Answer 10', 'isSomeText', TRUE, 'input', 1000),
    'answer_11' => array('Answer 11', 'isSomeText', TRUE, 'input', 1000),
    'answer_12' => array('Answer 12', 'isSomeText', TRUE, 'input', 1000),
    'answer_13' => array('Answer 13', 'isSomeText', TRUE, 'input', 1000),
    'answer_14' => array('Answer 14', 'isSomeText', TRUE, 'input', 1000),
    'answer_15' => array('Answer 15', 'isSomeText', TRUE, 'input', 1000),
    'answer_16' => array('Answer 16', 'isSomeText', TRUE, 'input', 1000),
    'answer_17' => array('Answer 17', 'isSomeText', TRUE, 'input', 1000),
    'answer_18' => array('Answer 18', 'isSomeText', TRUE, 'input', 1000),
    'answer_19' => array('Answer 19', 'isSomeText', TRUE, 'input', 1000),
    'answer_20' => array('Answer 20', 'isSomeText', TRUE, 'input', 1000),
    'answer_21' => array('Answer 21', 'isSomeText', TRUE, 'input', 1000),
    'answer_22' => array('Answer 22', 'isSomeText', TRUE, 'input', 1000),
    'answer_23' => array('Answer 23', 'isSomeText', TRUE, 'input', 1000),
    'answer_24' => array('Answer 24', 'isSomeText', TRUE, 'input', 1000),
    'answer_25' => array('Answer 25', 'isSomeText', TRUE, 'input', 1000),
    'explanatory_text' => array(
      'Explanatory text', 'isSomeText', FALSE, 'richtext', 7
    ),
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
    'answer_value_11' => array('Answer value 11', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_11'),
    'answer_value_12' => array('Answer value 12', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_12'),
    'answer_value_13' => array('Answer value 13', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_13'),
    'answer_value_14' => array('Answer value 14', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_14'),
    'answer_value_15' => array('Answer value 15', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_15'),
    'answer_value_16' => array('Answer value 16', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_16'),
    'answer_value_17' => array('Answer value 17', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_17'),
    'answer_value_18' => array('Answer value 18', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_18'),
    'answer_value_19' => array('Answer value 19', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_19'),
    'answer_value_20' => array('Answer value 20', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_20'),
    'answer_value_21' => array('Answer value 21', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_21'),
    'answer_value_22' => array('Answer value 22', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_22'),
    'answer_value_23' => array('Answer value 23', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_23'),
    'answer_value_24' => array('Answer value 24', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_24'),
    'answer_value_25' => array('Answer value 25', 'isNoHTML', TRUE, 'input', 1000, '', 'answer_25'),
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
    'answer_checked_11' => array('Answer 11 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_12' => array('Answer 12 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_13' => array('Answer 13 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_14' => array('Answer 14 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_15' => array('Answer 15 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_16' => array('Answer 16 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_17' => array('Answer 17 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_18' => array('Answer 18 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_19' => array('Answer 19 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_20' => array('Answer 20 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_21' => array('Answer 21 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_22' => array('Answer 22 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_23' => array('Answer 23 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_24' => array('Answer 24 checked', 'isNum', TRUE, 'yesno', '', '', 0),
    'answer_checked_25' => array('Answer 25 checked', 'isNum', TRUE, 'yesno', '', '', 0),
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
    'answer_11' => 'answer_value_11',
    'answer_12' => 'answer_value_12',
    'answer_13' => 'answer_value_13',
    'answer_14' => 'answer_value_14',
    'answer_15' => 'answer_value_15',
    'answer_16' => 'answer_value_16',
    'answer_17' => 'answer_value_17',
    'answer_18' => 'answer_value_18',
    'answer_19' => 'answer_value_19',
    'answer_20' => 'answer_value_20',
    'answer_21' => 'answer_value_21',
    'answer_22' => 'answer_value_22',
    'answer_23' => 'answer_value_23',
    'answer_24' => 'answer_value_24',
    'answer_25' => 'answer_value_25',
  );

}
