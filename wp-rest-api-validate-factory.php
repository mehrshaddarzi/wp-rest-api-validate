<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Validation;
use Illuminate\Translation;
use Illuminate\Filesystem\Filesystem;

class WP_REST_API_Validate_Factory
{
    private Validation\Factory $factory;

    public function __construct($lang = 'en')
    {
        $this->factory = new Validation\Factory(
            $this->loadTranslator($lang)
        );
    }

    protected function loadTranslator($lang = 'en'): Translation\Translator
    {
        $filesystem = new Filesystem();
        $loader = new Translation\FileLoader($filesystem, WP_REST_API_Validate::get_validation_lang_dir());
        $loader->addNamespace('lang', __FILE__ . '/lang');
        $loader->load($lang, 'validation', 'lang');
        return new Translation\Translator($loader, $lang);
    }

    public static function get_request_params(): array
    {
        return \Symfony\Component\HttpFoundation\Request::createFromGlobals()->request->all();
    }

    public function __call($method, $args)
    {
        return call_user_func_array(
            [$this->factory, $method],
            $args
        );
    }
}