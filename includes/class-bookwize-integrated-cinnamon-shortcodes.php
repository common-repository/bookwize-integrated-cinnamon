<?php

class Bookwize_Integrated_Shortcodes
{

    public function __construct()
    {

        add_action('init', [&$this, 'init']);

    }

    public function init()
    {

        // Add Shortcodes
        add_shortcode('bw_myreservation', [&$this, 'bw_myreservation']);
        add_shortcode('bw_form', [&$this, 'bw_form']);
        add_shortcode('bw_integrated', [&$this, 'bw_integrated']);

    }


    /*
    * no camelcase in shortcode parameters
    */
    public function bw_myreservation()
    {
        $args = [
            'meta_key' => 'bookwize_integrated_page_type',
            'meta_value' => 'integrated',
            'post_type' => get_post_types(['public' => true]),
            'post_status' => 'any',
            'posts_per_page' => 1,
            'suppress_filters' => false
        ];
        $posts = @get_posts($args);
        if (isset($posts[0])) {
            $url= get_permalink($posts[0]->ID).'?page=MyReservation';
            return '<a href="'. $url .'">'. __("My Reservation", "Bookwize Liquid").'</a>';
        }
        return [];
    }

    public function bw_currency()
    {
        return bw_render('bookwize-public-header');
    }
    public function bw_form()
    {
        do_action('bookwize_enqueue_scripts_and_styles');
        return bw_render('bookwize-public-form');
    }

    public function bw_integrated(){
        do_action('bookwize_enqueue_scripts_and_styles');
        return bw_render('bookwize-public-display');
    }

}
