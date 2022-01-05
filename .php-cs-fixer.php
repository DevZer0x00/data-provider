<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('tests/_support/_generated');

$config = (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PHP80Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PSR12' => true,
        '@PSR12:risky' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'ordered_interfaces' => ['order' => 'alpha', 'direction' => 'ascend'],
        'linebreak_after_opening_tag' => true,
        'mb_str_functions' => true,
        'no_php4_constructor' => true,
        'no_unreachable_default_argument_value' => true,
        'native_function_invocation' => false,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_closing_tag' => true,
        'phpdoc_order' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'cast_spaces' => ['space' => 'none'],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        'concat_space' => ['spacing' => 'one'],
        'php_unit_strict' => false,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'this'],
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/var/.php_cs.cache');

return $config;
