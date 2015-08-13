<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    atto_mediadrop
 * @copyright  2015 Anthony Kuske <www.anthonykuske.com>
 *             Based on tinymce mediacoreinsert plugin https://github.com/mediacore/mediacore-moodle/tree/fe40bf8df4
 *             and atto_mediacore plugin https://github.com/mediacore/mediacore-moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace atto_mediadrop\local;

class mediadrop
{
    public $videosperpage = 6;

    private $url;
    private $pluginname = 'atto_mediadrop';

    public function __construct() {

        $this->url = rtrim($this->get_config('mediadropurl'), '/');
    }

    /**
     * Return a configuration setting from the plugin.
     *
     * @param  string $key Name of the setting
     * @return mixed
     * @throws \dml_exception
     */
    public function get_config($key) {

        return get_config($this->pluginname, $key);
    }

    /**
     * Return a language string from the plugin.
     *
     * @param  string $key Name of the string
     * @return string The localized string
     * @throws \coding_exception
     */
    public function get_string($key) {

        return get_string($key, $this->pluginname);
    }

    /**
     * Fetch the list of media from the MediaDrop API
     *
     * @param  array $data Parameters to be passed to the API
     * @return object
     */
    public function fetch_media_list(array $data) {

        $uri = $this->url . '/api/media?' . http_build_query($data);
        return json_decode(file_get_contents($uri));
    }

    /**
     * Because the API doesn't provide the embed URL separately (obscures it in the
     * iframe src) and instead supplies a permalink which can differ (e.g. podcasts)
     * from the direct play URL, we need to intercept podcast URIs and reformat them
     * so that the iframe can play it properly; if we pass the given permalink e.g.
     *
     * http://demo.mediacore.tv/podcasts/imperial-rome-and-ostia/the-construction-of-imperial-rome
     *
     * straight through, the iframe comes up 404. We need to replace the "podcasts"
     * with "media" and strip out the second portion (third portion is the slug) of
     * the URI entirely so that the above example would read:
     *
     * http://demo.mediacore.tv/media/the-construction-of-imperial-rome
     *
     * @param string $url
     * @return string
     */
    public function get_embeddable_url($url) {

        $podcast = explode('/podcasts/', $url);
        if ($podcast[1]) {
            $uri = explode('/', $podcast[1]);
            $url = $podcast[0] . '/media/' . $uri[1];
        }
        return $url;
    }

    /**
     * Returns a duration formatted as HH:MM:SS given a duration in seconds.
     *
     * @param  int      $sec        Duration in seconds
     * @param  boolean  $padhours   If true, number of hours will have a leading zero if less than 10.
     * @return string               Formatted duration
     */
    public function format_seconds($sec, $padhours = false) {

        // start with a blank string
        $hms = '';

        // do the hours first: there are 3600 seconds in an hour, so if we divide
        // the total number of seconds by 3600 and throw away the remainder, we're
        // left with the number of hours in those seconds
        $hours = intval(intval($sec) / 3600);

        // add hours to $hms (with a leading 0 if asked for)
        $hms .= $padhours ? str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' : $hours . ':';

        // dividing the total seconds by 60 will give us the number of minutes
        // in total, but we're interested in *minutes past the hour* and to get
        // this, we have to divide by 60 again and then use the remainder
        $minutes = intval(($sec / 60) % 60);

        // add minutes to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, '0', STR_PAD_LEFT). ':';

        // seconds past the minute are found by dividing the total number of seconds
        // by 60 and using the remainder
        $seconds = intval($sec % 60);

        // add seconds to $hms (with a leading 0 if needed)
        $hms .= str_pad($seconds, 2, '0', STR_PAD_LEFT);

        // done!
        return $hms;
    }

    /**
     * Returns how long ago a timestamp was.
     * e.g. "2 days ago"
     *
     * @param  int $timestamp
     * @return string
     */
    public function format_relative_time($timestamp) {

        $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
        $lengths = array(60, 60, 24, 7, 4.35, 12, 10);

        $now = time();

        $difference = $now - $timestamp;

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if ($difference != 1) {
            $periods[$j] .= 's';
        }

        return "{$difference} {$periods[$j]} ago ";
    }
}
