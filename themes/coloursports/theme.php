<?php
defined('BASEPATH') or exit('No direct script access allowed');
return [
	'name' => 'ColourSports',
	'admin_logo' => '/assets/images/logo.png',
	'admin_icon' => '/assets/images/icon.png',
	'settings' => [
		'theme_options' => [
			'label'    => 'Home Page Layout',
			'desc'     => '',
			'icon'     => 'fa-fw fa fa-home',
			'segments' => [
				'sidebar'        => [
					'label'    => 'Sidebar',
					'icon'     => 'fa-fw fa fa-list-alt',
					'desc'     => '',
					'supports' => [
						'products',
						'new_products',
						'most_viewed',
						'trending_products',
						'daily_deals',
						'featured_products',
						'custom',
					],
				],
				'main_banner'    => [
					'label'    => 'Main Banner Area',
					'desc'     => '',
					'icon'     => 'fa-fw fa fa-picture-o',
					'sections' => [
						'slider' => [
							'label' => 'Main Slider',
							'desc'  => '',
							'type'  => 'slider',
						],
						'banner' => [
							'label' => 'Slider Side Banner',
							'desc'  => '',
							'type'  => 'custom',
						],
					],
				],
				'main'           => [
					'label'    => 'Main Sections',
					'icon'     => 'fa-fw fa fa-chrome',
					'desc'     => '',
					'supports' => [
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
				'footer_top'     => [
					'label'    => 'Footer Top',
					'icon'     => 'fa-fw fa fa-cog',
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
						'mailchimp',
					],
				],
				'footer_main'    => [
					'label'   => 'Footer Main',
					'icon'    => 'fa-fw fa fa-cog',
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
				'footer_bottom' => [
					'label'    => 'Footer Bottom',
					'desc'     => '',
					'sections' => [
						'bottom_links'  => [
							'label' => 'Footer Link',
							'icon'  => 'fa-fw fa fa-cog',
							'desc'  => '',
							'type'  => 'url_list',
						],
						'bottom_custom' => [
							'label' => 'Footer Custom',
							'icon'  => 'fa-fw fa fa-cog',
							'desc'  => '',
							'type'  => 'custom',
						],
						'copyright'     => [
							'label' => 'Copyright',
							'icon'  => 'fa-fw fa fa-copyright',
							'desc'  => '',
							'type'  => 'copyright',
						],
					],
				],
				'cart_settings' => [
					'label'    => 'Cart Settings',
					'desc'     => '',
					'icon'     => 'fa-fw fa fa-shopping-cart',
					'sections' => [
						'min_order' => [
							'label' => 'Minimum Order Amount',
							'desc'  => '',
							'type'  => 'text',
						]
					],
				],
			],
		],
	],
	'product_custom' => [
		'fields' => [
			[
				'id'         => 'seo_meta_title',
				'label'      => 'Seo Title',
				'type'       => 'text',
			],
			[
				'id'         => 'seo_meta_description',
				'label'      => 'Seo Description',
				'type'       => 'textarea',
				'attributes' => [ 'maxlength' => 160 ],
			],
			[
				'id'         => 'short_description',
				'label'      => 'Short Description',
				'type'       => 'editor',
			],
		],
	],
];
// End of file theme.php
