<?php
namespace EasySocialMedia\Admin;

class Admin {
    private $settings_page = 'easy-social-media';
    private $option_group = 'esm_options';
    private $option_name = 'esm_settings';

    public function init() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_options_page(
            __('Social Media Tabs', 'easy-social-media'),
            __('Social Media Tabs', 'easy-social-media'),
            'manage_options',
            $this->settings_page,
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting(
            $this->option_group,
            $this->option_name,
            [$this, 'sanitize_settings']
        );

        add_settings_section(
            'esm_main_section',
            __('Social Media Settings', 'easy-social-media'),
            [$this, 'section_callback'],
            $this->settings_page
        );

        $this->add_settings_fields();
    }

    private function add_settings_fields() {
        $social_platforms = [
            'facebook' => __('Facebook URL', 'easy-social-media'),
            'twitter' => __('Twitter URL', 'easy-social-media'),
            'instagram' => __('Instagram URL', 'easy-social-media'),
            'linkedin' => __('LinkedIn URL', 'easy-social-media'),
            'youtube' => __('YouTube URL', 'easy-social-media')
        ];

        foreach ($social_platforms as $platform => $label) {
            add_settings_field(
                "esm_{$platform}_url",
                $label,
                [$this, 'url_field_callback'],
                $this->settings_page,
                'esm_main_section',
                ['platform' => $platform]
            );
        }

        // Add other settings fields
        $this->add_appearance_fields();
    }

    private function add_appearance_fields() {
        $appearance_fields = [
            'icon_size' => __('Icon Size', 'easy-social-media'),
            'icon_color' => __('Icon Color', 'easy-social-media'),
            'hover_color' => __('Hover Color', 'easy-social-media'),
            'position_offset' => __('Position Offset', 'easy-social-media'),
            'mobile_visibility' => __('Mobile Visibility', 'easy-social-media')
        ];

        foreach ($appearance_fields as $field => $label) {
            add_settings_field(
                "esm_{$field}",
                $label,
                [$this, "{$field}_field_callback"],
                $this->settings_page,
                'esm_main_section'
            );
        }
    }

    public function sanitize_settings($input) {
        $sanitized = [];
        
        // Sanitize URLs
        $social_platforms = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'];
        foreach ($social_platforms as $platform) {
            if (isset($input["{$platform}_url"])) {
                $sanitized["{$platform}_url"] = esc_url_raw($input["{$platform}_url"]);
            }
        }

        // Sanitize other settings
        $sanitized['icon_size'] = sanitize_text_field($input['icon_size'] ?? 'medium');
        $sanitized['icon_color'] = sanitize_hex_color($input['icon_color'] ?? '#000000');
        $sanitized['hover_color'] = sanitize_hex_color($input['hover_color'] ?? '#666666');
        $sanitized['position_offset'] = absint($input['position_offset'] ?? 50);
        $sanitized['mobile_visibility'] = isset($input['mobile_visibility']);

        return $sanitized;
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields($this->option_group);
                do_settings_sections($this->settings_page);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Callback functions for settings fields
    public function section_callback() {
        echo '<p>' . esc_html__('Configure your social media widget settings below.', 'easy-social-media') . '</p>';
    }

    public function url_field_callback($args) {
        $options = get_option($this->option_name);
        $platform = $args['platform'];
        $value = $options["{$platform}_url"] ?? '';
        ?>
        <input type="url" 
               name="<?php echo esc_attr("{$this->option_name}[{$platform}_url]"); ?>" 
               value="<?php echo esc_url($value); ?>" 
               class="regular-text">
        <?php
    }

    public function icon_size_field_callback() {
        $options = get_option($this->option_name);
        $size = $options['icon_size'] ?? 'medium';
        $sizes = ['small' => __('Small', 'easy-social-media'),
                 'medium' => __('Medium', 'easy-social-media'),
                 'large' => __('Large', 'easy-social-media')];
        
        foreach ($sizes as $value => $label) {
            ?>
            <label>
                <input type="radio" 
                       name="<?php echo esc_attr("{$this->option_name}[icon_size]"); ?>" 
                       value="<?php echo esc_attr($value); ?>"
                       <?php checked($size, $value); ?>>
                <?php echo esc_html($label); ?>
            </label>
            <?php
        }
    }

    public function icon_color_field_callback() {
        $options = get_option($this->option_name);
        $color = $options['icon_color'] ?? '#000000';
        ?>
        <input type="text" 
               name="<?php echo esc_attr("{$this->option_name}[icon_color]"); ?>" 
               value="<?php echo esc_attr($color); ?>" 
               class="esm-color-picker">
        <?php
    }

    public function hover_color_field_callback() {
        $options = get_option($this->option_name);
        $color = $options['hover_color'] ?? '#666666';
        ?>
        <input type="text" 
               name="<?php echo esc_attr("{$this->option_name}[hover_color]"); ?>" 
               value="<?php echo esc_attr($color); ?>" 
               class="esm-color-picker">
        <?php
    }

    public function position_offset_field_callback() {
        $options = get_option($this->option_name);
        $offset = $options['position_offset'] ?? 50;
        ?>
        <input type="number" 
               name="<?php echo esc_attr("{$this->option_name}[position_offset]"); ?>" 
               value="<?php echo esc_attr($offset); ?>" 
               min="0" 
               max="200" 
               step="1">
        <p class="description"><?php esc_html_e('Offset from the top in pixels', 'easy-social-media'); ?></p>
        <?php
    }

    public function mobile_visibility_field_callback() {
        $options = get_option($this->option_name);
        $visible = isset($options['mobile_visibility']);
        ?>
        <label>
            <input type="checkbox" 
                   name="<?php echo esc_attr("{$this->option_name}[mobile_visibility]"); ?>" 
                   <?php checked($visible); ?>>
            <?php esc_html_e('Show on mobile devices', 'easy-social-media'); ?>
        </label>
        <?php
    }
}
