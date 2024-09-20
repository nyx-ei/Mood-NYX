<?php
/**
 * @package youtubesearch
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/youtubesearch/lib.php');

$id = optional_param('id', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('youtubesearch', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $youtubesearch = $DB->get_record('youtubesearch', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    print_error('You must specify a course_module ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/youtubesearch/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($youtubesearch->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

if (has_capability('mod/youtubesearch:search', $context)) {
    $renderer = $PAGE->get_renderer('mod_youtubesearch');
    $searchform = new \mod_youtubesearch\output\search_form();
    echo $renderer->render_search_form($searchform);

    if (isset($_POST['search_query'])) {
        $youtube_api = new \mod_youtubesearch\youtube_api();
        $search_results = $youtube_api->search_videos($_POST['search_query']);
        echo $renderer->render_search_results($search_results);
    }
} else {
    echo $OUTPUT->notification(get_string('nopermissiontosearch', 'mod_youtubesearch'), 'error');
}

echo $OUTPUT->footer();