/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function(config){
	config.toolbarCanCollapse 	= true
	config.toolbar_Advanced 	= [
		[ 'Source', '-', 'Maximize' ],
		[ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ],
		[ 'Blockquote', '-', 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],
		[ 'Image', 'Table', 'SpecialChar', 'Smiley', '-', 'HorizontalRule', 'PageBreak' ],
		[ 'Format', 'Font', 'FontSize', 'BGColor', 'TextColor' ],
	];
	config.toolbar_Basic = [
		[ 'Maximize' ],
		[ 'Bold', 'Italic', 'Underline'],
		[ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],
	];
	config.toolbar 	= 'Basic';
	config.height 	= '200px';
	//config.filebrowserImageBrowseUrl = location.protocol+"//"+location.hostname+"/gmfinv/pds/libraries/js/kcfinder/browse.php?opener=ckeditor&type=images&dir=images/public";
	//config.filebrowserImageUploadUrl = location.protocol+"//"+location.hostname+"/gmfinv/pds/libraries/js/kcfinder/upload.php?opener=ckeditor&type=images";
};
