<?php

/**
 * @package youtubesearch
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */


defined('MOODLE_INTERNAL') || die();

function youtubesearch_add_instance($youtubesearch) {
    global $DB;
    $youtubesearch->timecreated = time();
    $youtubesearch->timemodified = time();
    return $DB->insert_record('youtubesearch', $youtubesearch);
}

function youtubesearch_update_instance($youtubesearch) {
    global $DB;
    $youtubesearch->timemodified = time();
    $youtubesearch->id = $youtubesearch->instance;
    return $DB->update_record('youtubesearch', $youtubesearch);
}

function youtubesearch_delete_instance($id) {
    global $DB;
    if (!$youtubesearch = $DB->get_record('youtubesearch', array('id' => $id))) {
        return false;
    }
    $DB->delete_records('youtubesearch', array('id' => $youtubesearch->id));
    return true;
}

class youtubesearch {
    public function add_video_to_course($video_id, $course_id) {
        global $DB;
        $record = new stdClass();
        $record->course = $course_id;
        $record->name = 'YouTube Video';
        $record->intro = 'Added from YouTube search';
        $record->introformat = FORMAT_HTML;
        $record->timecreated = time();
        $record->timemodified = time();
        $record->video_id = $video_id;
        return $DB->insert_record('youtubesearch', $record);
    }
}