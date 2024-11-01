<?php 
/* section main */
if ( !defined('ABSPATH')) exit;
$setting = $p->add_section(array(
	'option_group'      =>  'simple_lyteLoad',
	'sanitize_callback' => null,
	'id'                => 'simple_lyteLoad', 
	'title'             => __('Lyte Load settings')
	)
);
//text field
$p->add_field(array(
	'label'   => __('lyte load css class'),
	'std'     => 'lyte',
	'id'      => 'cssClass',
	'type'    => 'text',
	'section' => $setting,
	'desc'    => __('CSS selector class to use , default (lyte)')
	)
);
//checkbox
$p->add_field(array(
	'label'   => __('Lyte Load Avatars?'),
	'std'     => false,
	'id'      => 'avatar',
	'type'    => 'checkbox',
	'section' => $setting,
	'desc'    => __('Check to enable avatar Lyte Loading')
	)
);

//checkbox
$p->add_field(array(
	'label'   => __('Lyte Load post thumbnail?'),
	'std'     => false,
	'id'      => 'thumbnail',
	'type'    => 'checkbox',
	'section' => $setting,
	'desc'    => __('Check to enable post thumbnail Lyte Loading, when using <code>the_post_thumbnail()</code> or <code>get_post_thumbnail()</code>')
	)
);

//checkbox
$p->add_field(array(
	'label'   => __('Lyte Load post content images?'),
	'std'     => true,
	'id'      => 'the_content',
	'type'    => 'checkbox',
	'section' => $setting,
	'desc'    => __('Check to enable post content images Lyte Loading, this is where most of your images should be')
	)
);

//image
$p->add_field(array(
	'label'   => __('default Image'),
	'id'      => 'defImg',
	'type'    => 'image',
	'section' => $setting,
	'desc'    => __('You can chage the default image place hollder by uploading your own.')
	)
);