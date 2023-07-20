<?php

add_action("customize_register", "language_customize_register");
function language_customize_register($wp_customize)
{
    if (!class_exists('Skyrocket_Toggle_Switch_Custom_control')) {
        return;
    }

    /**
     * Section initialize
     */

    $wp_customize->add_section('language', array (
        "title" => __("Language", "growtype"),
        "priority" => 100,
    ));

    /**
     *
     */
    $wp_customize->add_setting('language_selector_icon',
        array (
            'default' => 1,
            'transport' => 'refresh',
        )
    );

    $wp_customize->add_control(new Skyrocket_Toggle_Switch_Custom_control($wp_customize, 'language_selector_icon',
        array (
            'label' => esc_html__('Language Selector Icon'),
            'description' => __('Enable/disable language selector.', 'growtype-qtranslate'),
            'section' => 'language',
        )
    ));

    /**
     *
     */
    $wp_customize->add_setting('language_selector_individual_mode',
        array (
            'default' => 0,
            'transport' => 'refresh',
        )
    );

    $wp_customize->add_control(new Skyrocket_Toggle_Switch_Custom_control($wp_customize, 'language_selector_individual_mode',
        array (
            'label' => esc_html__('Individual mode'),
            'description' => __('Show language selections individually.', 'growtype-qtranslate'),
            'section' => 'language',
        )
    ));

    /**
     *
     */
    $wp_customize->add_setting('language_selector_text_mode',
        array (
            'default' => 0,
            'transport' => 'refresh',
        )
    );

    $wp_customize->add_control(new Skyrocket_Toggle_Switch_Custom_control($wp_customize, 'language_selector_text_mode',
        array (
            'label' => esc_html__('Text Mode'),
            'description' => __('Show language selections as text.', 'growtype-qtranslate'),
            'section' => 'language',
        )
    ));
}

/**
 * Extend header
 */
add_action('growtype_header_inner_before_close', 'growtype_header_inner_before_close_extend');
function growtype_header_inner_before_close_extend()
{
    $text_mode = get_theme_mod('language_selector_text_mode', false);

    if (growtype_qtranslate_language_selector()) {
        echo growtype_qtranslate_language_selector_html($text_mode ? 'text' : 'image');
    }
}

function growtype_qtranslate_language_selector_html($args)
{
    ob_start();
    ?>
    <div class="language-selector <?php echo growtype_qtranslate_language_selector_classes() ?>">
        <?php echo qtranxf_generateLanguageSelectCode($args) ?>
    </div>
    <?php

    return ob_get_clean();
}

/**
 * @return bool
 */
function growtype_qtranslate_language_selector()
{
    $enabled = get_theme_mod('language_selector_icon', true);

    return class_exists('qTranslateXWidget') && $enabled ? true : false;
}

/**
 * @return bool
 */
function growtype_qtranslate_language_selector_classes()
{
    $classes = [];
    $individual_mode = get_theme_mod('language_selector_individual_mode', false);
    $text_mode = get_theme_mod('language_selector_text_mode', false);

    if ($individual_mode) {
        array_push($classes, 'individual-mode');
    }

    if ($text_mode) {
        array_push($classes, 'text-mode');
    }

    return implode(' ', $classes);
}
