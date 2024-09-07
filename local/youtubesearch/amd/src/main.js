define(['jquery'], function($) {
    return {
        init: function() {
            $('#youtube-search-form').on('submit', function(e) {
                e.preventDefault();
                var query = $('input[name="q"]').val();
                $.ajax({
                    url: M.cfg.wwwroot + '/local/youtubesearch/search.php',
                    data: { q: query },
                    dataType: 'json',
                    success: function(results) {
                        var html = '';
                        if (results && results.length > 0) {
                            results.forEach(function(video) {
                                html += '<div class="youtube-video-item">';
                                html += '<img src="' + video.snippet.thumbnails.default.url + '" alt="' + video.snippet.title + '">';
                                html += '<h3>' + video.snippet.title + '</h3>';
                                html += '<button onclick="previewVideo(\'' + video.id.videoId + '\')">Preview</button>';
                                html += '<button onclick="addToCourse(\'' + video.id.videoId + '\')">Add to course</button>';
                                html += '</div>';
                            });
                        } else {
                            html = '<p>' + M.util.get_string('noresults', 'local_youtubesearch') + '</p>';
                        }
                        $('#youtube-search-results').html(html);
                    },
                    error: function() {
                        $('#youtube-search-results').html('<p>An error occurred while searching.</p>');
                    }
                });
            });

            window.previewVideo = function(videoId) {
                var previewUrl = 'https://www.youtube.com/embed/' + videoId;
                window.open(previewUrl, 'YouTube Preview', 'width=640,height=360');
            };

            window.addToCourse = function(videoId) {
                var courseId = M.cfg.courseId;
                $.ajax({
                    url: M.cfg.wwwroot + '/local/youtubesearch/add_to_course.php',
                    data: { video_id: videoId, course_id: courseId },
                    dataType: 'json',
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            alert(M.util.get_string('video_added', 'local_youtubesearch'));
                        } else {
                            alert(M.util.get_string('error_adding_video', 'local_youtubesearch'));
                        }
                    },
                    error: function() {
                        alert(M.util.get_string('error_adding_video', 'local_youtubesearch'));
                    }
                });
            };
        }
    };
});