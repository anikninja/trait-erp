<?php
defined('BASEPATH') or exit('No direct script access allowed');

return [
	'name'     => 'Grocerant',
	'settings' => [
		'theme_options' => [
			'label'    => 'Home Page Layout',
			'desc'     => '',
			'icon'     => 'fa-fw fa fa-home',
			'segments' => [
				'sidebar'     => [
					'label'    => 'Sidebar',
					'icon'     => 'fa-fw fa fa-list-alt',
					'desc'     => '',
					'supports' => [
						'daily_deals',
						'products',
						'custom',
					],
				],
				'slider'      => [
					'label'       => 'Main Slider',
					'icon'        => 'fa-fw fa fa-picture-o',
					'desc'        => '',
					'supports'    => [ 'slider' ],
					'max_section' => 1,
				],
				'main'        => [
					'label'    => 'Main Sections',
					'icon'     => 'fa-fw fa fa-chrome',
					'desc'     => '',
					'supports' => [
						'slider',
						// 'page',
						'categories',
						'products',
						'new_products',
						'most_viewed',
						'trending_products',
						'daily_deals',
						'featured_products',
						'custom',
					],
				],
				'footer_top'  => [
					'label'    => 'Footer Top',
					'icon'        => 'fa-fw fa fa-cog',
					'desc'     => '',
					'supports' => [
						'products',
						'new_products',
						'most_viewed',
						'trending_products',
						'daily_deals',
						'featured_products',
						'custom',
						'brand_slider',
					],
				],
				'footer_main' => [
					'label'   => 'Footer Main',
					'icon'        => 'fa-fw fa fa-cog',
					'desc'    => '',
					'widgets' => [
						[
							'label'    => 'Widget 1',
							'desc'     => '',
							'supports' => [
								'custom',
								'url_list',
							],
						],
						[
							'label'    => 'Widget 2',
							'desc'     => '',
							'supports' => [
								'custom',
								'url_list',
							],
						],
						[
							'label'    => 'Widget 3',
							'desc'     => '',
							'supports' => [
								'custom',
								'url_list',
							],
						],
						[
							'label'    => 'Widget 4',
							'desc'     => '',
							'supports' => [
								'custom',
								'url_list',
							],
						],
					],
				],
				'footer_bottom'   => [
					'label'       => 'Footer Bottom',
					'icon'        => 'fa-fw fa fa-cog',
					'desc'        => '',
					'widgets'     => [
						[
							'label'    => 'Copyright',
							'desc'     => '',
							'supports'    => [ 'copyright' ],
							'max_section' => 1,
						],
						[
							'label'    => 'Extra',
							'desc'     => '',
							'supports'    => [ 'custom' ],
							'max_section' => 1,
						],
					],
				],
				'pdf_settings' => [
					'label'    => 'PDF Template',
					'desc'     => '',
					'icon'     => 'fa-fw fa fa-shopping-cart',
					'sections' => [
						'template_type' => [
							'label' => 'Select Type',
							'desc'  => '',
							'type'  => 'selectpdf',
						]
					],
				],
			],
		],
	],
	'sub_nav' => false,
];
// End of file config.php
