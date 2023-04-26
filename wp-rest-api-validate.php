<?php
/*
 * Plugin Name:       WordPress REST API Validate
 * Plugin URI:        https://github.com/mehrshaddarzi/wp-rest-api-validate
 * Description:       Laravel Validation for WordPress REST API
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            Mehrshad Darzi
 * Author URI:        https://github.com/mehrshaddarzi
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-rest-api-validate
 * Domain Path:       /text-domain
 */

class WP_REST_API_Validate
{

    public function __construct()
    {
        if (apply_filters('wp_rest_api_validate_enable', true)) {
            add_filter('rest_request_after_callbacks', [$this, 'rest_request_after_callbacks'], 10, 3);
        }
    }

    public function args_validate_key()
    {
        return apply_filters('wp_rest_api_validate_args_key', 'validate');
    }

    public function args_validate_title_key()
    {
        return apply_filters('wp_rest_api_validate_args_title_key', 'title');
    }

    public function get_request_locale()
    {
        $lang = get_locale();
        if (strlen($lang) > 0) {
            $lang = explode('_', $lang);
        }

        return apply_filters('wp_rest_api_validate_locale', $lang[0]);
    }

    public static function get_validation_lang_dir()
    {
        return apply_filters('wp_rest_api_validate_lang_dir', dirname(__FILE__) . '/lang');
    }

    public function rest_request_after_callbacks($response, $handler, \WP_REST_Request $request)
    {
        // Check WP_Error
        if ($response instanceof WP_Error) {
            return $response;
        }

        // Get Request Attribution
        $get_attributes = $request->get_attributes();

        // Check Empty Attribute
        if (empty($get_attributes['args'])) {
            return $response;
        }

        // Pre Validate
        $pre = apply_filters('wp_rest_api_validate_pre', null, $response, $request, $handler);
        if (!is_null($pre)) {
            return $pre;
        }

        // Require Validation Package
        require_once dirname(__FILE__) . '/libs/vendor/autoload.php';
        require_once dirname(__FILE__) . '/wp-rest-api-validate-factory.php';

        // Setup Validate Arguments
        $files = [];
        foreach ($request->get_file_params() as $Input => $FILE) {
            $files[$Input] = new \Symfony\Component\HttpFoundation\File\UploadedFile($FILE['tmp_name'], $FILE['name'], $FILE['type'], $FILE['error']);
        }
        $data = array_merge($request->get_params(), $files);
        $rules = [];
        $attributes = [];

        // Get List Of Validate From Request
        foreach ($get_attributes['args'] as $key => $properties) {
            if (!empty($properties[$this->args_validate_key()])) {

                // Set Rules
                $rules[$key] = $properties[$this->args_validate_key()];

                // Set Attribute
                if (!empty($properties[$this->args_validate_title_key()])) {
                    $attributes[$key] = $properties[$this->args_validate_title_key()];
                }

            }
        }

        // Create Object Instance
        $validator = (new WP_REST_API_Validate_Factory($this->get_request_locale()))
            ->make($data, $rules, [], $attributes);

        // Check Success
        if ($validator->passes()) {
            return $response;
        }

        // Get Error Messages
        $messages = $validator->errors()->getMessages();

        // Show Error
        return new WP_Error(
            'rest_invalid_param',
            /* translators: %s: List of invalid parameters. */
            sprintf(__('Invalid parameter(s): %s'), implode(', ', array_keys($messages))),
            array(
                'status' => 400,
                'params' => $messages
            )
        );
    }
}

new WP_REST_API_Validate();
