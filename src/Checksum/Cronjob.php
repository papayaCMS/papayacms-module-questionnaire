<?php
/**
* Questionnaire checksum cronjob
*
* detects questionnaire configuration changes based on a checksum and sends an alarm email as soon
* as a change is detected
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
* @package Wla
* @subpackage Fraud Detection
* @version $Id: Cronjob.php 2 2013-12-09 16:39:31Z weinert $
*/

/**
* Basic class includes
*/
require_once(PAPAYA_INCLUDE_PATH.'system/base_cronjob.php');
require_once(PAPAYA_INCLUDE_PATH.'system/sys_email.php');
require_once(dirname(__FILE__).'/../Overview.php');

/**
* Wla Fraud Detection cronjob module
*
* @package Commercial
* @subpackage Questionnaire
*/
class PapayaQuestionnaireChecksumCronjob extends base_cronjob {
  // @var array $editFields
  public $editFields = array(
    'checksum' => array(
      'Checksum',
      'isSomeText',
      TRUE,
      'input',
      255,
      'If the questionnaire configuration\'s checksum differs
       from this value, an email will be sent',
      '',
    ),
    'Email Configuration',
    'subject' => array(
      'Subject',
      'isSomeText',
      TRUE,
      'input',
      255,
      '',
      'Questionnaire Configuration Change detected!',
    ),
    'emails' => array(
      'Recipients',
      '',
      TRUE,
      'textarea',
      4,
      'One email address per line',
    ),
  );

  /**
  * Check execution parameters
  *
  * @return boolean
  */
  public function checkExecParams() {
    if (empty($this->data['emails']) || empty($this->data['checksum'])) {
      return FALSE;
    }
    return TRUE;
  }

  /**
  * Execution method for cronjob module
  *
  * @return integer 0
  */
  public function execute() {
    // get checksum of active questionnaire configuration
    $tablePrefix = $this->papaya()->options->getOption('PAPAYA_DB_TABLEPREFIX');
    $overview = new PapayaQuestionnaireOverview($tablePrefix);
    $checksum = $overview->getChecksum();

    if ($checksum != $this->data['checksum']) {
      // checksum does not equal expected value
      $this->cronOutput(
        sprintf(
          'Checksum changed! Expected "%s", found "%s"',
          $this->data['checksum'],
          $checksum
        )
      );
      // send alert email
      $addresses = explode("\n", $this->data['emails']);
      $mail = new email;
      foreach ($addresses as $address) {
        $mail->addAddress(trim($address));
      }
      $mail->setSubject($this->data['subject']);
      $mail->setBody(
        'Reference Checksum: ' . $this->data['checksum'] . LF .
        'Detected Checksum: ' . $checksum
      );
      $mail->send();
    }

    return 0;
  }

}
