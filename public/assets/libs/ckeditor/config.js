/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.filebrowserBrowseUrl = '/assets/libs/pydio/?external_selector_type=ckeditor&relative_path=/files';
    config.filebrowserImageBrowseUrl = '/assets/libs/pydio/?external_selector_type=ckeditor&relative_path=/files';
    config.filebrowserUploadUrl = '/assets/libs/pydio/?external_selector_type=ckeditor&relative_path=/files';
};
