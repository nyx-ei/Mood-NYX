<?php

/**
 * @package    youtubesearch
 * @copyright  2024 NYX-EI {@link https://nyx-ei.tech}
 * @author     NYX-EI <help@nyx-ei.tech>
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/youtubesearch/lib.php');

require_login();

require_capability('local/youtubesearch:use', context_system::instance());

$PAGE->set_url(new moodle_url('/local/youtubesearch/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_youtubesearch'));
$PAGE->set_heading(get_string('pluginname', 'local_youtubesearch'));

$PAGE->requires->js_call_amd('local_youtubesearch/main', 'init');
$PAGE->requires->css('/local/youtubesearch/styles.css');

echo $OUTPUT->header();

$search_form = '
<form id="youtube-search-form">
    <input type="text" name="q" placeholder="' . get_string('search_placeholder', 'local_youtubesearch') . '">
    <button type="submit">' . get_string('search', 'local_youtubesearch') . '</button>
</form>
<div id="youtube-search-results"></div>
';

echo $search_form;

echo $OUTPUT->footer();