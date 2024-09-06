<?php

/**
 * @package youtubevideo
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

defined('MOODLE_INTERNAL') || die();

function youtubevideo_add_instance($youtubevideo)
{
    global $DB;

    $youtubevideo->timecreated = time();
    $youtubevideo->timemodified = time();
    return $DB->insert_record('youtubevideo', $youtubevideo);
}

function youtubevideo_update_instance($youtubevideo)
{
    global $DB;

    $youtubevideo->timemodified = time();
    $youtubevideo->id = $youtubevideo->instance;
    return $DB->update_record('youtubevideo', $youtubevideo);
}

function youtubevideo_delete_instance($id)
{
    global $DB;

    if (!$youtubevideo = $DB->get_record('youtubevideo', array('id' => $id))) {
        return false;
    }
    $DB->delete_records('youtubevideo', array('id' => $youtubevideo->id));
    return true;
}

function youtubevideo_supports($feature)
{
    $features = [
        FEATURE_MOD_INTRO => true,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_GRADE_HAS_GRADE => false,
        FEATURE_BACKUP_MOODLE2 => true,
    ];
    return $features[$feature] ?? null;
}

function get_youtube_id($url)
{
    $parts = parse_url($url);
    
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $qs);
        return $qs['v'] ?? $qs['vi'] ?? false;  
    }
    
    if (!empty($parts['path'])) {
        $path = explode('/', trim($parts['path'], '/'));
        return end($path);  
    }
    return false;
}