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

require(__DIR__ . '/../../../../../../config.php');

$mediadrop = new \atto_mediadrop\local\mediadrop();

// Get parameters
$currentpage = optional_param('page', 1, PARAM_INT);
$searchquery = optional_param('search', '', PARAM_RAW);
$elementid = optional_param('elementid', '', PARAM_TEXT); // The editor in the parent window

$offset = ($currentpage - 1) * $mediadrop->videosperpage;

$result = $mediadrop->fetch_media_list(array(
  'type' => 'video',
  'limit' => $mediadrop->videosperpage,
  'offset' => $offset,
  'search' => $searchquery
));

$videos = $result->media;
$totalvideos = $result->count;
$totalpages = ceil($totalvideos / $mediadrop->videosperpage);

$previousurl = ($offset <= 0) ? false : "?page=" . ($currentpage - 1 ) . "&amp;search={$searchquery}";
$nexturl = ($currentpage >= $totalpages) ? false : "?page=" . ($currentpage + 1 ) . "&amp;search={$searchquery}";

?><!DOCTYPE html>
<html dir="ltr" lang="en">
<head>

    <link rel="stylesheet" type="text/css" href="css/popup.css"/>

    <title><?php echo $mediadrop->get_string('popuptitle'); ?></title>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>
<body>

<header>
    <div class="mdrop-pagination">

        <!-- Previous page button -->
        <a href="<?php echo ($previousurl ? $previousurl : '#'); ?>"
           class="mdrop-btn mdrop-prev <?php echo ($previousurl ? '' : 'mdrop-disabled'); ?>"
           title="Go to Previous Page">
            &#8656;
        </a>

        <!-- Next page button -->
        <a href="<?php echo ($nexturl ? $nexturl : '#'); ?>"
           class="mdrop-btn mdrop-next <?php echo ($nexturl ? '' : 'mdrop-disabled'); ?>"
           title="Go to Next Page">
            &#8658;
        </a>

    </div>

    <div class="mdrop-title"><?php echo $mediadrop->get_string('popuptitle'); ?></div>

</header>

<form class="mdrop-search-form" action="" method="get">
    <?php
    if (!empty($searchquery)) {
        echo '<a class="mdrop-clear-search" href="?search=">x</a>';
    }
    ?>
    <input type="text" name="search" class="mdrop-search-input" placeholder="Search" value="<?php p($searchquery); ?>" />
</form>

<div class="mdrop-content">

    <?php

    if ($totalvideos < 1) {
        ?>
        <div class="mdrop-message">
            <img src="images/zero-state.png" alt="zero state">
            <h2><?php echo $mediadrop->get_string('nomedia'); ?></h2>
            <p><?php echo $mediadrop->get_string('nomediadesc'); ?></p>
        </div>
        <?php
    }

    foreach ($videos as $video) {

        $embeddableurl = s($mediadrop->get_embeddable_url($video->url));

        $safetitle = str_replace('"', '', $video->title);
        $safetitle = str_replace("'", '', $safetitle);
        $safetitle = s($safetitle);

        $onclick = "insertVideo('{$embeddableurl}', '{$safetitle}');";

        ?>
        <div class="mdrop-media mdrop-clearfix mdrop-video">

            <div class="mdrop-thumbnail">

                <a href="#" onclick="<?php echo $onclick; ?>">
                    <img src="<?php p($video->thumbs->s->url); ?>" alt="<?php p($video->title); ?>" />
                    <span class="mdrop-border"></span>
                </a>

                <div class="mdrop-overlay">
                    <?php
                    if (isset($video->duration)) {
                        echo '<span class="mdrop-length">' . $mediadrop->format_seconds($video->duration, true) . '</span>';
                    }
                    ?>
                    <span class="mdrop-icon"></span>
                </div>

            </div> <!-- end .mdrop-thumbnail -->

            <div class="mdrop-info">

                <h3><a href="#" onclick="<?php echo $onclick; ?>"><?php p($video->title); ?></a></h3>

                <span class="mdrop-date">
                    <?php echo $mediadrop->format_relative_time(strtotime($video->publish_on)) ?>
                </span>

            </div> <!-- end .mdrop-info -->

            <div class="mdrop-add">

                <span class="mdrop-btn mdrop-add-btn">
                    <a href="#" onclick="<?php echo $onclick; ?>">
                        <span class="mdrop-icon"></span>
                        Add
                    </a>
                </span>
            </div> <!-- end .mdrop-add -->

        </div> <!-- end .mdrop-media -->

        <?php
    } // end foreach
    ?>

</div> <!-- end .mdrop-content -->

<footer>Page <?php echo $currentpage; ?> of <?php echo $totalpages; ?></footer>

<script>
function insertVideo(url, title) {
    var event = new CustomEvent('atto_mediadrop_video_chosen', {
        detail: {
            url: url,
            title: title
        }
    });
    window.parent.document.dispatchEvent(event);
    return false;
}
</script>

</body>
</html>
