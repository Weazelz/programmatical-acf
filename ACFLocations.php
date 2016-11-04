<?php

namespace ACF;

class ACFLocations
{

    static function optionPage($postType = false, $ruleOperator = '==')
    {
        $base = 'acf-options';

        if (!$postType) {
            $value = $base;
        } else {
            $value = $base . '-' . $postType;
        }

        return [
            [
                'param'    => 'options_page',
                'operator' => $ruleOperator,
                'value'    => $value
            ]
        ];
    }

    static function postType($postType = 'post', $ruleOperator = '==')
    {
        return [
            'param'    => 'post_type',
            'operator' => $ruleOperator,
            'value'    => $postType
        ];
    }


    static function custom($param, $value, $ruleOperator = '==')
    {
        return [
            'param'    => $param,
            'operator' => $ruleOperator,
            'value'    => $value
        ];
    }

    static function pageType($pageType = 'front_page', $ruleOperator = '==')
    {
        if (is_string($pageType)) {
            return [
                'param'    => 'page_type',
                'operator' => $ruleOperator,
                'value'    => $pageType
            ];
        } else {
            $locationGroup = [];

            return $locationGroup;
        }
    }

    /**
     * @param string|array $templateName
     * @param string       $ruleOperator operators == and != apply
     *
     * @return array
     */
    static function template($templateName = 'index', $ruleOperator = '==')
    {

        if (is_string($templateName)) {
            return [
                'param'    => 'page_template',
                'operator' => $ruleOperator,
                'value'    => sanitize_title($templateName)
            ];

        } else {
            $locationGroup = [];

            return $locationGroup;
        }
    }

    /**
     * Shows/hides field on option page
     *
     * @param string $optionpageName
     * @param string $ruleOperator operators == and != apply
     *
     * @return array
     */
    static function option($optionpageName = '', $ruleOperator = '==')
    {
        return [
            [
                'param'    => 'options_page',
                'operator' => '==',
                'value'    => $optionpageName
            ]
        ];
    }
}
