<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('pagination')) {
    function pagination($uri, $total, $per_page, $args = [])
    {
        $ci = &get_instance();
        $ci->load->library('pagination');
        $config                         = [];
        $config['base_url']             = site_url($uri);
        $config['total_rows']           = $total;
        $config['per_page']             = $per_page;
        $config['full_tag_open']        = '<ul class="pagination">';
        $config['full_tag_close']       = '</ul>';
        $config['first_tag_open']       = '<li class="first">';
        $config['first_tag_close']      = '</li>';
        $config['last_tag_open']        = '<li class="last">';
        $config['last_tag_close']       = '</li>';
        $config['next_tag_open']        = '<li class="next">';
        $config['next_tag_close']       = '</li>';
        $config['prev_tag_open']        = '<li class="prev">';
        $config['prev_tag_close']       = '</li>';
        $config['cur_tag_open']         = '<li class="active"><a>';
        $config['cur_tag_close']        = '</a></li>';
        $config['num_tag_open']         = '<li class="page">';
        $config['num_tag_close']        = '</li>';
        $config['page_query_string']    = true;
        $config['use_page_numbers']     = true;
        $config['query_string_segment'] = 'page';
        $config['first_link']           = '<i class="fa fa-angle-double-left"></i>';
        $config['last_link']            = '<i class="fa fa-angle-double-right"></i>';
        $config['prev_link']            = '<i class="fa fa-angle-left"></i>';
        $config['next_link']            = '<i class="fa fa-angle-right"></i>';
        $config = array_merge( $config, $args );
        $ci->pagination->initialize($config);
        return $ci->pagination->create_links();
    }
}
