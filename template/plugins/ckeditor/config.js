/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
  	
	config.toolbar_Default =
	  [                                                                    
	    { name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] },
	    { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },	    
	    { name: 'insert', items : [ 'CodeSnippet','Image','Iframe','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },  
	  	{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },	
	  	'/',
	  	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	  	{ name: 'links', items : [ 'Link','Unlink' ] },
	    { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv']},		  	
	  	{ name: 'colors', items : [ 'TextColor','BGColor' ] },
	  	{ name: 'about', items : [ 'About' ] }  			
	  ]; 
  
  
  config.toolbar_Full =
  [                                                                    
    { name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] },
    { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
    { name: 'align', items: ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
    { name: 'insert', items : [ 'CodeSnippet','Image','Iframe','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },  
  	{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },	
  	'/',
  	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
  	{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
    { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv']},	
  	{ name: 'styles', items : [ 'Format' ] },
  	{ name: 'colors', items : [ 'TextColor','BGColor' ] },
  	{ name: 'about', items : [ 'About' ] }  			
  ];  
  
  config.toolbar_Rtl =
  [                                                                                    
  	{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
    { name: 'insert', items : [ 'CodeSnippet','Image','Iframe','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
    { name: 'clipboard', items : [ 'PasteFromWord', 'PasteText'] },
    { name: 'tools', items : [ 'ShowBlocks','Maximize'] },	
  	'/',  	  	    	  	  	
  	{ name: 'about', items : [ 'About' ]},
    { name: 'colors', items : [ 'BGColor', 'TextColor'] },
    { name: 'paragraph', items : ['CreateDiv','Blockquote','-','Indent','Outdent','-','BulletedList','NumberedList']},
    { name: 'links', items : [ 'Unlink','Link'] },
    { name: 'basicstyles', items : [ 'RemoveFormat','-','Superscript','Subscript','Strike','Underline','Italic','Bold' ] }  			
  ]; 
  
  config.toolbar_RtlFull =
  [                                                                                    
  	{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
    { name: 'insert', items : [ 'CodeSnippet','Image','Iframe','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
    { name: 'align', items: ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
    { name: 'clipboard', items : [ 'PasteFromWord', 'PasteText'] },
    { name: 'tools', items : [ 'ShowBlocks','Maximize'] },	
  	'/',  	  	    	  	  	
  	{ name: 'about', items : [ 'About' ]},
    { name: 'colors', items : [ 'BGColor', 'TextColor'] },
    { name: 'styles', items : [ 'Format' ] },
    { name: 'paragraph', items : ['CreateDiv','Blockquote','-','Indent','Outdent','-','BulletedList','NumberedList']},
    { name: 'links', items : [ 'Anchor','Unlink','Link'] },
    { name: 'basicstyles', items : [ 'RemoveFormat','-','Superscript','Subscript','Strike','Underline','Italic','Bold' ] }  			
  ];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar. //Maximize
	config.removeButtons = 'CreateDiv,PasteText,ShowBlocks,Save,Preview,Flash,PageBreak,Paste,Cut,Copy,Redo,Undo,Subscript,Superscript,Templates';
  config.extraPlugins = 'colorbutton,codesnippet';  
  config.disableNativeSpellChecker = false;
  config.forcePasteAsPlainText = true;
  config.enterMode = CKEDITOR.ENTER_BR;
  config.contentsLangDirection = app_language_text_direction;
      
  config.imageUploadUrl = app_cfg_ckeditor_images;  
  
  config.codeSnippet_languages = {
    html: 'HTML',
    css: 'CSS',
    javascript: 'JavaScript',
    php: 'PHP',    
    sql: 'SQL',
    xml: 'XML',
    xhtml: 'XHTML'
  };
};
