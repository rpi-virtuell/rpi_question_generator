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

        add_action('enqueue_block_assets', array($this, 'enqueue_block_scripts'));

        add_action('init', array($this, 'register_acf_field_group'));
        add_action('init', array($this, 'register_custom_post_type'));
        add_action('init', array($this, 'create_blocks'));
        add_action('init', array($this, 'create_default_block'));
	    add_action( 'wp_ajax_getLeitfrage', array( $this, 'getLeitfrage' ));
    }

    public function enqueue_block_scripts()
    {

        if (!is_admin()) return;

        wp_enqueue_style(
            'rpi-question-style',
            plugin_dir_url(__FILE__) . '/assets/css/rpi-question.css'
        );
        wp_enqueue_script(
            'rpi-question',
            plugin_dir_url(__FILE__) . '/assets/js/rpi-question.js'
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
                        'type' => 'text',
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
                        'rows' => 1,
                        'new_lines' => '',
                        'acfe_textarea_code' => 0,
                    ),
                    array(
                        'key' => 'field_leitfrage_block_icon',
                        'label' => 'Block Icon',
                        'name' => 'block_icon',
                        'type' => 'textarea',
                        'instructions' => 'Wähle <a target="_blank" href="https://fonts.google.com/icons?selected=Material+Symbols+Outlined:live_help:FILL@0;wght@500;GRAD@0;opsz@24&icon.set=Material+Symbols">hier ein Icon</a>, <b>Rechtsklick</b> unten rechts auf <b>SVG</b> -> <b>in neuem Tab offnen</b>, dann <b>Rechtsklick</b> auf das Icon ->  <b>Seitenquelltext anzeigen</b>. Den <b>Seitenquelltext kopieren</b> dann in das Eingabefeld <b>einfügen</b>.',
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
                        'rows' => 5,
                        'max_rows' => '',
                        'return_entities' => 0,
                        'acfe_textarea_code' => 1,
                    ),
	                array(
		                'key' => 'field_leitfrage_multiple',
		                'label' => 'Mehrfach verwenden',
		                'name' => 'multiple',
		                'type' => 'true_false',
		                'instructions' => '',
		                'required' => 0,
		                'conditional_logic' => 0,
		                'wrapper' => array(
			                'width' => '',
			                'class' => '',
			                'id' => '',
		                ),
		                'frontend_admin_display_mode' => 'edit',
		                'only_front' => 0,
		                'message' => 'Erlaube, dass dieser Block in einem Material mehrfach verwendet werden darf',
		                'default_value' => 0,
		                'ui' => 0,
		                'ui_on_text' => '',
		                'ui_off_text' => '',
	                ),
                    array(
		                'key' => 'field_leitfrage_minimum_characters',
		                'label' => 'Mindestanzahl an Zeichen',
		                'name' => 'minimum_characters',
		                'type' => 'number',
		                'instructions' => 'Gebe die Mindestanzahl an Zeichen an, welche ein Block haben sollte, Um bei der Materialerstellung als gefüllt zu gelten.',
		                'required' => 0,
		                'conditional_logic' => 0,
		                'wrapper' => array(
			                'width' => '',
			                'class' => '',
			                'id' => '',
		                ),
                        'default_value' => '0',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'min' => '',
                        'max' => '',
                        'step' => '',
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
            "menu_icon" => "dashicons-lightbulb",
            "supports" => ["title", "editor"],
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
            $is_multiple = isset($fields['multiple']) && $fields['multiple'] ? true :false;

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
                            'multiple' => $is_multiple,
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
                            'control_' . $block_id . 'c' => array(
	                            'type' => 'toggle',
	                            'name' => 'is_teaser',
	                            'default' => '',
	                            'label' => 'Der Inhalt des Blocks ist der Teaser',
	                            'help' => '',
	                            'child_of' => '',
	                            'placement' => 'inspector',
	                            'width' => '100',
	                            'hide_if_not_selected' => 'false',
	                            'save_in_meta' => 'false',
	                            'save_in_meta_name' => '',
	                            'required' => 'false',
	                            'checked' => 'false',
	                            'alongside_text' => '',
	                            'placeholder' => '',
	                            'characters_limit' => '',
                            ),
                            'control_' . $block_id . 'd' => array(
	                            'type' => 'hidden',
	                            'name' => 'template',
	                            'default' => '',
	                            'label' => '',
	                            'help' => '',
	                            'child_of' => '',
	                            'placement' => 'inspector',
	                            'width' => '100',
	                            'hide_if_not_selected' => 'false',
	                            'save_in_meta' => 'false',
	                            'save_in_meta_name' => '',
	                            'required' => 'false',
	                            'placeholder' => '',
	                            'characters_limit' => '',
                            ),
                            'control_' . $block_id . 'e' => array(
	                            'type' => 'hidden',
	                            'name' => 'is_valid',
	                            'default' => false,
	                            'label' => '',
	                            'help' => '',
	                            'child_of' => '',
	                            'placement' => 'inspector',
	                            'width' => '100',
	                            'hide_if_not_selected' => 'false',
	                            'save_in_meta' => 'false',
	                            'save_in_meta_name' => '',
	                            'required' => 'false',
	                            'placeholder' => '',
	                            'characters_limit' => '',
                            ),
                            'control_' . $block_id . 'f' => array(
	                            'type' => 'hidden',
	                            'name' => 'minimum_characters',
	                            'default' => $fields['minimum_characters'],
	                            'label' => '',
	                            'help' => '',
	                            'child_of' => '',
	                            'placement' => 'inspector',
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

    public function create_default_block()
    {

        if (function_exists('lazyblocks')) :
            $block_id = random_int(10, 99999999);

            $this->set_svg(file_get_contents(plugin_dir_path(__FILE__) . '/assets/029-query.svg'));

            $block_atts = array(
                'id' => $block_id,
                'title' => 'getBlockIds Block',
                'icon' => $this->clean_up_svg($this->currentSvg),
                'keywords' => array(),
                'slug' => 'lazyblock/reli-default-block',
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
                    'multiple' => true,
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
                        'default' => 'Standard Block',
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
            $this->add_to_svg_collection('lazyblock/reli-default-block', $this->currentSvg);
        endif;

    }


    public function frontend_callback($p)
    {
        if (empty(trim(strip_tags($p['insertedblocks'],array('<img>',' <figure>')))))
            return;
        $svgSize = 40;
        $svg = $this->svgCollection[$p['lazyblock']['slug']];

        echo '<div class="rpi-question-grid">';
        echo '<div class="rpi-question-svg" style="background-image:url(data:image/svg+xml;base64,'.base64_encode($svg).');background-size: contain;"></div>';
        echo '<h3 class="rpi-question-title">' . $p['title'] . '</h3>';
        echo '<div class="rpi-question-inner-block">' . $p['insertedblocks'] . '</div>';
        echo ' </div>';

        echo '<style>
                .rpi-question-grid{
                    display: grid;
                    grid-template-areas:    "svg title"
                                            "svg inner-block";
                    grid-template-columns: 40px auto;
                    grid-gap: 10px;                                       
                }
                .rpi-question-svg{
                    grid-area: svg;
                    margin: 20px auto 0;
                    height: ' . $svgSize . 'px;
                    width: ' . $svgSize . 'px;
                }
                .rpi-question-title{
                	margin-top: 15px !important;
                    grid-area: title;
                    font-size: 30px;
                    font-weight: normal;
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
	/**
	 * ajax response displays Leitfrage Contents
	 */
	public function getLeitfrage(){

		if(isset($_POST['slug'])){
			$slug = $_POST['slug'];

			$result = get_posts([
				'post_type'=> 'leitfragenblocks',
				'name'=>$slug
			]);
			$post = isset($result[0])?$result[0]:null;
			if(is_a($result[0],'WP_Post')){
				$title = $post->post_title;
				$leitfrage = get_field('leitfrage',$post->ID, true);
				$icon = get_field('block_icon',$post->ID, true);
				$content = apply_filters('the_content',$post->post_content);
				?>
                    <article class="modal-helper-article">
                        <div class="modal-helper-header">
                            <div>
                                <?php echo $icon; ?>
                            </div>
                            <h1><?php echo $title ?></h1>
                            <div></div>
                            <p class="leitfrage"><?php echo $leitfrage ?></p>
                        </div>
                        <div class="modal-helper-content">
                            <div class="modal-helper-inner-content">
		                        <?php echo $content; ?>
                            </div>
                        </div>
                    </article>
				<?php
				die();
			}else{
				echo 'Es wurde nichts gefunden';
				die();
			}
		};
        var_dump($_POST);die();

	}
}

new RpiQuestionGenerator();
