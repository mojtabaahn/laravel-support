<?php

/*
 * NOTICE OF LICENSE
 *
 * Part of the Rinvex Support Package.
 *
 * This source file is subject to The MIT License (MIT)
 * that is bundled with this package in the LICENSE file.
 *
 * Package: Rinvex Support Package
 * License: The MIT License (MIT)
 * Link:    https://rinvex.com
 */

declare(strict_types=1);

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

if (! function_exists('intend')) {
    /**
     * Return redirect response.
     *
     * @param array       $arguments
     * @param string|null $statusCode
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    function intend(array $arguments, $statusCode = null)
    {
        $redirect = redirect(array_pull($arguments, 'url'));
        $statusCode = $statusCode ?: isset($arguments['withErrors']) ? 422 : 200;

        if (request()->expectsJson()) {
            return new JsonResponse($arguments['withErrors'] ?: $arguments['with'] ?: 'OK', $statusCode);
        }

        foreach ($arguments as $key => $value) {
            $redirect = in_array($key, ['home', 'back']) ? $redirect->{$key}() : $redirect->{$key}($value);
        }

        return $redirect;
    }
}

if (! function_exists('lower_case')) {
    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     *
     * @return string
     */
    function lower_case($value)
    {
        return Str::lower($value);
    }
}

if (! function_exists('upper_case')) {
    /**
     * Convert the given string to upper-case.
     *
     * @param string $value
     *
     * @return string
     */
    function upper_case($value)
    {
        return Str::upper($value);
    }
}

if (! function_exists('mimetypes')) {
    /**
     * Get valid mime types.
     *
     * @see https://github.com/symfony/http-foundation/blob/3.0/File/MimeType/MimeTypeExtensionGuesser.php
     * @see http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
     *
     * @return array
     */
    function mimetypes()
    {
        return json_decode(file_get_contents(__DIR__.'/../../resources/data/mimetypes.json'), true);
    }
}

if (! function_exists('timezones')) {
    /**
     * Get valid timezones.
     * This list is based upon the timezone database version 2016.1.
     *
     * @see http://php.net/manual/en/timezones.php
     *
     * @return array
     */
    function timezones()
    {
        return json_decode(file_get_contents(__DIR__.'/../../resources/data/timezones.json'), true);
    }
}

if (! function_exists('array_search_recursive')) {
    /**
     * Recursively searches the array for a given value and returns the corresponding key if successful.
     *
     * @param mixed $needle
     * @param array $haystack
     *
     * @return mixed
     */
    function array_search_recursive($needle, $haystack)
    {
        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value || (is_array($value) && array_search_recursive($needle, $value) !== false)) {
                return $current_key;
            }
        }

        return false;
    }
}

if (! function_exists('array_trim_recursive')) {
    /**
     * Recursively trim elements of the given array.
     *
     * @param mixed  $values
     * @param string $charlist
     *
     * @return mixed
     */
    function array_trim_recursive($values, $charlist = " \t\n\r\0\x0B")
    {
        if (is_array($values)) {
            return array_map('array_trim_recursive', $values);
        }

        return is_string($values) ? trim($values, $charlist) : $values;
    }
}

if (! function_exists('array_filter_recursive')) {
    /**
     * Recursively filter empty strings and null elements of the given array.
     *
     * @param array $values
     * @param bool  $strOnly
     *
     * @return mixed
     */
    function array_filter_recursive($values, $strOnly = true)
    {
        foreach ($values as &$value) {
            if (is_array($value)) {
                $value = array_filter_recursive($value);
            }
        }

        return ! $strOnly ? array_filter($values) : array_filter($values, function ($item) {
            return ! is_null($item) && ! ((is_string($item) || is_array($item)) && empty($item));
        });
    }
}
