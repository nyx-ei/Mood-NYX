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
    switch($feature) {
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_SHOW_DESCRIPTION: return true;
        case FEATURE_GRADE_HAS_GRADE: return false;
        case FEATURE_BACKUP_MOODLE2: return true;
        default: return null;
    }
}

function get_youtube_id($url)
{
    $parts = parse_url($url);

    // Vérifie la présence de paramètres dans l'URL
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $qs);
        return $qs['v'] ?? $qs['vi'] ?? false;  // Récupère l'ID 'v' ou 'vi' ou retourne false
    }

    // Vérifie la présence du chemin dans l'URL
    if (!empty($parts['path'])) {
        $path = explode('/', trim($parts['path'], '/'));
        return end($path);  // Retourne le dernier segment du chemin
    }

    return false;
}
{
    $parts = parse_url($url);
    if(isset($parts['query'])){
        parse_str($parts['query'], $qs);
        if(isset($qs['v'])) return $qs['v'];
        else if(isset($qs['vi'])) return $qs['vi'];
    }
    if(isset($parts['path'])){
        $path = explode('/', trim($parts['path'], '/'));
        return $path[count($path)-1];
    }
    return false;
}