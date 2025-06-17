<?php

class Custom_Side_Tab_Admin {
    private $version;

    public function __construct() {
        $this->version = CST_VERSION;
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function enqueue_admin_scripts($hook) {
        if ('settings_page_custom-side-tab' !== $hook) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        wp_enqueue_script(
            'custom-side-tab-admin',
            CST_PLUGIN_URL . 'admin/js/custom-side-tab-admin.js',
            array('jquery', 'wp-color-picker'),
            $this->version,
            true
        );
    }

    public function add_plugin_page() {
        add_menu_page(
            'Side Tab Settings',
            'Side Tab',
            'manage_options',
            'custom-side-tab',
            array($this, 'create_admin_page'),
            'dashicons-arrow-right-alt2',
            30
        );
    }

    public function create_admin_page() {
        $tab_items = get_option('cst_tab_items');
        $settings = get_option('cst_settings', array(
            'background_color' => '#f39c12',
            'text_color' => '#ffffff',
            'hover_color' => '#e67e22'
        ));
        ?>
        <div class="wrap">
            <h2>Side Tab Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('cst_option_group');
                do_settings_sections('custom-side-tab');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'cst_option_group',
            'cst_tab_items',
            array($this, 'sanitize_tab_items')
        );

        register_setting(
            'cst_option_group',
            'cst_settings',
            array($this, 'sanitize_settings')
        );

        add_settings_section(
            'cst_setting_section',
            'Tab Items',
            array($this, 'section_info'),
            'custom-side-tab'
        );

        add_settings_field(
            'tab_items',
            'Tab Items',
            array($this, 'tab_items_callback'),
            'custom-side-tab',
            'cst_setting_section'
        );

        add_settings_field(
            'appearance',
            'Appearance',
            array($this, 'appearance_callback'),
            'custom-side-tab',
            'cst_setting_section'
        );
    }

    public function sanitize_tab_items($input) {
        $new_input = array();
        
        if (isset($input) && is_array($input)) {
            foreach ($input as $item) {
                $new_item = array(
                    'icon' => esc_url_raw($item['icon']),
                    'text' => sanitize_text_field($item['text']),
                    'link' => esc_url_raw($item['link']),
                    'target' => sanitize_text_field($item['target'])
                );
                $new_input[] = $new_item;
            }
        }
        
        return $new_input;
    }

    public function sanitize_settings($input) {
        $new_input = array();
        
        $new_input['background_color'] = sanitize_hex_color($input['background_color']);
        $new_input['text_color'] = sanitize_hex_color($input['text_color']);
        $new_input['hover_color'] = sanitize_hex_color($input['hover_color']);
        $new_input['enabled'] = isset($input['enabled']) ? 1 : 0;
        $new_input['position'] = in_array($input['position'], array('left', 'right')) ? $input['position'] : 'right';
        
        return $new_input;
    }

    public function section_info() {
        echo 'Configure your side tab items and appearance below:';
    }

    public function tab_items_callback() {
        $items = get_option('cst_tab_items', array());
        ?>
        <div id="tab-items-container">
            <?php foreach ($items as $index => $item) : ?>
            <div class="tab-item">
                <h4>Tab Item <?php echo $index + 1; ?></h4>
                <p>
                    <label>Icon:</label><br>
                    <input type="text" name="cst_tab_items[<?php echo $index; ?>][icon]" 
                           value="<?php echo esc_attr($item['icon']); ?>" class="regular-text icon-url" />
                    <button type="button" class="button upload-icon">Upload Icon</button>
                </p>
                <p>
                    <label>Text:</label><br>
                    <input type="text" name="cst_tab_items[<?php echo $index; ?>][text]" 
                           value="<?php echo esc_attr($item['text']); ?>" class="regular-text" />
                </p>
                <p>
                    <label>Link:</label><br>
                    <input type="text" name="cst_tab_items[<?php echo $index; ?>][link]" 
                           value="<?php echo esc_attr($item['link']); ?>" class="regular-text" />
                </p>
                <p>
                    <label>Open in:</label><br>
                    <select name="cst_tab_items[<?php echo $index; ?>][target]">
                        <option value="_self" <?php selected($item['target'], '_self'); ?>>Same Window</option>
                        <option value="_blank" <?php selected($item['target'], '_blank'); ?>>New Window</option>
                    </select>
                </p>
                <button type="button" class="button remove-item">Remove Item</button>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button button-secondary" id="add-new-item">Add New Item</button>
        <?php
    }

    public function appearance_callback() {
        $settings = get_option('cst_settings', array(
            'background_color' => '#f39c12',
            'text_color' => '#ffffff',
            'hover_color' => '#e67e22',
            'enabled' => 1,
            'position' => 'right'
        ));
        ?>
        <p>
            <label>
                <input type="checkbox" name="cst_settings[enabled]" 
                       value="1" <?php checked($settings['enabled'], 1); ?> />
                Enable Side Tab
            </label>
        </p>
        <p>
            <label>Position:</label><br>
            <select name="cst_settings[position]">
                <option value="left" <?php selected($settings['position'], 'left'); ?>>Left Side</option>
                <option value="right" <?php selected($settings['position'], 'right'); ?>>Right Side</option>
            </select>
        </p>
        <p>
            <label>Background Color:</label><br>
            <input type="text" name="cst_settings[background_color]" 
                   value="<?php echo esc_attr($settings['background_color']); ?>" 
                   class="color-picker" />
        </p>
        <p>
            <label>Text Color:</label><br>
            <input type="text" name="cst_settings[text_color]" 
                   value="<?php echo esc_attr($settings['text_color']); ?>" 
                   class="color-picker" />
        </p>
        <p>
            <label>Hover Color:</label><br>
            <input type="text" name="cst_settings[hover_color]" 
                   value="<?php echo esc_attr($settings['hover_color']); ?>" 
                   class="color-picker" />
        </p>
        <?php
    }
} 