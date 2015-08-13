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

/**
 * @module moodle-atto_mediadrop-button
 */

/**
 * Atto text editor mediadrop plugin.
 *
 * @namespace M.atto_mediadrop
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */
Y.namespace('M.atto_mediadrop').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

    _dialogue: false,

    initializer: function() {

        this.addButton({
            icon: 'icon',
            iconComponent: 'atto_mediadrop',
            callback: this._displayDialogue
        });

        var self = this;
        document.addEventListener('atto_mediadrop_video_chosen', function(e) {
            self._dialogue.hide();
            self._insertContent(e.detail.url, e.detail.title);
        });
    },

    /**
     * Display the video chooser dialog.
     *
     * @private
     */
    _displayDialogue: function() {

        this._dialogue = this.getDialogue({
            headerContent: 'MediaDrop',
            width: '800px',
            focusAfterHide: true
        });

        var iframe = Y.Node.create('<iframe></iframe>');
        iframe.setStyles({
            height: '600px',
            border: 'none',
            width: '100%'
        });
        iframe.setAttribute('src', this._getIframeURL());

        this._dialogue.set('bodyContent', iframe)
                .show();
    },

    /**
     * Returns the URL to the video chooser frame
     *
     * @return {String}
     * @private
     */
    _getIframeURL: function() {

        return M.cfg.wwwroot + '/lib/editor/atto/plugins/mediadrop/popup/';
    },

    /**
     * Create link element and insert into the editor
     *
     * @param {String} url
     * @param {String} title
     * @private
     */
    _insertContent: function(url, title) {

        var html = '<a href="' + url + '">' + title + '</a>';
        this.editor.focus();
        this.get('host').insertContentAtFocusPoint(html);
        this.markUpdated();
    }

});
