<?php
/*
 * Plugin Name: MemberPress Internal Translation Loader with Flag Switcher
 * Plugin URI: https://seusite.com
 * Description: Plugin to load MemberPress translations with option to change language directly, displaying flags in dropdown.
 * Version: 1.4.0
 * Author: Ana Carolina Rezende
 * Author URI: https://iamanacarolinarezende.github.io/
 * Requires at least: 4.6
 * Tested up to: 6.6
 * Text Domain: memberpress-internal-translation-switcher
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Function to translate
function load_memberpress_translations_with_switcher() {
    $locale = isset($_COOKIE['memberpress_language']) ? sanitize_text_field($_COOKIE['memberpress_language']) : determine_locale();
    $memberpress_path = WP_PLUGIN_DIR . '/memberpress/i18n/'; //insert the translation at this folder
    $memberpress_courses_path = WP_PLUGIN_DIR . '/memberpress-courses/i18n/'; //insert translations at this folder
    $wordpress_languages_path = WP_CONTENT_DIR . '/languages/'; //wordpress translation. insert here too

    if (is_dir($memberpress_path)) {
        load_textdomain('memberpress', $memberpress_path . 'memberpress-' . $locale . '.mo');
    }

    if (is_dir($memberpress_courses_path)) {
        $course_locale = $locale === 'es_ES' ? 'es_ES' : $locale;
        load_textdomain('memberpress-courses', $memberpress_courses_path . 'memberpress-courses-' . $course_locale . '.mo');
    }

    if (is_dir($wordpress_languages_path)) {
        $files = glob($wordpress_languages_path . '*' . $locale . '*.mo');
        foreach ($files as $file) {
            load_textdomain('default', $file);
        }
    }
}

add_action('init', 'load_memberpress_translations_with_switcher');

// Render switch and flags
function render_language_switcher() {
    $available_languages = [
        'en_US' => ['label' => 'English', 'flag' => 'flags/flag-en_us.png'],
        'es_ES' => ['label' => 'Español', 'flag' => 'flags/flag-es_es.png'],
    ];

    $current_language = isset($_COOKIE['memberpress_language']) ? sanitize_text_field($_COOKIE['memberpress_language']) : determine_locale();
    $current_language_flag = plugin_dir_url(__FILE__) . $available_languages[$current_language]['flag'];
    $current_language_label = $available_languages[$current_language]['label'];

    echo '<div id="language-switcher-wrapper" style="position: relative; display: inline-block; padding:5px;">';
    echo '<div id="language-switcher" style="background: white; padding: 8px 10px; border: 1px solid #ccc; cursor: pointer; display: flex; align-items: center; gap: 10px; height: 40px; border-radius: 5px; justify-content: space-between;">';
    echo "<img src=\"$current_language_flag\" alt=\"$current_language_label\" style=\"width: 20px; height: 15px;\">";
    echo "<span>$current_language_label</span>";
    echo '<span style="font-size: 16px; color: #666;">&#x25BC;</span>'; // arrow  - dropdown
    echo '</div>';

    echo '<ul id="language-options" style="display: none; position: absolute; top: 100%; left: 0; background: white; border: 1px solid #ccc; list-style: none; margin: 0; padding: 0; width: 100%; border-radius: 5px;">';

    foreach ($available_languages as $code => $info) {
        $flag_url = plugin_dir_url(__FILE__) . $info['flag'];
        echo "<li style=\"padding: 10px; cursor: pointer; display: flex; align-items: center; gap: 10px;\" data-lang=\"$code\">";
        echo "<img src=\"$flag_url\" alt=\"{$info['label']}\" style=\"width: 20px; height: 15px;\">";
        echo "<span>{$info['label']}</span>";
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
}

// shortcode to switcher
function memberpress_language_switcher_shortcode() {
    ob_start();
    render_language_switcher();
    return ob_get_clean();
}

add_shortcode('memberpress_language_switcher', 'memberpress_language_switcher_shortcode');

// process the language change
function handle_language_switch() {
    if (!empty($_POST['memberpress_language'])) {
        $language = sanitize_text_field($_POST['memberpress_language']);
        setcookie('memberpress_language', $language, time() + (86400 * 30), COOKIEPATH, COOKIE_DOMAIN);
        wp_redirect($_SERVER['REQUEST_URI']);
        exit;
    }
}

add_action('init', 'handle_language_switch');

// CSS and JavaScript to customize dropdown
function add_flag_styles_and_scripts() {
    ?>
    <style>
        #language-switcher:hover + #language-options,
        #language-options:hover {
            display: block;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const switcher = document.getElementById('language-switcher');
            const options = document.getElementById('language-options');

            switcher.addEventListener('click', function () {
                const isDisplayed = options.style.display === 'block';
                options.style.display = isDisplayed ? 'none' : 'block';
            });

            options.addEventListener('click', function (e) {
                if (e.target.tagName === 'LI' || e.target.closest('li')) {
                    const selectedLang = e.target.closest('li').getAttribute('data-lang');
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.style.display = 'none';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'memberpress_language';
                    input.value = selectedLang;

                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
    <?php
}

add_action('wp_head', 'add_flag_styles_and_scripts'); 


// add switcher at any menu
function add_language_switcher_to_menu($items, $args) {
    ob_start();
    render_language_switcher(); // call the render function
    $language_switcher = ob_get_clean();
    
    // Add switcher to menu
    $items .= '<li class="menu-item language-switcher">' . $language_switcher . '</li>';
    return $items;
}

add_filter('wp_nav_menu_items', 'add_language_switcher_to_menu', 10, 2);
