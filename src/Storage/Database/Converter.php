<?php
/*
* This class is intended to convert old DB structures to altered ones.
*/

/**
* Base questionnair storage db class
*/
require_once(dirname(__FILE__).'/Access.php');

class PapayaQuestionnaireStorageDatabaseConverter extends PapayaQuestionnaireStorageDatabaseAccess {

  public function convertXmlAnswerStructure() {
    $questions = $this->getAllQuestions();
    foreach ($questions as $questionId => $questionData) {
      $question = $this->getQuestion($questionId);
      $answerData = PapayaUtilStringXml::unserializeArray($questionData['question_answer_data']);
      $question->unsetAnswers();
      foreach ($answerData as $key => $value) {
        if (substr($key, 0, 7) == 'answer_' && substr($key, 0, 13) != 'answer_value_') {
          $position = substr($key, 7);
          $question->setAnswerValue($position, $key);
          $question->setAnswerText($position, papaya_strings::unicodeEntitiesToUTF8($value));
        }
      }
      $this->updateQuestion($question);
    }
  }
  public function getAllQuestions() {
    $result = array();
    $sql = "SELECT question_id, question_type, question_answer_data
              FROM %s
           ";
    $params = array(
      $this->_tableQuestion
    );
    if ($res = $this->databaseQueryFmt($sql, $params)) {
      while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
        $result[$row['question_id']] = $row;
      }
    }
    return $result;
  }
}
?>