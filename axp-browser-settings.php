<?php /*

**************************************************************************

Plugin Name:  AXP Browser Settings
Plugin URI:   https://github.com/axp-dev/axp-browser-settings
Description:  Plug-in for your browser on your phone
Version:      1.0.0
Author:       Alexander Pushkarev
Author URI:   https://github.com/axp-dev
Text Domain:  axp-browser-settings
License:      GPLv2 or later


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
**************************************************************************/

class AXP_Browser_Settings {
    public $menu_slug;
    public $fields;

    function __construct() {
        $this->menu_slug    = 'axp-browser-settings';
        $this->fields       = 'axp-browser-settings-fields';

        add_action( 'plugins_loaded',           array( &$this, 'init_textdomain' ));
        add_action( 'admin_menu',               array( &$this, 'register_menu' ) );
        add_action( 'admin_init',               array( &$this, 'register_settings' )  );
        add_action( 'admin_enqueue_scripts',    array( &$this, 'admin_enqueues' ) );
        add_action( 'wp_head',                  array( &$this, 'render_head' ) );
    }

    public function init_textdomain() {
        load_plugin_textdomain( 'axp-browser-settings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    public function register_menu() {
        add_options_page(
            __('Browser Settings Page', 'axp-browser-settings'),
            __('Browser Settings', 'axp-browser-settings'),
            'manage_options',
            'axp-browser-settings',
            array(&$this, 'render_page_settings')
        );
    }

    public function register_settings() {
        register_setting( $this->fields, $this->fields );

        add_settings_section(
            'main_section',
            __('Settings', 'axp-browser-settings'),
            null,
            $this->menu_slug
        );

        add_settings_field(
            'browser_color',
            __('Browser Color', 'axp-browser-settings'),
            array( $this, 'render_settings_fields' ),
            $this->menu_slug, 'main_section',
            array(
                'type'      => 'text-color',
                'id'        => 'browser-color',
                'desc'      => ''
            )
        );

        add_settings_field(
            'browser_telephone',
            __('Phone disable format', 'axp-browser-settings'),
            array( $this, 'render_settings_fields' ),
            $this->menu_slug, 'main_section',
            array(
                'type'      => 'checkbox',
                'id'        => 'browser-telephone',
                'desc'      => __('<code>tel:</code>, <code>sms:</code> will work', 'axp-browser-settings'),
                'default'   => '#fff',
            )
        );

        add_settings_field(
            'browser_address',
            __('Address disable format', 'axp-browser-settings'),
            array( $this, 'render_settings_fields' ),
            $this->menu_slug, 'main_section',
            array(
                'type'      => 'checkbox',
                'id'        => 'browser-address',
                'desc'      => ''
            )
        );

        add_settings_field(
            'browser_date',
            __('Date disable format', 'axp-browser-settings'),
            array( $this, 'render_settings_fields' ),
            $this->menu_slug, 'main_section',
            array(
                'type'      => 'checkbox',
                'id'        => 'browser-date',
                'desc'      => ''
            )
        );

        add_settings_field(
            'browser_email',
            __('Email disable format', 'axp-browser-settings'),
            array( $this, 'render_settings_fields' ),
            $this->menu_slug, 'main_section',
            array(
                'type'      => 'checkbox',
                'id'        => 'browser-email',
                'desc'      => __('<code>mailto:</code> will work', 'axp-browser-settings')
            )
        );

        add_settings_field(
            'browser_cleartype',
            __('ClearType', 'axp-browser-settings'),
            array( $this, 'render_settings_fields' ),
            $this->menu_slug, 'main_section',
            array(
                'type'      => 'checkbox',
                'id'        => 'browser-cleartype',
                'desc'      => __('Forcibly IE (<a href="https://en.wikipedia.org/wiki/ClearType">about ClearType</a>)', 'axp-browser-settings')
            )
        );
    }

    public function admin_enqueues($hook_suffix) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'axp-browser-handle', plugins_url('js/axp-browser-settings.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    }

    public function apx_get_filed( $name ) {
        return get_option( $this->fields )[$name];
    }

    public function render_settings_fields( $arguments ) {
        extract( $arguments );

        $option_name = $this->fields;
        $o = get_option( $option_name );

        switch ( $type ) {
            case 'text':
                $o[$id] = esc_attr( stripslashes($o[$id]) );
                echo "<input class='axp-browser-settings-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";
                echo ($desc != '') ? "<p class='description'>$desc</p>" : "";
                break;
            case 'text-color':
                $o[$id] = esc_attr( stripslashes($o[$id]) );
                echo "<input class='axp-browser-settings-color' type='text' id='$id' data-default-color='#fff' name='" . $option_name . "[$id]' value='$o[$id]' />";
                echo ($desc != '') ? "<p class='description'>$desc</p>" : "";
                break;
            case 'checkbox':
                $checked = ($o[$id] == 'on') ? " checked='checked'" :  '';
                echo "<label><input class='axp-browser-settings-checkbox' type='checkbox' id='$id' name='" . $option_name . "[$id]' $checked /> ";
                echo "</label>";
                echo ($desc != '') ? "<p class='description'>$desc</p>" : "";
                break;
        }
    }

    public function render_page_settings() {
        ?>
        <div class="wrap">
            <h2><?php _e('Browser Settings', 'axp-browser-settings'); ?></h2>
            <p>
                <?php _e('The plugin is absolutely free. You can support the developer.', 'axp-browser-settings') ?>
            </p>

            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                <input type="hidden" name="cmd" value="_donations">
                <input type="hidden" name="business" value="axp-dev@yandex.ru">
                <input type="hidden" name="lc" value="GB">
                <input type="hidden" name="item_name" value="AXP Browser Settings">
                <input type="hidden" name="no_note" value="0">
                <input type="hidden" name="currency_code" value="RUB">
                <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
                <input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
            </form>

            <form method="POST" enctype="multipart/form-data" action="options.php">
                <?php settings_fields( $this->fields ); ?>
                <?php do_settings_sections( $this->menu_slug ); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function render_head() {
        echo "<!-- AXP Browser Settings -->\n";
        echo "<meta name=\"theme-color\" content=\"{$this->apx_get_filed('browser-color')}\">\n";
        echo "<meta name=\"msapplication-navbutton-color\" content=\"{$this->apx_get_filed('browser-color')}\">\n";

        if ($this->apx_get_filed('browser-telephone')) {
            echo "<meta name=\"format-detection\" content=\"telephone=no\">\n";
            echo "<meta http-equiv=\"x-rim-auto-match\" content=\"none\">\n";
        }
        if ($this->apx_get_filed('browser-address')) {
            echo "<meta name=\"format-detection\" content=\"address=no\">\n";
        }
        if ($this->apx_get_filed('browser-date')) {
            echo "<meta name=\"format-detection\" content=\"date=no\">\n";
        }
        if ($this->apx_get_filed('browser-email')) {
            echo "<meta name=\"format-detection\" content=\"email=no\">\n";
        }
        if ($this->apx_get_filed('cleartype')) {
            echo "<meta http-equiv=\"cleartype\" content=\"on\">\n";
        }
        echo "<!-- /AXP Browser Settings -->\n";
    }
}

$AXP_Browser_Settings = new AXP_Browser_Settings();