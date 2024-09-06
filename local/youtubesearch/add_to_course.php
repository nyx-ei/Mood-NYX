<?php

/**
 * @package    youtubesearch
 * @copyright  2024 NYX-EI {@link https://nyx-ei.tech}
 * @author     NYX-EI <help@nyx-ei.tech>
 */

require_once(__DIR__ . '/../../config.php');
require_login();

$video_id = required_param('video_id', PARAM_ALPHANUMEXT);
$course_id = required_param('course_id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $course_id), '*', MUST_EXIST);
$context = context_course::instance($course->id);
require_capability('moodle/course:manageactivities', $context);

// Add the YouTube video as a URL resource to the course
$module = $DB->get_record('modules', array('name' => 'url'), '*', MUST_EXIST);

$url_instance = new stdClass();
$url_instance->course = $course_id;
$url_instance->name = "YouTube Video: " . $video_id;
$url_instance->intro = "";
$url_instance->introformat = FORMAT_HTML;
$url_instance->externalurl = "https://www.youtube.com/watch?v=" . $video_id;
$url_instance->display = RESOURCELIB_DISPLAY_EMBED;
$url_instance->displayoptions = serialize(array(
    'printheading' => 1,
    'printintro' => 0,
    'printlastmodified' => 1,
    'width' => 640,
    'height' => 360,
    'popupwidth' => 620,
    'popupheight' => 450
));
$url_instance->timemodified = time();
$url_instance->section = 0; // Add to the first section of the course

$url_instance->id = $DB->insert_record('url', $url_instance);

$module_instance = new stdClass();
$module_instance->course = $course_id;
$module_instance->module = $module->id;
$module_instance->instance = $url_instance->id;
$module_instance->section = 0;
$module_instance->added = time();

$module_instance->id = $DB->insert_record('course_modules', $module_instance);

// Update the section
$section = $DB->get_record('course_sections', array('course' => $course_id, 'section' => 0));
$section->sequence = trim($section->sequence . ',' . $module_instance->id, ',');
$DB->update_record('course_sections', $section);

rebuild_course_cache($course_id);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => get_string('video_added', 'local_youtubesearch')]);