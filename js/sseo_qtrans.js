/**
 * Javascript utility for SimpleSEO/qTranslate library
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

// Initialize custom namespace objects
window.SSEO = window.SSEO || {};
window.SSEO.qTrans = window.SSEO.qTrans || {};

// Initialize language list, this will be filled with PHP-generated JS code
SSEO.qTrans.languages = [];

// Select current form controls based on current language
SSEO.qTrans.switch_postbox = function(parent, target, lang) {

	// Remove previous active button
	jQuery('#' + parent + ' .qtranslate_lang_div').removeClass('active');
	
	// Some code to be executed for each language
	for (var i in SSEO.qTrans.languages) {
		var al = SSEO.qTrans.languages[i];
		
		var val;
		val = jQuery('#qtrans_textarea_' + target + '_' + al).val();
		if (val != undefined) jQuery('#' + target).val(qtrans_integrate(al, jQuery('#qtrans_textarea_' + target + '_' + al).val(), jQuery('#' + target).val()));
	    
		val = jQuery('#qtrans_textinput_' + target + '_' + al).val();
		if (val != undefined) jQuery('#' + target).val(qtrans_integrate(al, jQuery('#qtrans_textinput_' + target + '_' + al).val(), jQuery('#' + target).val()));
	    
		jQuery('#' + parent + ' .qtranslate_lang_div').removeClass('active');
	    if (lang != false) {
			jQuery('#qtrans_textarea_' + target + '_' + al).hide();
			jQuery('#qtrans_textinput_' + target + '_' + al).hide();
		} // end if
		
	} // end for

	// Update active button status
    if (lang != false) {
        jQuery('#qtrans_switcher_' + parent + '_' + lang).addClass('active');
        jQuery('#qtrans_textarea_' + target + '_' + lang).show();
        jQuery('#qtrans_textinput_' + target + '_' + lang).show();
    } // end function

} // end function