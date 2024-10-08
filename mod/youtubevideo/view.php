<?php

/**
 * @package youtubevideo
 * 
 * @copyright 2024 NYX-EI {@link http://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/youtubevideo/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$y = optional_param('y', 0, PARAM_INT); //youtube instance ID

if ($id) {
    $cm = get_coursemodule_from_id('youtubevideo', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $youtubevideo = $DB->get_record('youtubevideo', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($y) {
    $youtubevideo = $DB->get_record('youtubevideo', array('id' => $y), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $youtubevideo->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('youtubevideo', $youtubevideo->id, $course->id, false, MUST_EXIST);
} else {
    throw new moodle_exception('missingidandcmid', 'youtubevideo');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/youtubevideo/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($youtubevideo->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$PAGE->requires->css('/mod/youtubevideo/styles.css');

$output = $PAGE->get_renderer('mod_youtubevideo');

echo $output->header();

echo $output->manage_link($course->id, $context);

echo $output->heading(format_string($youtubevideo->name));

echo $output->module_intro(format_module_intro('youtubevideo', $youtubevideo, $cm->id));

$youtube_id = get_youtube_id($youtubevideo->youtubeurl);
echo $output->youtube_video($youtube_id);

echo $output->footer();
