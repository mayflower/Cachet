<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Exception Transformers
    |--------------------------------------------------------------------------
    |
    | Here are each of the exception transformers setup for your application.
    |
    | This allows you to turn your exceptions into other exceptions such as
    | http exceptions for perfect results when passed to the displayers. Note
    | that this list is processed in order and subsequent transformers can
    | still modify the results of previous ones if required.
    |
    */

    'transformers' => [
        'GrahamCampbell\Exceptions\Transformer\AuthTransformer',
        'GrahamCampbell\Exceptions\Transformer\CsrfTransformer',
        'GrahamCampbell\Exceptions\Transformer\ModelTransformer',
        'CachetHQ\Cachet\Exceptions\Transformers\BusTransformer',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exception Displayers
    |--------------------------------------------------------------------------
    |
    | Here are each of the exception displayers setup for your application.
    |
    | These displayers are sorted by priority. Note that when we are in debug
    | mode, we will select the first valid displayer from the list, and when we
    | are not in debug mode, we'll filter out all verbose displayers, then
    | select the first valid displayer from the new list.
    |
    */

    'displayers' => [
        'CachetHQ\Cachet\Exceptions\Displayers\MaintenanceDisplayer',
        'CachetHQ\Cachet\Exceptions\Displayers\SettingsDisplayer',
        'CachetHQ\Cachet\Exceptions\Displayers\RedirectDisplayer',
        'CachetHQ\Cachet\Exceptions\Displayers\ThrottleDisplayer',
        'CachetHQ\Cachet\Exceptions\Displayers\JsonValidationDisplayer',
        'GrahamCampbell\Exceptions\Displayer\DebugDisplayer',
        'GrahamCampbell\Exceptions\Displayer\HtmlDisplayer',
        'GrahamCampbell\Exceptions\Displayer\JsonDisplayer',
        'GrahamCampbell\Exceptions\Displayer\JsonApiDisplayer',
    ],

    /*
    |--------------------------------------------------------------------------
    | Displayer Filters
    |--------------------------------------------------------------------------
    |
    | Here are each of the filters for the displayers.
    |
    | This allows you to apply filters to your displayers in order to work out
    | which displayer to use for each exception. This includes things like
    | content type negotiation.
    |
    */

    'filters' => [
        'GrahamCampbell\Exceptions\Filter\VerboseFilter',
        'GrahamCampbell\Exceptions\Filter\CanDisplayFilter',
        'GrahamCampbell\Exceptions\Filter\ContentTypeFilter',
        'CachetHQ\Cachet\Exceptions\Filters\ApiFilter',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Displayer
    |--------------------------------------------------------------------------
    |
    | Here you may define the default displayer for your application.
    |
    | This displayer will be used if your filters have filtered out all the
    | displayers, otherwise leaving us unable to displayer the exception.
    |
    */

    'default' => 'GrahamCampbell\Exceptions\Displayer\HtmlDisplayer',

    /*
    |--------------------------------------------------------------------------
    | Exception Levels
    |--------------------------------------------------------------------------
    |
    | Here are each of the log levels for the each exception.
    |
    | If an exception passes an instance of test for each key, then the log
    | level used is the value associated with each key.
    |
    */

    'levels' => [
        'Illuminate\Auth\Access\AuthorizationException'                 => 'warning',
        'Illuminate\Database\Eloquent\ModelNotFoundException'           => 'warning',
        'Illuminate\Session\TokenMismatchException'                     => 'notice',
        'Symfony\Component\HttpKernel\Exception\HttpExceptionInterface' => 'notice',
        'Symfony\Component\Debug\Exception\FatalErrorException'         => 'critical',
        'Exception'                                                     => 'error',
    ],

];
