<?php
// functions.php

/**
 * ACF rule constant - hides all default fields except permalink and title
 */
use ACF\ACFGroup;
use ACF\ACFLocations;

$ACF_HIDE_ALL = [
    'the_content',
    'excerpt',
    'custom_fields',
    'discussion',
    'comments',
    'revisions',
    'author',
    'format',
    'categories',
    'tags',
    'send-trackbacks'
];


/*
 * Option page fields
 */
acf_add_options_page();
acf_add_options_sub_page('Pages');
acf_add_options_sub_page('Settings');

/**
 * Location rules
 */
$optionPageTheme = [
    ACFLocations::optionPage('theme')
];

$optionPageSettings = [
    ACFLocations::optionPage('settings')
];

/**
 * Option page groups and fields example
 *
 * Groups and fields will be rendered on the page in order you first registered the group/field
 *
 * Field order can be updated by just reordering group methods, groups might not update
 */
$opThemeGroup = new ACFGroup('theme_settings_group', 'Label', $optionPageTheme);

$opThemeGroup->addPostObject('op_theme_contact_page', 'Select contact page');
$opThemeGroup->addMessage('op_theme_msg', 'Some message example');
$opThemeGroup->addTextField('op_theme_text_field', 'Some text field', [
    'default_value' => 'default_calue',
    'width' => 50
]);

/**
 * Template groups and fields example
 */
$pageBackground = new ACFGroup('od_page_background', 'Page background image', [[ACFLocations::template('contact_form_template')]]);

$pageBackground->addImageField('page_background_image', 'Background image');


// your template file
get_field('op_theme_contact_page', 'options'); // Returns id of chosen post
get_field('page_background_image'); // Returns image id if current query loop item has that field

