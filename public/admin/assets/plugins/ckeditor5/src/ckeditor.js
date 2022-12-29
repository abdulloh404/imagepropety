// The editor creator to use.
import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';

import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import UploadAdapter from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import EasyImage from '@ckeditor/ckeditor5-easy-image/src/easyimage';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';

import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';     // <--- ADDED

export default class ClassicEditor extends ClassicEditorBase {}

// Plugins to include in the build.
ClassicEditor.builtinPlugins = [
    Essentials,
    UploadAdapter,
    Autoformat,
    Bold,
    Italic,
    BlockQuote,
    EasyImage,
    Heading,
    Image,
    ImageCaption,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    Link,
    List,
    Paragraph,
    Alignment                                                            // <--- ADDED
];

// Editor configuration.

ClassicEditor.create(document.querySelector('textarea.ckeditor5'), {
	//removePlugins: ['imageUpload', 'mediaEmbed'],
	toolbar: {
		items: [
			'heading',
			'|',
			'bold',
			'italic',
			'link',
			'bulletedList',
			'numberedList',
			'|',
			'outdent',
			'indent',
			'|',
			'uploadImage',
			'blockQuote',
			'insertTable',
			'mediaEmbed',
			'undo',
			'redo',
		],
	},
	image: {
		toolbar: [
			'imageStyle:inline',
			'imageStyle:block',
			'imageStyle:side',
			'|',
			'toggleImageCaption',
			'imageTextAlternative',
		],
		//resizeUnit: 'px',
		resizeOptions: [
			{
				name: 'resizeImage:original',
				value: null,
				label: 'Original',
			},
			{
				name: 'resizeImage:40',
				value: '40',
				label: '40%',
			},
			{
				name: 'resizeImage:50',
				value: '50',
				icon: '50%',
			},
			{
				name: 'resizeImage:60',
				value: '60',
				label: '60%',
			},
			{
				name: 'resizeImage:75',
				value: '75',
				icon: '75%',
			},
		],
	},
	table: {
		contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
	},
	// This value must be kept in sync with the language defined in webpack.config.js.
	language: 'en',
	resizeOptions: [
		{
			name: 'resizeImage:original',
			value: null,
			label: 'Original',
		},
		{
			name: 'resizeImage:40',
			value: '40',
			label: '40%',
		},
		{
			name: 'resizeImage:50',
			value: '50',
			icon: '50%',
		},
		{
			name: 'resizeImage:60',
			value: '60',
			label: '60%',
		},
		{
			name: 'resizeImage:75',
			value: '75',
			icon: '75%',
		},
	],
	//plugins: [ window.HtmlEmbed ],
	ckfinder: {
		// Upload the images to the server using the CKFinder QuickUpload command.
		uploadUrl:
			'<?php echo base_url();?>/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json',
		options: {
			resourceType: 'Images',
		},
		openerMethod: 'popup',
	},
	htmlEmbed: {
		showPreviews: true,
		sanitizeHtml: (inputHtml) => {
			// Strip unsafe elements and attributes, e.g.:
			// the `<script>` elements and `on*` attributes.
			const outputHtml = sanitize(inputHtml);

			return {
				html: outputHtml,
				// true or false depending on whether the sanitizer stripped anything.
				hasChanged: true,
			};
		},
	},
	// plugins: [ window.HtmlEmbed ],
	// toolbar: [
	// 	'heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', 'link', 'htmlEmbed', 'image', 'resizeImage'
	// ],
	heading: {
		options: [
			{
				model: 'paragraph',
				title: 'Paragraph',
				class: 'ck-heading_paragraph',
			},
			{
				model: 'heading1',
				view: 'h1',
				title: 'Heading 1',
				class: 'ck-heading_heading1',
			},
			{
				model: 'heading2',
				view: 'h2',
				title: 'Heading 2',
				class: 'ck-heading_heading2',
			},
			{
				model: 'heading3',
				view: 'h3',
				title: 'Heading 3',
				class: 'ck-heading_heading3',
			},
			{
				model: 'heading4',
				view: 'h4',
				title: 'Heading 4',
				class: 'ck-heading_heading4',
			},
			{
				model: 'heading5',
				view: 'h5',
				title: 'Heading 5',
				class: 'ck-heading_heading5',
			},
			{
				model: 'heading6',
				view: 'h6',
				title: 'Heading 6',
				class: 'ck-heading_heading6',
			},
		],
	},
})
.then((editor) => {
    // editor.execute( 'ckfinder' );
    window.editor = editor;
})
.catch((err) => {
    console.error(err.stack);
});