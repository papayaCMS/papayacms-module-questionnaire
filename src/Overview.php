<?php
/**
* The Overview class displays an overview of all configured questionnaires in the backend.
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
* @version $Id: Overview.php 6 2014-02-18 17:23:00Z SystemVCS $
*/

require_once(PAPAYA_INCLUDE_PATH.'system/sys_base_db.php');

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireOverview extends base_db {

  private $_tablePool;
  private $_tableGroup;
  private $_tableQuestion;
  private $_tableAnswerOptions;

  private $_printLink = '';

  /**
   * constructor
   * @param string $tablePrefix papaya table prefix
   */
  public function __construct($tablePrefix) {
    $this->_tablePool = $tablePrefix.'_questionnaire_pool';
    $this->_tableGroup = $tablePrefix.'_questionnaire_group';
    $this->_tableQuestion = $tablePrefix.'_questionnaire_question';
    $this->_tableAnswerOptions = $tablePrefix.'_questionnaire_answer_options';
  }

  /**
   * calculate a checksum for the entire questionnaire configuration
   * @rturn string checksum
   */
  public function getChecksum() {
    $data = $this->_loadFromDatabase();
    return $this->_getChecksum($data);
  }

  public function getHtml($extended = FALSE) {
    $data = $this->_loadFromDatabase();
    $html = sprintf(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="robots" content="noindex, nofollow">
        <title>%1$s</title>
        <meta name="MSSmartTagsPreventParsing" content="TRUE">
        <style type="text/css">
          body { font-size: 10pt; }
          h4 { margin-bottom: 0; }
          ul { margin-top: 0.25em; }
        </style>
        </head>
        <body>
        <h1>%s [%s]</h1>
        %s
        </body>
        </html>',
      $this->_gt('Overview of all Questionnaires'),
      $this->_getChecksum($data),
      $this->_renderOverview($data, $extended)
    );
    return $html;
  }

  /**
   * get papaya admin backend XML that displays the entire questionnaire configuration
   * @param boolean $extended set to TRUE to include extended information
   * @return string xml string
   */
  public function getXml($extended = FALSE) {
    $data = $this->_loadFromDatabase();

    $printLink = '';
    if ($this->_printLink != '') {
      $printLink = sprintf(
        '<span style="float: right">[<a href="%s" target="_blank">Druckansicht</a>]</span> ',
        $this->_printLink
      );
    }
    $headline = sprintf(
      '%s%s [%s]',
      $printLink,
      $this->_gt('Overview of all Questionnaires'),
      $this->_getChecksum($data)
    );
    $overview = $this->_renderOverview($data, $extended);
    $xml = sprintf(
      '<sheet>
        <header>
          <lines>
            <line class="headertitle">%s</line>
          </lines>
        </header>
        <text>%s</text>
      </sheet>',

      $headline,
      $overview
    );

    return $xml;
  }

  public function setPrintLink($printLink) {
    $this->_printLink = $printLink;
  }

  /**
   * load questionnaire configuration from database and return as big nested array
   * @return array questionnaire configuration
   */
  private function _loadFromDatabase() {
    $sql = "SELECT question_pool_id, question_pool_name, question_pool_identifier,
                   question_group_id, question_group_identifier, question_group_position,
                   question_group_name, question_group_text, question_group_min_answers,
                   question_group_subtitle,
                   question_id, question_identifier, question_position, question_type,
                   question_text, question_answer_data,
                   answer_choice_id, answer_choice_text, answer_choice_value
              FROM %s AS pool
         LEFT JOIN %s AS grp USING (question_pool_id)
         LEFT JOIN %s AS question USING (question_group_id)
         LEFT JOIN %s AS answer USING (question_id)
          ORDER BY question_pool_id, question_group_position, question_position, answer_choice_id";
    $params = array(
      $this->_tablePool,
      $this->_tableGroup,
      $this->_tableQuestion,
      $this->_tableAnswerOptions,
    );
    $dbResult = $this->databaseQueryFmt($sql, $params);
    $result = array();
    while ($row = $dbResult->fetchRow(DB_FETCHMODE_ASSOC)) {
      $poolId = $row['question_pool_id'];
      $groupId = $row['question_group_id'];
      $questionId = $row['question_id'];
      $answerId = $row['answer_choice_id'];

      if (!isset($result[$poolId])) {
        $result[$poolId] = array();
      }
      $pool =& $result[$poolId];

      $pool['name'] = $row['question_pool_name'];
      $pool['identifier'] = $row['question_pool_identifier'];

      $pool['groups'][$groupId]['identifier'] = $row['question_group_identifier'];
      $pool['groups'][$groupId]['position'] = $row['question_group_position'];
      $pool['groups'][$groupId]['name'] = $row['question_group_name'];
      $pool['groups'][$groupId]['text'] = $row['question_group_text'];
      $pool['groups'][$groupId]['min_answers'] = $row['question_group_min_answers'];
      $pool['groups'][$groupId]['subtitle'] = $row['question_group_subtitle'];

      $pool['groups'][$groupId]['questions'][$questionId]['identifier'] =
        $row['question_identifier'];
      $pool['groups'][$groupId]['questions'][$questionId]['position'] =
        $row['question_position'];
      $pool['groups'][$groupId]['questions'][$questionId]['type'] =
        $row['question_type'];
      $pool['groups'][$groupId]['questions'][$questionId]['text'] =
        $row['question_text'];
      $pool['groups'][$groupId]['questions'][$questionId]['answer_data'] =
        PapayaUtilStringXml::unserializeArray($row['question_answer_data']);

      $pool['groups'][$groupId]['questions'][$questionId]['answers'][$answerId]['text'] =
        $row['answer_choice_text'];
      $pool['groups'][$groupId]['questions'][$questionId]['answers'][$answerId]['value'] =
        $row['answer_choice_value'];
    }
    return $result;
  }

  /**
   * transform questionnaire configuration array into HTML
   * @param array $data questionnaire configuration
   * @param boolean $extended set to TRUE to include extended information
   * @return string HTML view of questionnaire configuration
   */
  private function _renderOverview($data, $extended) {
    $result = '<div>';
    $result .= sprintf('<h2>%s</h2>', $this->_gt('Table of Contents'));
    $result .= '<ul style="margin-bottom: 4em;">';
    foreach ($data as $poolId => $pool) {
      $result .= sprintf('<li><a href="#pool-%s">%s</a></li>', $poolId, $pool['name']);
    }
    $result .= '</ul>';
    foreach ($data as $poolId => $pool) {
      $result .= sprintf(
        '<h2 style="margin-top: 3em;"><a name="pool-%s" style="color: #000;">%s [%s]</a></h2>',
        $poolId,
        $pool['name'],
        $this->_getChecksum($pool)
      );
      $result .= '<div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">';
      if ($extended) {
        $result .= $this->_renderTable($pool, array('groups'));
      }
      foreach ($pool['groups'] as $group) {
        $result .= sprintf(
          '<h3>%s: %s [%s]</h3>',
          $group['identifier'],
          $group['name'],
          $this->_getChecksum($group)
        );
        $result .=
          '<div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">';
        if ($extended) {
          $result .= $this->_renderTable($group, array('questions'));
        }
        foreach ($group['questions'] as $question) {
          $result .= sprintf(
            '<h4>%s: %s %s [%s]</h4>',
            $question['identifier'],
            $question['text'],
            $question['answer_data']['mandatory'] ? '<strong>(X)</strong>' : '',
            $this->_getChecksum($question)
          );
          $result .= '<div style="margin-left: 1em; padding-left: 1em;">';
          if ($extended) {
            $result .= $this->_renderTable($question, array('answers'));
          }
          $result .= '<ul style="padding-left: 1em;">';
          foreach ($question['answers'] as $answer) {
            $result .= sprintf(
              '<li>%s = <strong>%s</strong></li>',
              $answer['text'],
              $answer['value']
            );
          }
          $result .= '</ul>';
          $result .= '</div>';
        }
        $result .= '</div>';
      }
      $result .= '</div>';
    }
    $result .= '</div>';
    return $result;
  }

  /**
   * recursively renders an array as HTML table
   * @param array $array array of data
   * @param array $ignoreKeys keys in the array that will not be rendered
   * @return string HTML table
   */
  private function _renderTable($array, $ignoreKeys = array()) {
    $result = '<table style="border-collapse: collapse; font-size: 10px;">';
    foreach ($array as $key => $value) {
      if (!in_array($key, $ignoreKeys)) {
        $result .= sprintf(
          '<tr>
            <th style="border: 1px #ccc solid;">%s</th>
            <td style="border: 1px #ccc solid;">%s</td>
           </tr>',
          $key,
          $this->_renderValue($value)
        );
      }
    }
    $result .= '</table>';
    return $result;
  }

  /**
   * renders a value (either directly or as a table if it is an array)
   * @param mixed $value value to render
   * @return string string or HTML representation of the value
   */
  private function _renderValue($value) {
    if (is_array($value)) {
      return $this->_renderTable($value);
    } else {
      return htmlspecialchars($value);
    }
  }

  /**
   * calculate a checksum of an array
   * @param array $data
   * @return string the checksum
   */
  private function _getChecksum($data) {
    return base_convert(sprintf('%u', crc32(serialize($data))), 10, 36);
  }
}