<?php

/*
Plugin Name: Rpi Question Generator
Plugin URI: https://github.com/rpi-virtuell/rpi_question_generator
Description: Wordpress plugin to add new custom blocks via post type
Version: 1.0
Author: Daniel Reintanz
Author URI: https://github.com/FreelancerAMP
License: A "Slug" license name e.g. GPL2
*/

class RpiQuestionGenerator
{
    /**
     * @var string
     */
    private $currentSvg;
    /**
     * @var array
     */
    private $svgCollection;

    /**
     * Plugin constructor
     *
     * @since 0.1
     * @access public
     * @uses plugin_basename
     * @action sso_rest_auth_client
     */
    public function __construct()
    {

        add_action('enqueue_block_assets',array($this, 'enqueue_block_scripts'));

        add_action('init', array($this, 'register_acf_field_group'));
        add_action('init', array($this, 'register_custom_post_type'));
        add_action('init', array($this, 'create_blocks'));
    }

    public function enqueue_block_scripts(){
        wp_enqueue_style(
            'rpi-question-style',
            plugin_dir_url( __FILE__ ) . '/assets/css/rpi-question.css'
        );
        wp_enqueue_script(
            'rpi-question',
            plugin_dir_url( __FILE__ ) . '/assets/js/rpi-question.js'
        );
    }

    public function set_svg($value)
    {
        $this->currentSvg = $value;
    }

    public function add_to_svg_collection($slug, $svg)
    {
        $this->svgCollection[$slug] = $svg;
    }

    public function register_acf_field_group()
    {
        if (function_exists('acf_add_local_field_group')):

            acf_add_local_field_group(array(
                'key' => 'group_leitfragen_blocks',
                'title' => 'Leifragen Blocks',
                'fields' => array(
                    array(
                        'key' => 'field_leitfrage',
                        'label' => 'Leitfrage',
                        'name' => 'leitfrage',
                        'type' => 'textarea',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => 3,
                        'new_lines' => '',
                        'acfe_textarea_code' => 0,
                    ),
                    array(
                        'key' => 'field_leitfrage_block_icon',
                        'label' => 'Block Icon',
                        'name' => 'block_icon',
                        'type' => 'textarea',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => file_get_contents(plugin_dir_path(__FILE__) . '/assets/029-query.svg'),
                        'mode' => 'text/html',
                        'lines' => 1,
                        'indent_unit' => 4,
                        'maxlength' => '',
                        'rows' => 4,
                        'max_rows' => '',
                        'return_entities' => 0,
                        'acfe_textarea_code' => 1,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'leitfragenblocks',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'left',
                'instruction_placement' => 'label',
                'hide_on_screen' => array(
                    0 => 'the_content',
                    1 => 'excerpt',
                    2 => 'discussion',
                    3 => 'comments',
                    4 => 'revisions',
                    5 => 'slug',
                    6 => 'author',
                    7 => 'format',
                    8 => 'page_attributes',
                    9 => 'featured_image',
                    10 => 'categories',
                    11 => 'tags',
                    12 => 'send-trackbacks',
                ),
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
                'acfe_display_title' => '',
                'acfe_autosync' => '',
                'acfe_form' => 0,
                'acfe_meta' => '',
                'acfe_note' => '',
            ));

        endif;
    }

    public function register_custom_post_type()
    {

        /**
         * Post Type: Leitfragen.
         */

        $labels = [
            "name" => __("Leitfragen", "twentytwentytwo"),
            "singular_name" => __("Leitfrage", "twentytwentytwo"),
        ];

        $args = [
            "label" => __("Leitfragen", "twentytwentytwo"),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "delete_with_user" => false,
            "exclude_from_search" => true,
            "capability_type" => "page",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "can_export" => false,
            "rewrite" => ["slug" => "leitfragenblocks", "with_front" => true],
            "query_var" => true,
            "supports" => ["title", "editor", "thumbnail"],
            "show_in_graphql" => false,
        ];

        register_post_type("leitfragenblocks", $args);
    }

    public function create_blocks()
    {
        $posts = get_posts(array('posts_per_page' => -1,
            'post_type' => 'leitfragenblocks',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'suppress_filters' => true, // DO NOT allow WPML to modify the query
            'post_status' => 'publish',
            'update_post_meta_cache' => false));
        foreach ($posts as $post) {
            $fields = get_fields($post->ID);

            if ($fields) {
                if (function_exists('lazyblocks')) :
                    $block_id = random_int(10, 99999999);

                    if (!empty($fields['block_icon'] && strstr($fields['block_icon'], '<svg') && strstr($fields['block_icon'], '</svg'))) {
                        $this->set_svg($fields['block_icon']);
                    } else {
                        $this->set_svg(file_get_contents(plugin_dir_path(__FILE__) . '/assets/029-query.svg'));
                    }

                    $block_atts = array(
                        'id' => $block_id,
                        'title' => $post->post_title,
                        'icon' => $this->clean_up_svg($this->currentSvg),
                        'keywords' => array(),
                        'slug' => 'lazyblock/reli-leitfragen-' . $post->post_name,
                        'description' => '',
                        'category' => 'leitfragen',
                        'category_label' => 'Leitfragen',
                        'supports' => array(
                            'customClassName' => true,
                            'anchor' => false,
                            'align' => array(
                                0 => 'wide',
                                1 => 'full',
                            ),
                            'html' => false,
                            'multiple' => false,
                            'inserter' => true,
                        ),
                        'ghostkit' => array(
                            'supports' => array(
                                'spacings' => false,
                                'display' => false,
                                'scrollReveal' => false,
                                'frame' => false,
                                'customCSS' => false,
                            ),
                        ),
                        'controls' => array(
                            'control_' . $block_id . 'a' => array(
                                'type' => 'text',
                                'name' => 'title',
                                'default' => $post->post_title,
                                'label' => '',
                                'help' => '',
                                'child_of' => '',
                                'placement' => 'content',
                                'width' => '100',
                                'hide_if_not_selected' => 'false',
                                'save_in_meta' => 'false',
                                'save_in_meta_name' => '',
                                'required' => 'false',
                                'placeholder' => '',
                                'characters_limit' => '',
                            ),
                            'control_' . $block_id . 'b' => array(
                                'type' => 'inner_blocks',
                                'name' => 'insertedblocks',
                                'default' => '',
                                'label' => $fields['leitfrage'],
                                'help' => '',
                                'child_of' => '',
                                'placement' => 'content',
                                'width' => '100',
                                'hide_if_not_selected' => 'false',
                                'save_in_meta' => 'false',
                                'save_in_meta_name' => '',
                                'required' => 'false',
                                'placeholder' => '',
                                'characters_limit' => '',
                            ),
                        ),
                        'code' => array(
                            'output_method' => 'php',
                            'editor_html' => '',
                            'editor_callback' => '',
                            'editor_css' => '',
                            'frontend_html' => '',
                            'frontend_callback' => array($this, 'frontend_callback'),
                            'frontend_css' => '',
                            'show_preview' => 'always',
                            'single_output' => false,
                        ),
                        'condition' => array(),
                    );

                    lazyblocks()->add_block($block_atts);
                    $this->add_to_svg_collection('lazyblock/reli-leitfragen-' . $post->post_name, $this->currentSvg);
                endif;
            }
        }

    }


    public function frontend_callback($p)
    {

        $svgSize = 72;
        $svg = $this->svgCollection[$p['lazyblock']['slug']];

        echo '<div class="rpi-question-grid">';
        echo '<div class="rpi-question-svg">' . $this->clean_up_svg($svg, $svgSize, $svgSize) . '</div>';
        echo '<h3 class="rpi-question-title">' . $p['title'] . '<h3>';
        echo '<div class="rpi-question-inner-block">' . $p['insertedblocks'] . '</div>';
        echo ' </div>';


        echo '<style>
                .rpi-question-grid{
                    display: grid;
                    grid-template-areas:    "svg title"
                                            "svg inner-block";
                    grid-template-columns: 20% 80%;
                    grid-gap: 10px;                                       
                }
                .rpi-question-svg{
                    grid-area: svg;
                    margin: 15px auto;
                    height: ' . $svgSize . 'px;
                    width: ' . $svgSize . 'px;
                }
                .rpi-question-title{
                margin-top: 15px !important;
                    grid-area: title;
                }
                .rpi-question-inner-block{
                    grid-area: inner-block;
                }
                @media (max-width: 800px) {
                
                    .rpi-question-svg{
                    height: 48px;
                    width : 48px;
                    }
                    .rpi-question-svg svg{
                    height: inherit;
                    width : inherit;
                    }
                }
            </style>';
    }

    /**
     * @param $svg
     * @return mixed
     */
    public function clean_up_svg($svg, $height = 24, $width = 24)
    {
        $svg = str_replace("\n", "", $svg);

        $re = '#height="[^"]*"#m';
        $svg = preg_replace($re, 'height="' . $height . '"', $svg);

        $re = '#width="[^"]*"#m';
        $svg = preg_replace($re, 'width="' . $width . '"', $svg);

        $re = '#<!\[CDATA\[[^\]]*\].?>#m';
        $svg = preg_replace($re, '', $svg);

        $re = '/(.*\W*?)(<svg)/m';

        return preg_replace($re, '$2', $svg);

    }
}

new RpiQuestionGenerator();