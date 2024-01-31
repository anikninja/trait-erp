<?php
defined('BASEPATH') or exit('No direct script access allowed');
return [
	'name' => 'RacketMayhem',
	'admin_logo' => '/assets/images/logo.png',
	'admin_icon' => '/assets/images/icon.png',
	'settings' => [
		'theme_options' => [
			'label'    => 'Home Page Layout',
			'desc'     => '',
			'icon'     => 'fa-fw fa fa-home',
			'segments' => [
                'header'         => [
                    'label'    => 'Header',
                    'icon'     => 'fa-fw fa fa-list-alt',
                    'desc'     => '',
                    'sections' => [
                        'top' => [
                            'label'  => 'Header Top',
                            'type'   => 'custom',
                        ],
                        'top_middle' => [
                            'label'  => 'Header Top Middle',
                            'type'   => 'custom',
                        ],
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
					],
				],
				'main'           => [
					'label'    => 'Main Sections',
					'icon'     => 'fa-fw fa fa-chrome',
					'desc'     => '',
					'supports' => [
						'products',
						'trending_products',
						'custom',
						'brand_slider',
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
				'label'      => 'Overview',
				'type'       => 'editor',
			],
			[
				'id'    => 'video_url',
				'label' => 'Video URL',
				'desc'  => 'Only Youtube Video Url Supported',
				'type'  => 'url',
			],
			[
				'id'     => 'specification',
				'label'  => 'Specification',
				'type'   => 'group',
				'repeat' => true,
				'fields'  => [
					[
						'id'    => 'label',
						'label' => 'Label',
						'type'  => 'text',
					],
					[
						'id'    => 'value',
						'label' => 'Value',
						'type'  => 'text',
					],
				],
			],
			[
				'id'     => 'additional_info',
				'label'  => 'Additional Information',
				'type'   => 'group',
				'fields' => [
					[
						'id'    => 'additional_title',
						'label' => 'Headline',
						'type'  => 'text',
					],
					[
						'id'    => 'additional_summery',
						'label' => 'Summery',
						'type'  => 'editor',
					],
					[
						'id'     => 'additional_features',
						'label'  => 'Features',
						'type'   => 'group',
						'fields' => [
							[
								'id'    => 'feature_desc',
								'label' => 'Short Description',
								'type'  => 'text',
							],
							[
								'id'     => 'feature',
								'label'  => '',
								'type'   => 'group',
								'repeat' => true,
								'fields' => [
									[
										'id'    => 'value',
										'label' => 'Feature',
										'type'  => 'text',
									],
								],
							],
						],
					],
				],
			],
			[
				'id'     => 'faq',
				'label'  => 'FAQ',
				'type'   => 'group',
				'repeat' => true,
				'fields' => [
					[
						'id'    => 'question',
						'label' => 'Question',
						'type'  => 'text',
					],
					[
						'id'    => 'answer',
						'label' => 'Answer',
						'type'  => 'editor',
					],
				],
			],
		],
	],
	'sub_nav' => true,
];
// End of file theme.php
