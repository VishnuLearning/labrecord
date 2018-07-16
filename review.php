<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This page prints a review of a particular quiz attempt
 *
 * It is used either by the student whose attempts this is, after the attempt,
 * or by a teacher reviewing another's attempt during or afterwards.
 *
 * @package   mod_quiz
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');



$id = required_param('id', PARAM_INT);
global $DB;
list ($course, $cm) = get_course_and_cm_from_cmid($id, 'labrecord');
$labrecords = $DB->get_record('labrecord', array('id'=> $cm->instance), '*', MUST_EXIST);
require_course_login($course, true, $cm);
$quizzes = $DB->get_records_sql('SELECT quizid from {labrecord_quizzes} WHERE labrecordid = ?', array($cm->instance));
$count = 0;

$select = 'select max(uniqueid) as uniqueid, max(attempt) as attemptid, quiz, userid from {quiz_attempts} WHERE userid=? and quiz=? group by quiz, userid';

$url = new moodle_url('/mod/labrecord/view.php', array('id'=>$id));
if ($page !== 0) {
    $url->param('page', $page);
} else if ($showall) {
    $url->param('showall', $showall);
}
$PAGE->set_url($url);

$PAGE->set_title('lab record');
$output = $PAGE->get_renderer('mod_quiz');
function printattempt($attemptid) {
    $attemptobj = quiz_attempt::create($attemptid);
    //$page = $attemptobj->force_page_number_into_range($page);

    // Now we can validate the params better, re-genrate the page URL.
    
    //$PAGE->set_url($attemptobj->review_url(null, $page, $showall));

    $options = $attemptobj->get_display_options(true);

    // Check permissions - warning there is similar code in reviewquestion.php and
    // quiz_attempt::check_file_access. If you change on, change them all.
    if (!$attemptobj->is_own_attempt() ||  !$attemptobj->is_finished() ||  !$attemptobj->is_review_allowed()) return;

    // Load the questions and states needed by this page.
    $questionids = $attemptobj->get_slots();

    // Work out appropriate title and whether blocks should be shown.
    $strreviewtitle = '';

    // Set up the page header.
    
    // Work out some time-related things.
    $attempt = $attemptobj->get_attempt();
    $quiz = $attemptobj->get_quiz();
    
    $slots = $attemptobj->get_slots();
    $lastpage = true;

    // Arrange for the navigation to be displayed.
    echo $output->review_page($attemptobj, $slots, $page, $showall, $lastpage, $options, $summarydata=null);
}

foreach ($quizzes as $quiz) {
    $params = array($USER->id, $quiz->quizid);
    $attempts = $DB->get_records_sql($select, $params);
    foreach($attempts as $attempt) {
       printattempt($attempt->attemptid);
    }
    $count = $count + 1;
}

