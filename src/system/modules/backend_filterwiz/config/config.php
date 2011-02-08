<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * This is the widget configuration file.
 *
 * PHP version 5
 * @copyright  Thyon Design 2008 
 * @author     John Brand <john.brand@thyon.com> 
 * @package    FilterWizard 
 * @license    GPL 
 * @filesource
 */

/**
 * Form Fields
 */
array_insert($GLOBALS['BE_FFL'],5, array
	(
		'filterWizard'   => 'FilterWizard',
	)
);

if (TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] 				= 'system/modules/backend_filterwiz/html/filterwizard.css'; 
	$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/backend_filterwiz/html/filterwizard.js'; 
}

?>