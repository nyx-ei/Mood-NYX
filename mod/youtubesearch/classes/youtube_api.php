<?php

namespace mod_youtubesearch;

/**
 * @package youtubevideo
 * @copyright 2024 NYX-EI {@link https://nyx-ei.tech}
 * @author NYX-EI <help@nyx-ei.tech>
 */

defined('MOODLE_INTERNAL') || die();

class youtube_api {

    private $api_key;

    public function __construct() {
        $this->api_key = get_config('mod_youtubesearch', 'api_key');
    }

    public function search_videos($query, $max_results = 10) {
        $url = 'https://www.googleapis.com/youtube/v3/search';
        $params = [
            'part' => 'snippet',
            'q' => urlencode($query),
            'maxResults' => $max_results,
            'type' => 'video',
            'key' => $this->api_key
        ];

        $response = file_get_contents($url . '?' . http_build_query($params));
        $results = json_decode($response, true);

        $videos = [];
        foreach ($results['items'] as $item) {
            $videos[] = [
                'id' => $item['id']['videoId'],
                'title' => $item['snippet']['title'],
                'thumbnail' => $item['snippet']['thumbnails']['medium']['url'],
                'description' => $item['snippet']['description']
            ];
        }

        return $videos;
    }
}