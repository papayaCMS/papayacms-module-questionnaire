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
* @version $Id: SingleChoice10.php 2 2013-12-09 16:39:31Z weinert $
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
class        PapayaQuestionnaireQuestionSingleChoice10
  extends    PapayaQuestionnaireQuestionSingleChoice5
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

}
