<?php

/**
 * @package    youtubesearch
 * @copyright  2024 NYX-EI {@link https://nyx-ei.tech}
 * @author     NYX-EI <help@nyx-ei.tech>
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/youtubesearch/lib.php');
require_login();

$query = required_param('q', PARAM_RAW);

$results = local_youtubesearch_youtube_search($query);

header('Content-Type: application/json');
echo json_encode($results);