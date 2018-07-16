<?php
/**
 * Labrecord module main User Interface
 *
 * @package mod_labrecord
 * @copyright 2017 Abhinav Dayal <abhinav.dayal@vishnu.edu.in>
 */

require('../../config.php');
require_once('lib.php');
global $DB;
$id = required_param('id', PARAM_INT);
list ($course, $cm) = get_course_and_cm_from_cmid($id, 'labrecord');
$labrecords = $DB->get_record('labrecord', array('id'=> $cm->instance), '*', MUST_EXIST);
require_course_login($course, true, $cm);

$quizzes = $DB->get_records_sql('SELECT quizid from {labrecord_quizzes} WHERE labrecordid = ?', array($cm->instance));
$count = 0;

$select = <<<EOT
SELECT
    qn.name,
    qas.timecreated,
    qa.maxmark,
    qa.minfraction,
    qas.fraction,
    qa.questionsummary,
    qa.responsesummary
	 
FROM (select max(uniqueid) as uniqueid, max(attempt) as attempt, quiz, userid from {quiz_attempts} group by quiz, userid) quiza
INNER JOIN {question_attempts} qa ON qa.questionusageid = quiza.uniqueid
INNER JOIN (select max(fraction) as fraction, max(timecreated) as timecreated, questionattemptid from {question_attempt_steps} group by questionattemptid) qas ON qas.questionattemptid = qa.id
INNER JOIN {question} qn ON qa.questionid = qn.id
 
WHERE quiza.userid=? and quiza.quiz=?

ORDER BY quiza.userid, quiza.attempt, qa.slot
EOT;

foreach ($quizzes as $quiz) {
    $params = array($USER->id, $quiz->quizid);
    print_object($params);
    $questions = $DB->get_records_sql($select, $params);
    foreach($questions as $question) {
        echo $question->name;
        echo $question->questionsummary;
        echo $question->responsesummary;
        echo $question->maxmark * $question->fraction;
        echo '---------------------------<br/>';
    }
    $count = $count + 1;
}
?>
