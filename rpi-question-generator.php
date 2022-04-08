<?php

/*
Plugin Name: Rpi Question Generator
Plugin URI: https://github.com/rpi-virtuell/rpi_question_generator
Description: A brief description of the Plugin.
Version: 1.0
Author: reintanz
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

class RpiQuestionGenerator
{

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


    }

    public function crate_blocks()
    {
        foreach (get_field('Leitfragen') as $fragen) {

            if (function_exists('lazyblocks')) :

                BUGFU::log($fragen);


            endif;

        }
    }
}

new RpiQuestionGenerator();