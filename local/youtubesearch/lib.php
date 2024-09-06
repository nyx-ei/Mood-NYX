<?php

/**
 * @package    youtubesearch
 * @copyright  2024 NYX-EI {@link https://nyx-ei.tech}
 * @author     NYX-EI <help@nyx-ei.tech>
 */

defined('MOODLE_INTERNAL') || die();

function local_youtubesearch_extend_navigation(global_navigation $navigation) {
    global $USER;
    
    if (has_capability('local/youtubesearch:use', context_system::instance(), $USER->id)) {
        $node = navigation_node::create(
            get_string('pluginname', 'local_youtubesearch'),
            new moodle_url('/local/youtubesearch/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'youtubesearch',
            new pix_icon('i/search', '')
        );
        $node->showinflatnavigation = true;
        
        $moremenu = $navigation->find('home', navigation_node::TYPE_CUSTOM);
        if ($moremenu) {
            $moremenu->add_node($node);
        } else {
            $navigation->add_node($node);
        }
    }
}

function local_youtubesearch_youtube_search($query, $max_results = 10) {
    $api_key = get_config('local_youtubesearch', 'api_key');
    if (empty($api_key)) {
        return false;
    }

    $url = 'https://www.googleapis.com/youtube/v3/search';
    $params = [
        'part' => 'snippet',
        'q' => $query,
        'key' => $api_key,
        'maxResults' => $max_results,
        'type' => 'video'
    ];

    $full_url = $url . '?' . http_build_query($params);
    $response = file_get_contents($full_url);

    if ($response === false) {
        return false;
    }

    $data = json_decode($response, true);

    if (isset($data['items'])) {
        return $data['items'];
    }

    return false;
}