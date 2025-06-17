<?php

class Custom_Side_Tab {
    private $version;

    public function __construct() {
        $this->version = CST_VERSION;
    }

    public function run() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_side_tab'));
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            'custom-side-tab',
            CST_PLUGIN_URL . 'assets/css/custom-side-tab.css',
            array(),
            $this->version
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            'custom-side-tab',
            CST_PLUGIN_URL . 'assets/js/custom-side-tab.js',
            array('jquery'),
            $this->version,
            true
        );

        $settings = get_option('cst_settings', array(
            'background_color' => '#f39c12',
            'text_color' => '#ffffff',
            'hover_color' => '#e67e22'
        ));

        // Pass settings to JavaScript
        wp_localize_script(
            'custom-side-tab',
            'cstSettings',
            array(
                'items' => $this->get_tab_items(),
                'hover_color' => $settings['hover_color']
            )
        );
    }

    private function get_tab_items() {
        $default_items = array(
            array(
                'icon' => CST_PLUGIN_URL . 'assets/images/phone.png',
                'text' => 'Contact Us',
                'link' => 'tel:123456789',
                'target' => '_self'
            ),
            array(
                'icon' => CST_PLUGIN_URL . 'assets/images/quote.png',
                'text' => 'Get a Quote',
                'link' => '/get-a-quote',
                'target' => '_self'
            ),
            array(
                'icon' => CST_PLUGIN_URL . 'assets/images/contact.png',
                'text' => 'Contact Form',
                'link' => '/contact-form',
                'target' => '_self'
            )
        );

        $saved_items = get_option('cst_tab_items', $default_items);
        return $saved_items;
    }

    public function render_side_tab() {
        $settings = get_option('cst_settings', array(
            'background_color' => '#f39c12',
            'text_color' => '#ffffff',
            'hover_color' => '#e67e22',
            'enabled' => 1,
            'position' => 'right'
        ));

        // Don't render if disabled
        if (empty($settings['enabled'])) {
            return;
        }

        $items = $this->get_tab_items();
        
        $style = sprintf(
            'background-color: %s; color: %s;',
            esc_attr($settings['background_color']),
            esc_attr($settings['text_color'])
        );

        $position = esc_attr($settings['position']);
        ?>
        <div id="custom-side-tab" class="collapsed <?php echo $position; ?>" style="<?php echo $style; ?>">
            <button id="side-tab-toggle" class="side-tab-toggle">
                <span class="toggle-icon">›</span>
            </button>
            <div class="side-tab-items">
                <?php foreach ($items as $item) : ?>
                    <a href="<?php echo esc_url($item['link']); ?>" 
                       target="<?php echo esc_attr($item['target']); ?>"
                       class="side-tab-item">
                        <img src="<?php echo esc_url($item['icon']); ?>" 
                             alt="<?php echo esc_attr($item['text']); ?>" />
                        <span><?php echo esc_html($item['text']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
} 