<?php

/**
 * @package youtubesearch
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/youtubesearch/lib.php');


$video_id = required_param('video_id', PARAM_TEXT);
$course_id = required_param('course_id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);
require_login($course);

$context = context_course::instance($course_id);
require_capability('mod/youtubesearch:addvideo', $context);

$youtubesearch = new youtubesearch();
$result = $youtubesearch->add_video_to_course($video_id, $course_id);

if ($result) {
    redirect(new moodle_url('/course/view.php', array('id' => $course_id)), get_string('video_added_success', 'mod_youtubesearch'));
} else {
    print_error(get_string('video_add_failed', 'mod_youtubesearch'));
}