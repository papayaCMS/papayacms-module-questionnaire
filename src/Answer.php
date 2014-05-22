<?php
/**
* The Answer class handles storage and retrieval of questionnaire answers.
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
* @tutorial Answer/PapayaQuestionnaireAnswer.cls
* @package Commercial
* @subpackage Questionnaire
* @version $Id: Answer.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireAnswer {
  /**
  * PapayaQuestionnaireAnswerDatabaseAccess object
  * @var PapayaQuestionnaireAnswerDatabaseAccess
  */
  private $_databaseAccessObject = NULL;

  /**
  * Configuration object
  * @var PapayaConfiguration
  */
  private $_configuration = NULL;

  /**
  * Set the PapayaQuestionnaireAnswerDatabaseAccess object to use
  *
  * @param PapayaQuestionnaireAnswerDatabaseAccess $databaseAccessObject
  */
  public function setDatabaseAccessObject($databaseAccessObject) {
    $this->_databaseAccessObject = $databaseAccessObject;
  }

  /**
  * Get (and, if necessary, initialize) the PapayaQuestionnaireAnswerDatabaseAccess object
  *
  * @return PapayaQuestionnaireAnswerDatabaseAccess
  */
  public function getDatabaseAccessObject() {
    if (!is_object($this->_databaseAccessObject)) {
      include_once(dirname(__FILE__).'/Answer/Database/Access.php');
      $this->_databaseAccessObject = new PapayaQuestionnaireAnswerDatabaseAccess();
      $this->_databaseAccessObject->setConfiguration($this->_configuration);
    }
    return $this->_databaseAccessObject;
  }

  /**
  * Set configuration object
  *
  * @param PapayaConfiguration $configuration
  */
  public function setConfiguration($configuration) {
    $this->_configuration = $configuration;
  }

  /**
  * Get answers without deactivation timestamp by user id and subject id
  *
  * If you provide an array of question ids, only the answers for these questions are loaded.
  * Otherwise, all answers for the user are loaded.
  * The result has got the following format:
  * array($questionId => $answer, ...)
  * If no answers are available, an empty array is returned.
  *
  * @param string $userId
  * @param string $subjectId
  * @param array $questionIds optional, default empty array
  * @return array
  */
  public function getActiveAnswersByUserAndSubject($userId, $subjectId, $questionIds = array()) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getActiveAnswersByUserAndSubject(
      $userId,
      $subjectId,
      $questionIds
    );
  }

  /**
   * Get answers matching a filter
   * @param array $filter array of database fields => value(s)
   * @return array matching answers
   */
  public function getAnswers($filter) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getAnswers($filter);
  }

  /**
  * Get all available answers by user id and subject id
  *
  * The result has got the following format:
  * array($questionId => array('answer_value' => $value, 'answer_deactivated' => $timestamp), ...)
  *
  * @param string $userId
  * @param string $subjectId
  * @param array $questionIds optional, default empty array
  * @return array
  */
  public function getAnswersByUserAndSubject($userId, $subjectId, $questionIds = array()) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getAnswersByUserAndSubject($userId, $subjectId, $questionIds);
  }

  /**
  * Get active answers by subject id
  *
  * By default, the result has got the following format:
  * array($questionId => array($userId => $answer, ...), ...)
  * If you set the optional $byUser parameter to TRUE, the format will switch to:
  * array($userId => array($questionId => $answer, ...), ...)
  *
  * @param string $subjectId
  * @param boolean $byUser optional, default FALSE
  * @return array
  */
  public function getActiveAnswersBySubjectId($subjectId, $byUser = FALSE) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getActiveAnswersBySubjectId($subjectId, $byUser);
  }

  /**
  * Get active subject ids by user id
  *
  * The return value is an array of all subjects the user has answered questions for;
  * the keys are subject ids and the values are answer timestamps.
  * If there are no answers, an empty array is returned.
  *
  * @param string $userId
  * @param boolean $asc
  * @return array
  */
  public function getActiveSubjectIdsByUser($userId, $asc = FALSE) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getActiveSubjectIdsByUser($userId, $asc);
  }

  /**
  * Get subject ids by user id
  *
  * The return value is an array of all subjects the user has answered questions for;
  * the keys are subject ids and the values are answer timestamps and deletion timestamp.
  * If there are no answers, an empty array is returned.
  *
  * @param string $userId
  * @param boolean $asc
  * @return array
  */
  public function getSubjectIdsByUser($userId, $asc = FALSE) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getSubjectIdsByUser($userId, $asc);
  }

  /**
   * Get answer sets (=filled out questionnaires)
   * @param array $filter a filter array of fieldname => fieldvalue combinations
   * @param ASC|DESC $sort sort order
   * @return array answer sets, key is the answer_set_id, empty array if nothing found
   */
  public function getAnswerSets($filter, $sort = 'ASC') {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getAnswerSets($filter, $sort);
  }

  /**
   * Mark an answer set as deleted
   * @param string $answerSetId answer set id of the answer
   * @return boolean TRUE on success
   */
  public function markAnswerSetDeleted($answerSetId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->markAnswerSetDeleted($answerSetId);
  }

  /**
   * Get answer options (=possible answers for questions)
   * @param array $filter a filter array of fieldname => fieldvalue combinations
   * @return array answer options, key is the answer_choice_id, empty array if nothing found
   */
  public function getAnswerOptions($filter) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getAnswerOptions($filter);
  }

  /**
  * Get active subject ids by user id filtered by meta key value conditions
  *
  * The return value is an array of all subjects the user has answered questions for;
  * the keys are subject ids and the values are answer timestamps.
  * If there are no answers, an empty array is returned.
  *
  * @param string $userId
  * @param string $metaKey
  * @param string $metaValue optional
  * @return array
  */
  public function getActiveSubjectIdsByMetaKeyValue($userId, $metaKey, $metaValue = NULL) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getActiveSubjectIdsByMetaKeyValue($userId, $metaKey, $metaValue);
  }

  /**
  * Get answer timestamp by user id and subject id
  *
  * If answers for the same subject by the same user should have different timestamps
  * (which shouldn't happen under normal circumstances), the latest timestamp is returned.
  * If there are no answers for the selected subject by the selected user,
  * the return value is 0.
  *
  * @param string $userId
  * @param string $subjectId
  * @return integer timestamp if available, 0 otherwise
  */
  public function getAnswerTimestampByUserAndSubject($userId, $subjectId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getAnswerTimestampByUserAndSubject($userId, $subjectId);
  }

  /**
  * Save answers by user id and subject id
  *
  * You need to provide the user and subject ids
  * as well as an array of answers in the following format:
  * array($questionId => $answer, ...)
  *
  * @param string $userId
  * @param string $subjectId
  * @param array $answers
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function saveAnswersByUserAndSubject($userId, $subjectId, $answers) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->saveAnswersByUserAndSubject($userId, $subjectId, $answers);
  }

  public function saveAnswersOfUserForSubject($userId, $subjectId, $answers, $answerSetId = NULL) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->saveAnswersOfUserForSubject(
      $userId, $subjectId, $answers, $answerSetId
    );
  }

  /**
  * Deactivate answers by user id
  *
  * @param string $userId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function deactivateAnswersByUserId($userId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->deactivateAnswersByUserId($userId);
  }

  /**
  * Reactivate answers by user id
  *
  * @param string $userId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function activateAnswersByUserId($userId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->activateAnswersByUserId($userId);
  }

  /**
  * Deactivate answers by subject id
  *
  * @param string $subjectId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function deactivateAnswersBySubjectId($subjectId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->deactivateAnswersBySubjectId($subjectId);
  }

  /**
  * Reactivate answers by subject id
  *
  * @param string $userId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function activateAnswersBySubjectId($subjectId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->activateAnswersBySubjectId($subjectId);
  }

  public function addMetaForAnswerSet($answerSetId, $key, $value) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->addMetaForAnswerSet($answerSetId, $key, $value);
  }

  public function getMetaForAnswerSet($answerSetId, $key = NULL) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getMetaForAnswerSet($answerSetId, $key);
  }

  /**
  * Returns an array with answer set ids of the current year for a given user
  *
  * @param string $userId
  * @return array
  */
  public function getAnswerSetsInCurrentYear($userId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getAnswerSetsInCurrentYear($userId);
  }

  /**
  * Retrieve answer sets for given surfer ids - ordered by their timestamp
  *
  * @param array $surferIds
  * @return array
  */
  public function getAnswerSetsForSurfers($surferIds) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->getAnswerSetsForSurfers($surferIds);
  }

  /**
  * Transfer "ownership" of answer set to given surferid
  *
  * @param array $answerSetIds
  * @param string $surferId
  * @return boolean TRUE on success, FALSE otherwise
  */
  public function transferAnswerSetsToSurfer($answerSetIds, $surferId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->transferAnswerSetsToSurfer($answerSetIds, $surferId);
  }

  /**
   * Deletes deprecated answer sets after merging
   *
   * @param array $answerSetIds
   * @param string $userId
   * @return boolean TRUE on success, FALSE otherwise
   */
  public function deleteDeprecatedAnswerSets($answerSetIds, $userId) {
    $databaseAccessObject = $this->getDatabaseAccessObject();
    return $databaseAccessObject->deleteDeprecatedAnswerSets($answerSetIds, $userId);
  }
}
