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

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'atto_mediadrop';
$plugin->version = 2015081303;
$plugin->requires = 2013111800; // MOODLE_26_STABLE
$plugin->maturity = MATURITY_STABLE;
