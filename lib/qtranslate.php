<?php
/**
 * qTranslate PHP extension for SimpleSEO Pack
 *
 * Includes utility functions loaded only if qTranslate is available
 *
 * @version 1.0
 */

/*

Copyright (c) 2011, Vito Tardia.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

/**
 * Adds a switch-language button to the metabox title bar
 */
function qtrans_sseo_createTitlebarButton($parent, $language, $target, $id) {
	
	global $q_config;
	$html = "
		jQuery('#".$parent." .handlediv').after('<div class=\"qtranslate_lang_div\" id=\"".$id."\"><img alt=\"".$language."\" title=\"".$q_config['language_name'][$language]."\" src=\"".WP_CONTENT_URL.'/'.$q_config['flag_location'].$q_config['flag'][$language]."\" /></div>');
		jQuery('#".$id."').click(function() {SSEO.qTrans.switch_postbox('".$parent."','_sseo_meta_keywords','".$language."');});
		jQuery('#".$id."').click(function() {SSEO.qTrans.switch_postbox('".$parent."','_sseo_meta_description','".$language."');});
		";
	return $html;

} // end function

/**
 * Clones the "description" textarea
 */
function qtrans_sseo_createTextArea($parent, $language, $target) {
	
	global $q_config;
	$html = "
		jQuery('#".$target."').after('<textarea name=\"qtrans_textarea_".$target."_".$language."\" id=\"qtrans_textarea_".$target."_".$language."\"></textarea>');
		jQuery('#qtrans_textarea_".$target."_".$language."').attr('cols', jQuery('#".$target."').attr('cols'));
		jQuery('#qtrans_textarea_".$target."_".$language."').attr('rows', jQuery('#".$target."').attr('rows'));
		jQuery('#qtrans_textarea_".$target."_".$language."').attr('style', jQuery('#".$target."').attr('style'));
		jQuery('#qtrans_textarea_".$target."_".$language."').attr('tabindex', jQuery('#".$target."').attr('tabindex'));
		jQuery('#qtrans_textarea_".$target."_".$language."').blur(function() {SSEO.qTrans.switch_postbox('".$parent."','".$target."','" . $language . "');});
		jQuery('#qtrans_textarea_".$target."_".$language."').val(qtrans_use('".$language."',jQuery('#".$target."').val()));
		";
	return $html;

} // end function

/**
 * Clones the "keywords" text input field
 */
function qtrans_sseo_createTextField($parent, $language, $target) {
	
	global $q_config;
	$html = "
		jQuery('#".$target."').after('<input type=\"text\" name=\"qtrans_textinput_".$target."_".$language."\" id=\"qtrans_textinput_".$target."_".$language."\"/>');
		jQuery('#qtrans_textinput_".$target."_".$language."').attr('class', jQuery('#".$target."').attr('class'));
		jQuery('#qtrans_textinput_".$target."_".$language."').attr('size', jQuery('#".$target."').attr('size'));
		jQuery('#qtrans_textinput_".$target."_".$language."').attr('tabindex', jQuery('#".$target."').attr('tabindex'));
		jQuery('#qtrans_textinput_".$target."_".$language."').blur(function() {SSEO.qTrans.switch_postbox('".$parent."','".$target."','" . $language . "');});
		jQuery('#qtrans_textinput_".$target."_".$language."').val(qtrans_use('".$language."',jQuery('#".$target."').val()));
		";
	return $html;

} // end function

function qtrans_sseo_modifyMetabox() {
	
	global $q_config;

	echo "<script type=\"text/javascript\">\n// <![CDATA[\n";
	echo "// Simple SEO qTranslate JS \n";
	echo "if(jQuery('#sseo_primary-meta-box').length > 0) {";

	echo $q_config['js']['qtrans_is_array'];
	echo $q_config['js']['qtrans_xsplit'];
	echo $q_config['js']['qtrans_split'];
	echo $q_config['js']['qtrans_integrate'];

	//echo $q_config['js']['qtrans_switch_postbox'];

	echo $q_config['js']['qtrans_use'];
	$el = qtrans_getSortedLanguages();
	foreach($el as $language) {
		
		// Populate languages in JS object
		echo "SSEO.qTrans.languages.push('$language')";
		
		// Add a button for every language
		echo qtrans_sseo_createTitlebarButton('sseo_primary-meta-box', $language, 'sseo-data', 'qtrans_switcher_sseo_primary-meta-box_'.$language);
		
		// Create a copy of the form controls for each language
		echo qtrans_sseo_createTextArea('sseo_primary-meta-box', $language, '_sseo_meta_description');
		echo qtrans_sseo_createTextField('sseo_primary-meta-box', $language, '_sseo_meta_keywords');

	} // end foreach
	
	// Activate the form controls for the current languages

	echo "    SSEO.qTrans.switch_postbox('sseo_primary-meta-box','_sseo_meta_keywords','" . $q_config['default_language'] . "');";
	echo "    SSEO.qTrans.switch_postbox('sseo_primary-meta-box','_sseo_meta_description','" . $q_config['default_language'] . "');";
	
	// Hide original form controls
	echo "    jQuery('#_sseo_meta_keywords').hide();";
	echo "    jQuery('#_sseo_meta_description').hide();";

	echo "}";
	echo "// ]]>\n</script>\n";

} // end function

/**
 * Prevents content display if not available in the current language but only in
 * the default language
 */
function qtrans_sseo_cleanMeta($text) {
	global $q_config;
	$defaultLanguage = $q_config['language_name'][$q_config['default_language']];
	
	if (preg_match('/^\(' . $q_config['language_name'][$q_config['default_language']] . '\)/', $text)) return '';
	
	return $text;
} // end function

?>