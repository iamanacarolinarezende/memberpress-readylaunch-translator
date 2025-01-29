***MEMBERPRESS AND READYLAUCH***</br>
English and Spanish</br>
This custom code enables translation for the MemberPress plugin, specifically within the ReadyLaunch interface. Since MemberPress does not fully support multilingual plugins like WPML or Polylang within ReadyLaunch templates, this solution dynamically translates interface elements using JavaScript and PHP.</br>
</br>
The code works by detecting the current language of the website and replacing text strings inside ReadyLaunch templates. It ensures that all MemberPress pages, including checkout, login, and account pages, display the correct translations without modifying the core plugin files.</br>
</br>
**INSTRUCTIONS**</br>
Download and ZIP all files (memberpress-language-switcher.php + flags folder + flags.png)</br>
Install the plugin</br>
Insert at this file: public_html/wp-content/plugins/memberpress/app/views/readylaunch/layout/app.php (line 31) (this code will make the dropdown appear at Account / Registration
   <!-- Seletor de Idioma -->
		<?php
        if (shortcode_exists('memberpress_language_switcher')) {
            echo do_shortcode('[memberpress_language_switcher]');
      }
    ?>

This will translate to English x Spanish. If you want to add more languages use the link below and add the flags at FLAGS folder:</br>
https://memberpress.com/docs/how-to-translate-memberpress/</br>
Or you can simply use Loco translation plugin</br>
</br>
contact if you need any help, open an issue.</br>

[buymeacoffee.com/ajudae](https://buymeacoffee.com/ajudae)
