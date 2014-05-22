<?php
require_once(dirname(__FILE__).'/bootstrap.php');

require_once(dirname(__FILE__).'/../src/Overview.php');

class PapayaQuestionnaireOverviewTest extends PapayaTestCase {

  private $_row1;
  private $_row2;
  private $_row3;

  public function setUp() {
    parent::setUp();
    if (!defined('DB_FETCHMODE_ASSOC')) {
      define('DB_FETCHMODE_ASSOC', 2);
    }

    $this->_row1 = array(
      'question_pool_id' => 1,
      'question_pool_name' => 'Test Pool 1',
      'question_pool_identifier' => 'p1',
      'question_group_id' => 11,
      'question_group_identifier' => 'g11',
      'question_group_position' => 1,
      'question_group_name' => 'Test Group 1',
      'question_group_text' => 'Test Group 1 Text',
      'question_group_min_answers' => 1,
      'question_group_subtitle' => 'Subtitle',
      'question_id' => 111,
      'question_identifier' => 'q111',
      'question_position' => 1,
      'question_type' => 'single_choice_5',
      'question_text' => 'Frage?',
      'question_answer_data' => '<data><data-element name="mandatory">0</data-element></data>',
      'answer_choice_id' => 1111,
      'answer_choice_text' => 'Antwort 1',
      'answer_choice_value' => 'positiv',
    );

    $this->_row2 = array(
      'question_pool_id' => 1,
      'question_pool_name' => 'Test Pool 1',
      'question_pool_identifier' => 'p1',
      'question_group_id' => 11,
      'question_group_identifier' => 'g11',
      'question_group_position' => 1,
      'question_group_name' => 'Test Group 1',
      'question_group_text' => 'Test Group 1 Text',
      'question_group_min_answers' => 1,
      'question_group_subtitle' => 'Subtitle',
      'question_id' => 111,
      'question_identifier' => 'q111',
      'question_position' => 1,
      'question_type' => 'single_choice_5',
      'question_text' => 'Frage?',
      'question_answer_data' => '<data><data-element name="mandatory">0</data-element></data>',
      'answer_choice_id' => 1112,
      'answer_choice_text' => 'Antwort 2',
      'answer_choice_value' => 'negativ',
    );

    $this->_row3 = array(
      'question_pool_id' => 2,
      'question_pool_name' => 'Test Pool 2',
      'question_pool_identifier' => 'p2',
      'question_group_id' => 21,
      'question_group_identifier' => 'g21',
      'question_group_position' => 1,
      'question_group_name' => 'Test Group 2',
      'question_group_text' => 'Test Group 2 Text',
      'question_group_min_answers' => 1,
      'question_group_subtitle' => 'Subtitle',
      'question_id' => 211,
      'question_identifier' => 'q211',
      'question_position' => 1,
      'question_type' => 'single_choice_5',
      'question_text' => 'Frage?',
      'question_answer_data' => '<data><data-element name="mandatory">1</data-element></data>',
      'answer_choice_id' => 2111,
      'answer_choice_text' => 'Antwort 1',
      'answer_choice_value' => 'negativ',
    );
  }

  private function _getOverviewObject() {
    $mockDbResult = $this->getMock('dbresult_base', array('fetchRow'));
    $mockDbResult
      ->expects($this->any())
      ->method('fetchRow')
      ->will($this->onConsecutiveCalls($this->_row1, $this->_row2, $this->_row3)
    );
    $mockDbAccess = $this->getMock('PapayaDatabaseAccess', array('queryFmt'), array(), '', FALSE);
    $mockDbAccess
      ->expects($this->once())
      ->method('queryFmt')
      ->will($this->returnValue($mockDbResult));

    $overview = new PapayaQuestionnaireOverview('');
    $overview->setDatabaseAccess($mockDbAccess);
    return $overview;
  }

  public function testGetChecksum() {
    $overview = $this->_getOverviewObject();
    $this->assertEquals('ms7nh0', $overview->getChecksum());
  }

  public function testGetHtml() {
    $overview = $this->_getOverviewObject();

    $expectedHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="robots" content="noindex, nofollow">
        <title>Overview of all Questionnaires</title>
        <meta name="MSSmartTagsPreventParsing" content="TRUE">
        <style type="text/css">
          body { font-size: 10pt; }
          h4 { margin-bottom: 0; }
          ul { margin-top: 0.25em; }
        </style>
        </head>
        <body>
        <h1>Overview of all Questionnaires [ms7nh0]</h1>
        <div><h2>Table of Contents</h2><ul style="margin-bottom: 4em;"><li><a href="#pool-1">Test Pool 1</a></li><li><a href="#pool-2">Test Pool 2</a></li></ul><h2 style="margin-top: 3em;"><a name="pool-1" style="color: #000;">Test Pool 1 [1y5oyts]</a></h2><div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;"><h3>g11: Test Group 1 [13u50mm]</h3><div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;"><h4>q111: Frage?  [qpsws6]</h4><div style="margin-left: 1em; padding-left: 1em;"><ul style="padding-left: 1em;"><li>Antwort 1 = <strong>positiv</strong></li><li>Antwort 2 = <strong>negativ</strong></li></ul></div></div></div><h2 style="margin-top: 3em;"><a name="pool-2" style="color: #000;">Test Pool 2 [tpbmpw]</a></h2><div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;"><h3>g21: Test Group 2 [eskjwv]</h3><div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;"><h4>q211: Frage? <strong>(X)</strong> [ehj32]</h4><div style="margin-left: 1em; padding-left: 1em;"><ul style="padding-left: 1em;"><li>Antwort 1 = <strong>negativ</strong></li></ul></div></div></div></div>
        </body>
        </html>';

    $this->assertEquals($expectedHtml, $overview->getHtml());
  }

  public function testGetXml() {
    $overview = $this->_getOverviewObject();

    $expectedXml = '
      <sheet>
        <header>
          <lines>
            <line class="headertitle">Overview of all Questionnaires [ms7nh0]</line>
          </lines>
        </header>
        <text>
          <div>
            <h2>Table of Contents</h2>
            <ul style="margin-bottom: 4em;">
              <li>
                <a href="#pool-1">Test Pool 1</a>
              </li>
              <li>
                <a href="#pool-2">Test Pool 2</a>
              </li>
            </ul>
            <h2 style="margin-top: 3em;">
              <a name="pool-1" style="color: #000;">Test Pool 1 [1y5oyts]</a>
            </h2>
            <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
              <h3>g11: Test Group 1 [13u50mm]</h3>
              <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
                <h4>q111: Frage?  [qpsws6]</h4>
                <div style="margin-left: 1em; padding-left: 1em;">
                  <ul style="padding-left: 1em;">
                    <li>Antwort 1 = <strong>positiv</strong></li>
                    <li>Antwort 2 = <strong>negativ</strong></li>
                  </ul>
                </div>
              </div>
            </div>
            <h2 style="margin-top: 3em;">
              <a name="pool-2" style="color: #000;">Test Pool 2 [tpbmpw]</a>
            </h2>
            <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
              <h3>g21: Test Group 2 [eskjwv]</h3>
              <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
                <h4>q211: Frage? <strong>(X)</strong> [ehj32]</h4>
                <div style="margin-left: 1em; padding-left: 1em;">
                  <ul style="padding-left: 1em;">
                    <li>Antwort 1 = <strong>negativ</strong></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </text>
      </sheet>
    ';

    $actualXml = $overview->getXml();

    $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
  }

  public function testGetXmlWithPrintLink() {
    $overview = $this->_getOverviewObject();
    $overview->setPrintLink('print.html');

    $expectedXml = '
      <sheet>
        <header>
          <lines>
            <line class="headertitle"><span style="float: right">[<a href="print.html" target="_blank">Druckansicht</a>]</span> Overview of all Questionnaires [ms7nh0]</line>
          </lines>
        </header>
        <text>
          <div>
            <h2>Table of Contents</h2>
            <ul style="margin-bottom: 4em;">
              <li>
                <a href="#pool-1">Test Pool 1</a>
              </li>
              <li>
                <a href="#pool-2">Test Pool 2</a>
              </li>
            </ul>
            <h2 style="margin-top: 3em;">
              <a name="pool-1" style="color: #000;">Test Pool 1 [1y5oyts]</a>
            </h2>
            <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
              <h3>g11: Test Group 1 [13u50mm]</h3>
              <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
                <h4>q111: Frage?  [qpsws6]</h4>
                <div style="margin-left: 1em; padding-left: 1em;">
                  <ul style="padding-left: 1em;">
                    <li>Antwort 1 = <strong>positiv</strong></li>
                    <li>Antwort 2 = <strong>negativ</strong></li>
                  </ul>
                </div>
              </div>
            </div>
            <h2 style="margin-top: 3em;">
              <a name="pool-2" style="color: #000;">Test Pool 2 [tpbmpw]</a>
            </h2>
            <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
              <h3>g21: Test Group 2 [eskjwv]</h3>
              <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
                <h4>q211: Frage? <strong>(X)</strong> [ehj32]</h4>
                <div style="margin-left: 1em; padding-left: 1em;">
                  <ul style="padding-left: 1em;">
                    <li>Antwort 1 = <strong>negativ</strong></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </text>
      </sheet>
    ';

    $actualXml = $overview->getXml();

    $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
  }

  public function testGetXmlExtended() {
    $overview = $this->_getOverviewObject();

    $expectedXml = '
      <sheet>
        <header>
          <lines>
            <line class="headertitle">Overview of all Questionnaires [ms7nh0]</line>
          </lines>
        </header>
        <text>
          <div>
            <h2>Table of Contents</h2>
            <ul style="margin-bottom: 4em;">
              <li>
                <a href="#pool-1">Test Pool 1</a>
              </li>
              <li>
                <a href="#pool-2">Test Pool 2</a>
              </li>
            </ul>
            <h2 style="margin-top: 3em;">
              <a name="pool-1" style="color: #000;">Test Pool 1 [1y5oyts]</a>
            </h2>
            <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
              <table style="border-collapse: collapse; font-size: 10px;">
                <tr>
                  <th style="border: 1px #ccc solid;">name</th>
                  <td style="border: 1px #ccc solid;">Test Pool 1</td>
                </tr>
                <tr>
                  <th style="border: 1px #ccc solid;">identifier</th>
                  <td style="border: 1px #ccc solid;">p1</td>
                </tr>
              </table>
              <h3>g11: Test Group 1 [13u50mm]</h3>
              <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
                <table style="border-collapse: collapse; font-size: 10px;">
                  <tr>
                    <th style="border: 1px #ccc solid;">identifier</th>
                    <td style="border: 1px #ccc solid;">g11</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">position</th>
                    <td style="border: 1px #ccc solid;">1</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">name</th>
                    <td style="border: 1px #ccc solid;">Test Group 1</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">text</th>
                    <td style="border: 1px #ccc solid;">Test Group 1 Text</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">min_answers</th>
                    <td style="border: 1px #ccc solid;">1</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">subtitle</th>
                    <td style="border: 1px #ccc solid;">Subtitle</td>
                  </tr>
                </table>
                <h4>q111: Frage?  [qpsws6]</h4>
                <div style="margin-left: 1em; padding-left: 1em;">
                  <table style="border-collapse: collapse; font-size: 10px;">
                    <tr>
                      <th style="border: 1px #ccc solid;">identifier</th>
                      <td style="border: 1px #ccc solid;">q111</td>
                    </tr>
                    <tr>
                      <th style="border: 1px #ccc solid;">position</th>
                      <td style="border: 1px #ccc solid;">1</td>
                    </tr>
                    <tr>
                      <th style="border: 1px #ccc solid;">type</th>
                      <td style="border: 1px #ccc solid;">single_choice_5</td>
                    </tr>
                    <tr>
                      <th style="border: 1px #ccc solid;">text</th>
                      <td style="border: 1px #ccc solid;">Frage?</td>
                    </tr>
                    <tr>
                      <th style="border: 1px #ccc solid;">answer_data</th>
                      <td style="border: 1px #ccc solid;">
                        <table style="border-collapse: collapse; font-size: 10px;">
                          <tr>
                            <th style="border: 1px #ccc solid;">mandatory</th>
                            <td style="border: 1px #ccc solid;">0</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                  <ul style="padding-left: 1em;">
                    <li>Antwort 1 = <strong>positiv</strong></li>
                    <li>Antwort 2 = <strong>negativ</strong></li>
                  </ul>
                </div>
              </div>
            </div>
            <h2 style="margin-top: 3em;">
              <a name="pool-2" style="color: #000;">Test Pool 2 [tpbmpw]</a>
            </h2>
            <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
              <table style="border-collapse: collapse; font-size: 10px;">
                <tr>
                  <th style="border: 1px #ccc solid;">name</th>
                  <td style="border: 1px #ccc solid;">Test Pool 2</td>
                </tr>
                <tr>
                  <th style="border: 1px #ccc solid;">identifier</th>
                  <td style="border: 1px #ccc solid;">p2</td>
                </tr>
              </table>
              <h3>g21: Test Group 2 [eskjwv]</h3>
              <div style="border-left: 1px #ccc dotted; margin-left: 1em; padding-left: 1em;">
                <table style="border-collapse: collapse; font-size: 10px;">
                  <tr>
                    <th style="border: 1px #ccc solid;">identifier</th>
                    <td style="border: 1px #ccc solid;">g21</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">position</th>
                    <td style="border: 1px #ccc solid;">1</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">name</th>
                    <td style="border: 1px #ccc solid;">Test Group 2</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">text</th>
                    <td style="border: 1px #ccc solid;">Test Group 2 Text</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">min_answers</th>
                    <td style="border: 1px #ccc solid;">1</td>
                  </tr>
                  <tr>
                    <th style="border: 1px #ccc solid;">subtitle</th>
                    <td style="border: 1px #ccc solid;">Subtitle</td>
                  </tr>
                </table>
                <h4>q211: Frage? <strong>(X)</strong> [ehj32]</h4>
                <div style="margin-left: 1em; padding-left: 1em;">
                  <table style="border-collapse: collapse; font-size: 10px;">
                    <tr>
                      <th style="border: 1px #ccc solid;">identifier</th>
                      <td style="border: 1px #ccc solid;">q211</td>
                    </tr>
                    <tr>
                      <th style="border: 1px #ccc solid;">position</th>
                      <td style="border: 1px #ccc solid;">1</td>
                    </tr>
                    <tr>
                      <th style="border: 1px #ccc solid;">type</th>
                      <td style="border: 1px #ccc solid;">single_choice_5</td>
                    </tr>
                    <tr>
                      <th style="border: 1px #ccc solid;">text</th>
                      <td style="border: 1px #ccc solid;">Frage?</td>
                    </tr>
                    <tr>
                      <th style="border: 1px #ccc solid;">answer_data</th>
                      <td style="border: 1px #ccc solid;">
                        <table style="border-collapse: collapse; font-size: 10px;">
                          <tr>
                            <th style="border: 1px #ccc solid;">mandatory</th>
                            <td style="border: 1px #ccc solid;">1</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                  <ul style="padding-left: 1em;">
                    <li>Antwort 1 = <strong>negativ</strong></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </text>
      </sheet>
    ';

    $actualXml = $overview->getXml(TRUE);

    $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml);
  }

}
