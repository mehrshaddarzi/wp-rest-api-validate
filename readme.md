# Laravel Validation for WordPress REST API Plugin

![Packagist](https://img.shields.io/github/license/mehrshaddarzi/wp-trait)

Professional validation argument for WordPress REST API

## Table of Contents

* [Installation](#installation)
* [How to use in register rest route](#how-to-use-in-register_rest_route)
* [List of WordPress Hooks](#list-of-wordpress-hooks)
* [Contributing](#contributing)
* [License](#license)

## Installation

[Download zip file](https://github.com/mehrshaddarzi/wp-rest-api-validate/releases/download/v1.0.0/wp-rest-api-validate.zip) and install plugin in your WordPress site.

### How to use in register_rest_route

You Can add `validate` parameter in `register_rest_route` function:

```php
add_action('rest_api_init', 'register_routes');
function register_routes()
{

    register_rest_route(
        'v1',
        'book/add',
        array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'callback_function_name',
            'permission_callback' => '__return_true',
            'args' => [
                'isbn' => [
                    'title' => __('Book ISBN'),
                    'validate' => ['required', 'min:5']
                ],
                'image' => [
                    'title' => __('screenshot'),
                    'validate' => ['file', 'mimes:jpg,png']
                ],
                'meta' => [
                    'title' => __('meta'),
                    'validate' => 'required|array|min:3'
                ],
                'author' => [
                    'title' => __('Author Book'),
                    'validate' => ['required', function ($attribute, $value, $fail) {
                        if (strlen($value) < 10) {
                            $fail("Show Error in closure-based Validation");
                        }
                    }]
                ]
            ]
        )
    );
}
```

### List of Laravel Validation

All laravel validation Rules are available in this plugin:

https://laravel.com/docs/10.x/validation#available-validation-rules


### List of WordPress Hooks

#### Use custom parameter key in your code
```php
apply_filters('wp_rest_api_validate_args_key', 'validate');
```

#### Use custom language (default is WordPress locale)
```php
apply_filters('wp_rest_api_validate_locale', get_locale());
```

#### Change validation language dir
```php
apply_filters('wp_rest_api_validate_lang_dir', dirname(__FILE__) . '/lang');
```

#### Pre Validation Start

use for disable or add custom condition by request:

```php
apply_filters('wp_rest_api_validate_pre', 
    null, 
    \WP_REST_Response $response, 
    \WP_REST_Request $request, 
    $handler
);
```

## Contributing

- [Mehrshad Darzi](https://www.linkedin.com/in/mehrshaddarzi/)

We appreciate you taking the initiative to contribute to this project.
Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by
writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our
documentation.

## License

The `wp-rest-api-validate` is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

