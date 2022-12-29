/**
 * @license Copyright (c) 2003-2022, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */

// The editor creator to use.
import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import Font from '@ckeditor/ckeditor5-font/src/font';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import UploadAdapter from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import CKFinder from '@ckeditor/ckeditor5-ckfinder/src/ckfinder';
import EasyImage from '@ckeditor/ckeditor5-easy-image/src/easyimage';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageResizeEditing from '@ckeditor/ckeditor5-image/src/imageresize/imageresizeediting';
import ImageResizeHandles from '@ckeditor/ckeditor5-image/src/imageresize/imageresizehandles';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
import Indent from '@ckeditor/ckeditor5-indent/src/indent';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import TextTransformation from '@ckeditor/ckeditor5-typing/src/texttransformation';
import CloudServices from '@ckeditor/ckeditor5-cloud-services/src/cloudservices';

import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';     // <--- ADDED
import SourceEditing from '@ckeditor/ckeditor5-source-editing/src/sourceediting';
import ExportWord from '@ckeditor/ckeditor5-export-word/src/exportword';
import ExportPdf from '@ckeditor/ckeditor5-export-pdf/src/exportpdf';

export default class ClassicEditor extends ClassicEditorBase {}

// Plugins to include in the build.
ClassicEditor.builtinPlugins = [
	Essentials,
	UploadAdapter,
	Autoformat,
	Bold,
	Italic,
	BlockQuote,
	CKFinder,
	CloudServices,
	EasyImage,
	Heading,
	Image,
	ImageResizeEditing,
	ImageResizeHandles,
	ImageCaption,
	ImageStyle,
	ImageToolbar,
	ImageUpload,
	Indent,
	Link,
	List,
	MediaEmbed,
	Paragraph,
	PasteFromOffice,
	Table,
	TableToolbar,
	TextTransformation,
	Alignment,// <--- ADDED
	SourceEditing,
	ExportWord,
	ExportPdf
];

const imageConfiguration = {
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
};

// Editor configuration.
ClassicEditor.defaultConfig = {
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
			'blockQuote',
			'insertTable',
			'image',
			'ImageResizeEditing',
			'ImageResizeHandles',
			'uploadImage',
			'mediaEmbed',
			'undo',
			'redo',
			'ExportWord',
			'ExportPdf',
			'SourceEditing',
		],
	},
	image: imageConfiguration,
	table: {
		contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
	},
	// This value must be kept in sync with the language defined in webpack.config.js.
	language: 'en',
	// resizeOptions: [
	// 	{
	// 		name: 'resizeImage:original',
	// 		value: null,
	// 		label: 'Original',
	// 	},
	// 	{
	// 		name: 'resizeImage:40',
	// 		value: '40',
	// 		label: '40%',
	// 	},
	// 	{
	// 		name: 'resizeImage:50',
	// 		value: '50',
	// 		icon: '50%',
	// 	},
	// 	{
	// 		name: 'resizeImage:60',
	// 		value: '60',
	// 		label: '60%',
	// 	},
	// 	{
	// 		name: 'resizeImage:75',
	// 		value: '75',
	// 		icon: '75%',
	// 	},
	// ],
	plugins: [ window.HtmlEmbed ],
	ckfinder: {
		// Upload the images to the server using the CKFinder QuickUpload command.
		uploadUrl:
			'/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json',
		options: {
			resourceType: 'Images',
		},
		openerMethod: 'popup',
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
};