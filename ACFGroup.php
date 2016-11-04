<?php

namespace ACF;

/**
 * Lets developer to register fields in code the normal way, without touching admin panel.
 * This removes the DB split problem by always calling ACF fields from code, also no need to
 * export fields to code - more code less work!
 *
 * Class ACFGroup
 * @package Theme\Controllers\Template
 */
class ACFGroup
{

    private $name = '';

    /**
     * Holds group based options
     *
     * @var array
     */
    private $group = [];

    /**
     * Holds all registered fields data for this group
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Locations where to show these fields, use ACFLocation class to apply locations
     *
     * @var array
     */
    private $locations = [];

    /**
     * Menu order, when to show field group in Admin
     *
     * @var int|mixed|string
     */
    private $menuOrder = 0;

    /**
     * Elements that some might want to remove, like excerpt fields, default content editor etc
     *
     * @var array
     */
    private $hiddenOnScreenElements = [];

    /**
     * Array of ACFRepeater objects
     *
     * @var array
     */
    protected $repeaterObjects = [];

    /**
     * Is current group in option page
     *
     * @var bool
     */
    private $optionPage = false;

    /**
     * ACFGroup constructor.
     *
     * @param       $groupName
     * @param       $label
     * @param array $locations
     * @param array $hideOnScreen
     * @param array $options
     */
    function __construct($groupName, $label, $locations = [], $hideOnScreen = [], $options = [])
    {
        $this->name = $groupName;

        $this->group = [
            'key'                   => 'group_' . $groupName,
            'title'                 => $label,
            'fields'                => [],
            'menu_order'            => isset($options['menu_order']) ? (int)$options['menu_order'] : 0,
            'position'              => isset($options['position']) ? $options['position'] : 'acf_after_title',
            'style'                 => isset($options['style']) ? $options['style'] : 'default',
            'label_placement'       => isset($options['label_placement']) ? $options['label_placement'] : 'top',
            'instruction_placement' => isset($options['instruction_placement']) ? $options['instruction_placement'] : 'label',
            'hide_on_screen'        => [],
            'location'              => [],
            'active'                => isset($options['active']) ? $options['active'] : 1,
            'description'           => isset($options['description']) ? $options['description'] : '',
        ];

        $this->locations = $locations;

        if (isset($this->locations[0]['option_page'])) {
            $this->optionPage = true;
            unset($this->locations[0]['option_page']);
        }

        $this->menuOrder = isset($options['menu_order']) ? $options['menu_order'] : '';

        $this->hiddenOnScreenElements = $hideOnScreen;

        if (function_exists('add_action')) {
            add_action('init', [$this, 'registerFieldGroup'], 10);
        }

        global $acfGroups;

        $acfGroups->addGroup($this);
    }

    public function getGroupName()
    {
        return $this->group['key'];
    }

    /**
     * @return bool
     */
    public function isOptionPage()
    {
        return $this->optionPage;
    }

    function addGoogleMapField($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'google_map',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'center_lat'        => isset($options['center_lat']) ? $options['center_lat'] : '',
            'center_lng'        => isset($options['center_lng']) ? $options['center_lng'] : '',
            'zoom'              => isset($options['zoom']) ? $options['zoom'] : '',
            'height'            => isset($options['height']) ? $options['height'] : ''
        ];

        return $this;
    }

    /**
     * @param        $name
     * @param string $label
     * @param array  $selectChoices - key => value pairs where key is option tag `value` and value is shown to user
     * @param array  $options
     *
     * @return $this
     */
    function addSelectField($name, $label = '', $selectChoices = [], $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'select',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'choices'           => $selectChoices,
            'default_value'     => isset($options['default_value']) ? $options['default_value'] : [],
            'allow_null'        => isset($options['allow_null']) ? $options['allow_null'] : 0,
            'multiple'          => isset($options['multiple']) ? $options['multiple'] : 0,
            'ui'                => isset($options['ui']) ? $options['ui'] : 0,
            'ajax'              => isset($options['ajax']) ? $options['ajax'] : 0,
            'placeholder'       => isset($options['placeholder']) ? $options['placeholder'] : '',
            'disabled'          => isset($options['disabled']) ? $options['disabled'] : 0,
            'readonly'          => isset($options['readonly']) ? $options['readonly'] : 0,
        ];

        return $this;
    }

    /**
     * Adds a message row
     *
     * @param        $name
     * @param string $label
     * @param array  $options
     *
     * @return $this
     */
    public function addTab($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'tab',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'placement'         => isset($options['placement']) ? $options['placement'] : 'left', // top
            'endpoint'          => isset($options['endpoint']) ? $options['endpoint'] : 0
        ];

        return $this;
    }

    /**
     * Adds a message row
     *
     * @param        $name
     * @param string $label
     * @param string $message
     * @param array  $options
     *
     * @return $this
     */
    public function addMessage($name, $label = '', $message = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'message',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'message'           => $message,
            'new_lines'         => 'wpautop',
            'esc_html'          => isset($options['esc_html']) ? $options['esc_html'] : 0
        ];

        return $this;
    }

    /**
     * Registers a new field for this group
     *
     * @param string $name - Name of the field
     * @param array  $options
     *
     * @return $this
     */
    public function addImageField($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'image',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'return_format'     => isset($options['return_format']) ? $options['return_format'] : 'id', // url, array
            'preview_size'      => isset($options['preview_size']) ? $options['preview_size'] : 'thumbnail',
            'library'           => isset($options['library']) ? $options['library'] : 'all',
            'min_width'         => isset($options['min_width']) ? $options['min_width'] : '',
            'min_height'        => isset($options['min_height']) ? $options['min_height'] : '',
            'min_size'          => isset($options['min_size']) ? $options['min_size'] : '',
            'max_width'         => isset($options['max_width']) ? $options['max_width'] : '',
            'max_height'        => isset($options['max_height']) ? $options['max_height'] : '',
            'max_size'          => isset($options['max_size']) ? $options['max_size'] : '',
            'mime_types'        => isset($options['mime_types']) ? $options['mime_types'] : ''
        ];

        return $this;
    }

    /**
     *
     * @param string $name - Name of the field
     * @param array  $options
     *
     * @return $this
     */
    public function addGalleryField($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'gallery',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'min'           => isset($options['min']) ? $options['min'] : '',
            'max'           => isset($options['max']) ? $options['max'] : '',
            'insert'           => isset($options['insert']) ? $options['insert'] : 'append`',
            'library'           => isset($options['library']) ? $options['library'] : 'all',
            'min_width'         => isset($options['min_width']) ? $options['min_width'] : '',
            'min_height'        => isset($options['min_height']) ? $options['min_height'] : '',
            'min_size'          => isset($options['min_size']) ? $options['min_size'] : '',
            'max_width'         => isset($options['max_width']) ? $options['max_width'] : '',
            'max_height'        => isset($options['max_height']) ? $options['max_height'] : '',
            'max_size'          => isset($options['max_size']) ? $options['max_size'] : '',
            'mime_types'        => isset($options['mime_types']) ? $options['mime_types'] : ''
        ];

        return $this;
    }

    /**
     * Registers a generic new field for this group
     *
     * @param string $type - ACF field type
     * @param string $name - Name of the field
     * @param array  $options
     */
    protected function addField($type, $name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => $type,
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'default_value'     => isset($options['default_value']) ? $options['default_value'] : '',
            'placeholder'       => isset($options['placeholder']) ? $options['placeholder'] : '',
            'prepend'           => isset($options['prepend']) ? $options['prepend'] : '',
            'append'            => isset($options['append']) ? $options['append'] : '',
            'new_lines'         => isset($options['new_lines']) ? $options['new_lines'] : 'wpautop',
            'maxlength'         => isset($options['maxlength']) ? $options['maxlength'] : '',
            'readonly'          => isset($options['readonly']) ? $options['readonly'] : 0,
            'disabled'          => isset($options['disabled']) ? $options['disabled'] : 0
        ];
    }

    /**
     * Registers a generic new field for this group
     *
     * @param string $name
     * @param string $name - Name of the field
     * @param array  $options
     */
    function addDateTimePicker($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'date_time_picker',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'display_format'    => isset($options['display_format']) ? $options['display_format'] : 'd/m/Y G:i',
            'return_format'     => isset($options['return_format']) ? $options['return_format'] : 'd/m/Y G:i',
            'first_day'         => 1,
        ];
    }

    /**
     * Registers a generic new field for this group
     *
     * @param string $name
     * @param string $label - Name of the field
     * @param array  $options
     */
    function addDatePicker($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'date_picker',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'display_format'    => isset($options['display_format']) ? $options['display_format'] : 'd/m/Y',
            'return_format'     => isset($options['return_format']) ? $options['return_format'] : 'd/m/Y',
            'first_day'         => 1,
        ];
    }

    /**
     * Registers a generic new field for this group
     *
     * @param string $type - ACF field type
     * @param string $name - Name of the field
     * @param array  $options
     */
    function addBooleanField($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'true_false',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'message'           => isset($options['message']) ? $options['message'] : '',
            'default_value'     => isset($options['default_value']) ? $options['default_value'] : '',
        ];
    }

    /**
     * Registers a generic new field for this group
     *
     * @param string $type - ACF field type
     * @param string $name - Name of the field
     * @param array  $options
     */
    function addTaxonomyField($name, $label = '', $taxonomy, $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'taxonomy',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'taxonomy'          => $taxonomy,
            'field_type'        => isset($options['field_type']) ? $options['field_type'] : 'select',
            'allow_null'        => isset($options['allow_null']) ? $options['allow_null'] : 1,
            'add_term'          => isset($options['add_term']) ? $options['add_term'] : 0,
            'save_terms'        => isset($options['save_terms']) ? $options['save_terms'] : 0,
            'load_terms'        => isset($options['load_terms']) ? $options['load_terms'] : 0,
            'return_format'     => isset($options['return_format']) ? $options['return_format'] : 'id',
            'multiple'          => isset($options['multiple']) ? $options['multiple'] : 0
        ];
    }

    /**
     * Registers number field
     *
     * @param string $name - Name of the field
     * @param string $label
     * @param array  $options
     *
     * @return $this
     */
    function addNumberField($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'number',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'default_value'     => isset($options['default_value']) ? $options['default_value'] : '',
            'placeholder'       => isset($options['placeholder']) ? $options['placeholder'] : '',
            'prepend'           => isset($options['prepend']) ? $options['prepend'] : '',
            'append'            => isset($options['append']) ? $options['append'] : '',
            'min'               => isset($options['min']) ? $options['min'] : '',
            'max'               => isset($options['max']) ? $options['max'] : '',
            'step'              => isset($options['step']) ? $options['step'] : '',
            'readonly'          => isset($options['readonly']) ? $options['readonly'] : '',
            'disabled'          => isset($options['disabled']) ? $options['disabled'] : '',
        ];

        return $this;
    }

    function addFileUpload($name, $label, $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'file',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'return_format'     => isset($options['return_format']) ? $options['return_format'] : 'id', // array,url
            'library'           => isset($options['library']) ? $options['library'] : 'all',
            'min_size'          => isset($options['min_size']) ? $options['min_size'] : '',
            'max_size'          => isset($options['max_size']) ? $options['max_size'] : '',
            'mime_types'        => isset($options['mime_types']) ? $options['mime_types'] : '',
        ];

        return $this;
    }

    /**
     * Registers page link field
     *
     * @param string $name - Name of the field
     * @param string $label
     * @param array  $options
     *
     * @return $this
     */
    function addPageLink($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'page_link',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'post_type'         => isset($options['post_type']) ? $options['post_type'] : [],
            'taxonomy'          => isset($options['taxonomy']) ? $options['taxonomy'] : [], // ['product_type:grouped']
            'allow_null'        => 0,
            'multiple'          => 0,
        ];

        return $this;
    }

    /**
     * Registers post object field
     *
     * @param string $name - Name of the field
     * @param string $label
     * @param array  $options
     *
     * @return $this
     */
    function addPostObject($name, $label = '', $options = [])
    {
        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'post_object',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'post_type'         => isset($options['post_type']) ? $options['post_type'] : [],
            'taxonomy'          => isset($options['taxonomy']) ? $options['taxonomy'] : [], // ['product_type:grouped']
            'allow_null'        => 0,
            'multiple'          => 0,
            'return_format'     => isset($options['return_format']) ? $options['return_format'] : 'id', // 'object'
            'ui'                => 1,
        ];

        return $this;
    }

    /**
     * Registers text field
     *
     * @param        $name
     * @param string $label
     * @param array  $options
     *
     * @return $this
     */
    function addTextField($name, $label = '', $options = [])
    {

        $this->addField('text', $name, $label, $options);

        return $this;
    }

    /**
     * Registers url field
     *
     * @param        $name
     * @param string $label
     * @param array  $options
     *
     * @return $this
     */
    function addUrlField($name, $label = '', $options = [])
    {

        $this->addField('url', $name, $label, $options);

        return $this;
    }

    /**
     * registers textarea field
     *
     * @param        $name
     * @param string $label
     * @param array  $options
     *
     * @return $this
     */
    function addTextareaField($name, $label = '', $options = [])
    {
        $this->addField('textarea', $name, $label, $options);

        return $this;
    }

    /**
     * Registers WYSIWYG editor field
     *
     * @param        $name
     * @param string $label
     * @param array  $options
     *
     * @return $this
     */
    function addWysiwygField($name, $label = '', $options = [])
    {

        /*
         * 14.06.2016 /
         * This function was previously used like this: $this->addField('wysiwyg', $name, $label, $options)
         * Restructured the function because some more flexibility was needed for the $options
         *
         */

        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'wysiwyg',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => array(
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ),
            'default_value'     => isset($options['default_value']) ? $options['default_value'] : '',
            'tabs'              => isset($options['tabs']) ? $options['tabs'] : 0,
            'toolbar'           => isset($options['toolbar']) ? $options['toolbar'] : 0,
            'media_upload'      => isset($options['media_upload']) ? $options['media_upload'] : 0
        ];

        return $this;
    }

    /**
     * @param        $name
     * @param string $label
     * @param array  $options
     * @param        $acfAddSubfields - gives ACFRepeater class as parameter, works exactly like ACFGroup class
     */
    function addRepeaterField($name, $label = '', $acfAddSubfields, $options = [])
    {
        $acfRepeater = new ACFRepeater();

        /**
         * @param        ACFRepeater
         * */
        $acfAddSubfields($acfRepeater);

        $this->fields[$name] = [
            'key'               => 'field_' . $name,
            'label'             => $label,
            'name'              => $name,
            'type'              => 'repeater',
            'instructions'      => isset($options['instructions']) ? $options['instructions'] : '',
            'required'          => isset($options['required']) ? $options['required'] : 0,
            'conditional_logic' => isset($options['conditional_logic']) ? $options['conditional_logic'] : 0,
            'wrapper'           => [
                'width' => isset($options['width']) ? $options['width'] : 100,
                'class' => isset($options['class']) ? $options['class'] : '',
                'id'    => isset($options['id']) ? $options['id'] : '',
            ],
            'collapsed'         => isset($options['collapsed']) ? $options['collapsed'] : '',
            'min'               => isset($options['min']) ? $options['min'] : '',
            'max'               => isset($options['max']) ? $options['max'] : '',
            'layout'            => isset($options['layout']) ? $options['layout'] : 'block',
            'button_label'      => isset($options['button_label']) ? $options['button_label'] : 'Add row',
            'sub_fields'        => $acfRepeater->getSubfields()
        ];
    }

    /**
     * Lets secretly call registerFieldGroup, we dont want it to be called multiple times for single group
     *
     * @param $name
     * @param $arguments
     *
     * @throws \Exception
     */
    function __call($name, $arguments)
    {
        if ($name == 'registerFieldGroup') {
            $this->registerFieldGroup();
        }
    }

    /**
     * This adds all the registered fields into ACF function, without it ACF will not know about registered fields
     *
     * @throws \Exception
     */
    protected function registerFieldGroup()
    {
        if (function_exists('acf_add_local_field_group')) {

            $group = $this->group;

            $group['location'] = $this->locations;
            $group['menu_order'] = $this->menuOrder;
            $group['hide_on_screen'] = $this->hiddenOnScreenElements;

            $group['fields'] = $this->fields;

            $group['fields'] = array_values($group['fields']);

            acf_add_local_field_group($group);
        } else {
            throw new \Exception("Function for registering field group does not exists, either ACF PRO plugin is missing or not yet initialized");
        }
    }
}
