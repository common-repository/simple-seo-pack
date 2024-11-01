<?php
/*
Plugin Name: Simple SEO Pack
Version: 1.1.3.8
Plugin URI: http://www.vtardia.com/simple-seo-pack/
Author: Vito Tardia
Author URI: http://www.vtardia.com
Description: Simple SEO is a quick way to add meta tags to your post and pages using WP custom fields.

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
 * Main SimpleSEO plugin class
 * 
 * All methods are user statically
 * 
 * @version 1.1.3.8
 */
class SimpleSEO {
    
    public static $prefix = 'sseo_';
    public static $metabox = array();
    
    /**
     * Initialize the plugin
     */
    static function init() {
        
        // Init localization
        load_plugin_textdomain ( 'simple-seo-pack', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/locale' );
        
        // Add custom menu handler
        add_action('admin_menu', array('SimpleSEO', 'init_menu'));

        // Add meta boxes for posts and pages
        SimpleSEO::$metabox = array(
            'id'       => SimpleSEO::$prefix . 'primary-meta-box',
            'title'    => __('Simple SEO Meta Tags', 'simple-seo-pack'),
            'page'     => array('page', 'post'),
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => array(
                    array(
                        'name' => __('Keywords', 'simple-seo-pack'),
                        'desc' => __('Insert a list of desired keywords', 'simple-seo-pack'),
                        'id'   => '_' . SimpleSEO::$prefix . 'meta_keywords',
                        'type' => 'text',
                        ),
                    array(
                        'name' => __('Description', 'simple-seo-pack'),
                        'desc' => __('Insert a short description for this page or post', 'simple-seo-pack'),
                        'id'   => '_' . SimpleSEO::$prefix . 'meta_description',
                        'type' => 'textarea',
                        ),
                    array(
                        'name' => __('Use global settings (not recomended)', 'simple-seo-pack'),
                        'id'   => '_' . SimpleSEO::$prefix . 'use_global_settings',
                        'type' => 'checkbox',
                        ),
                )
            );

        add_action('add_meta_boxes', array('SimpleSEO', 'add_metabox'));
        add_action('save_post', array('SimpleSEO', 'save_postdata'));
        
        // Add header meta tags
        add_action('wp_head', array('SimpleSEO', 'print_keywords'));
        add_action('wp_head', array('SimpleSEO', 'print_description'));
        
        add_action('admin_enqueue_scripts', array('SimpleSEO', 'add_js_admin'));
        
        // Support for qTranslate
        // Commented 25 Apr 2014, no more support, will be removed in the future
        // if (function_exists('qtrans_init')) {
        //     
        //     require_once dirname(__FILE__) . '/lib/qtranslate.php';
        // 
        //     add_filter('admin_footer', 'qtrans_sseo_modifyMetabox');
        // 
        //     // Translation filters
        //     add_filter('sseo_keywords', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);
        //     add_filter('sseo_description', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);
        //     
        //     // Cleaning filters
        //     add_filter('sseo_keywords', 'qtrans_sseo_cleanMeta');
        //     add_filter('sseo_description', 'qtrans_sseo_cleanMeta');
        // 
        // } // end if

    } // end function
    

    /**
     * Adds options menu
     */
    static function init_menu() {

        // Add general options menu
        add_options_page(__('Simple SEO', 'simple-seo-pack'), __('Simple SEO', 'simple-seo-pack'), 'manage_options', 'sseo-options', array('SimpleSEO', 'manage_options'));
        
    } // end function

    /**
     * Manages the Simple SEO option page
     */
    static function manage_options() {

        // Open page
        echo '<div class="wrap">';
        echo "<h2>" . __('Simple SEO Options', 'simple-seo-pack') . "</h2>";

        echo "<p>" . __("From here you can set the default values for the <code>keywords</code> and <code>description</code> meta tags. These values will be used for all the posts and pages which don't have their custom ones.", 'simple-seo-pack') . "</p>";

        // Open form
        echo '<form method="post" action="options.php">';
        wp_nonce_field('update-options');

        // General options
        
        echo "<h3>" . __('Keywords', 'simple-seo-pack') . "</h3>";
        echo "<p>" . __("Insert 3-12 kewords or key phrases separating them with commas.", 'simple-seo-pack') . "</p>";
         
        $keywords = get_option(SimpleSEO::$prefix . 'keywords');
        echo '<textarea name="' . SimpleSEO::$prefix . 'keywords" cols="60" rows="4" style="width: 98%; font-size: 12px;" class="code">';
        echo $keywords;
        echo '</textarea>';

        echo "<h3>" . __('Description', 'simple-seo-pack') . "</h3>";
        echo "<p>" . __("Insert a description containing more or less 5-24 words.", 'simple-seo-pack') . "</p>";
         
        $description = get_option(SimpleSEO::$prefix . 'description');
        echo '<textarea name="' . SimpleSEO::$prefix . 'description" cols="60" rows="4" style="width: 98%; font-size: 12px;" class="code">';
        echo $description;
        echo '</textarea>';

        // WP inteface
        echo '<input type="hidden" name="action" value="update" />';
        echo '<input type="hidden" name="page_options" value="' . SimpleSEO::$prefix . 'keywords,' . SimpleSEO::$prefix . 'description" />';

        // Second submit button

        echo '<p class="submit">
        <input type="submit" class="button button-primary" name="Submit" value="' . __('Save Changes', 'simple-seo-pack') . '" />
        </p>';

        // Close document
        echo '</form>';
        echo '</div>';

    } // end function manage_options
    
    /**
     * Prints post or page keywords
     */
    static function print_keywords() {
        global $post;
        
        if (!is_object($post)) return;

        if (is_archive()) return;

        if ('on' == get_post_meta($post->ID, '_' . SimpleSEO::$prefix . 'use_global_settings', true)) {
            
            if (!$keywords = get_option(SimpleSEO::$prefix . 'keywords')) {
                return false;
            } // end if

        } else if (!$keywords = get_post_meta($post->ID, '_' . SimpleSEO::$prefix . 'meta_keywords', true)) {
                return false;
        } // end if

        $keywords = apply_filters('sseo_keywords', $keywords);
        
        if (!empty($keywords)) {
            $html = '<meta name="keywords" content="' . $keywords . '" />';

            echo $html . "\n";
        } // end if
        
    } // end function print_keywords

    /**
     * Prints post or page description
     */
    static function print_description() {
        global $post;

        if (!is_object($post)) return;
        
        if (is_archive()) return;
        
        if ('on' == get_post_meta($post->ID, '_' . SimpleSEO::$prefix . 'use_global_settings', true)) {
            
            if (!$description = get_option(SimpleSEO::$prefix . 'description')) {
                return false;
            } // end if

        } else if (!$description = get_post_meta($post->ID, '_' . SimpleSEO::$prefix . 'meta_description', true)) {
            return false;
        } // end if
        
        $description = apply_filters('sseo_description', $description);
        
        if (!empty($description)) {
            $html = '<meta name="description" content="' . $description . '" />';

            echo $html . "\n";
        } // end if

    } // end function print_keywords
    
    /**
     * Setup the metabox data for posts and pages
     * 
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     */
    static function add_metabox() {
        
        $metabox = &SimpleSEO::$metabox;
        foreach ($metabox['page'] as $post_type) {
            add_meta_box($metabox['id'], $metabox['title'], array('SimpleSEO', 'show_metabox'), $post_type, $metabox['context'], $metabox['priority'], $metabox['fields']);
        } // end foreach
        
    } // end function
    

    /**
     * Displays the metabox content
     * 
     * @param  object  $post    The current post object passed by wordpress
     * @param  array   $params  The metabox data passed by wordpress, $params['args'] contains the custom arguments
     */
    static function show_metabox($post, $params) {
        
        // Use nonce for verification
        echo '<input type="hidden" name="' . SimpleSEO::$prefix . 'meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
        
        $requiredFields = array();
        
        echo '<div id="sseo-data">';
        
        foreach ($params['args'] as $field) {
            
            // get current post meta data
            $meta = get_post_meta($post->ID, $field['id'], true);

            $field_id = str_replace(SimpleSEO::$prefix, '', $field['id']);
            $field_id = $field['id'];
            
            $required = '';
            if (in_array($field['id'], $requiredFields)) {
                $required = ' required';
            } // end if
            
            $beforeField = '';

            echo '<p><label for="', $field_id, '" style="font-weight:bold;">', $field['name'], ' </label>';
            
            switch ($field['type']) {

                case 'text':
                    echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : '', '" size="30" class="large-text' . $required . '" />', '
                    ', $field['desc'];
                break;

                case 'small-text':
                    echo  $beforeField, '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : "", '" size="30" class="small-text' . $required . '" />', '
                    ', $field['desc'];
                break;

                case 'textarea':
                    echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:99%">', $meta ? $meta : "", '</textarea>', '
                    ', $field['desc'];
                break;

                case 'select':
                    echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                    foreach ($field['options'] as $option) {
                        echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                    } // end foreach
                    echo '</select>';
                break;

                case 'radio':
                    foreach ($field['options'] as $option) {
                        echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                    } // end foreach
                break;

                case 'checkbox':
                    echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                    break;
                break;

            } // end switch
            echo '</p>';

        } // end foreach
        echo '</div>';
    } // end function
    
    
    /**
     * Check posted data and saves metabox data
     */
    static function save_postdata($post_id) {

        // verify nonce
        if (!wp_verify_nonce($_POST[SimpleSEO::$prefix . 'meta_box_nonce'], basename(__FILE__))) {
            return $post_id;
        } // end if
        
        if ( wp_is_post_revision( $post_id ) ) {
            return $post_id;
        } // end if

        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        } // end if

        // check permissions
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            } // end if
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        } // end if

        if ( !wp_is_post_revision( $post_id ) ) {

            $sseo_fields = SimpleSEO::$metabox['fields'];

            foreach ($sseo_fields as $field) {

                $old = get_post_meta($post_id, $field['id'], true);
                $new = (isset($_POST[$field['id']])) ? $_POST[$field['id']]: null;

                if ($new && $new != $old) {
                    update_post_meta($post_id, $field['id'], $new);
                } elseif ('' == $new && $old) {
                    delete_post_meta($post_id, $field['id'], $old);
                } // end if

            } // end foreach

        } // end if

    } // end function
    
    /**
     * Safe add JS for admin panels
     */
    static function add_js_admin() {
    
        // Add JS support for qTranslate
        if (function_exists('qtrans_init')) {
            wp_enqueue_script('sseo-qtrans', plugins_url('/js/sseo_qtrans.js', __FILE__), array('jquery'), '1.0', false);
        } // end if
        
    } // end function
    
    
} // end class


/// MAIN----------------------------------------------------------------------

add_action('plugins_loaded', array('SimpleSEO', 'init'));
?>
