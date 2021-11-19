<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('files')
    ->exclude('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,

        /**
         * Array Notation.
         */
        // PHP arrays should be declared using the configured syntax.
        'array_syntax' => ['syntax' => 'short'],

        /**
         * Cast Notation.
         */
        // A single space should be between cast and variable.
        'cast_spaces' => true,

        /**
         * Class Notation.
         */
        // Methods must be separated with one blank line.
        'class_attributes_separation' => true,

        /**
         * Control Structure.
         */
        // PHP multi-line arrays should have a trailing comma.
        'trailing_comma_in_multiline' => true,

        /**
         * Function Notation.
         */
        // In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],

        /**
         * Import.
         */
        // Unused use statements must be removed.
        'no_unused_imports' => true,
        // Ordering use statements.
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        // Transforms imported FQCN parameters and return types in function arguments to short version.
        'fully_qualified_strict_types' => true,
        // Imports or fully qualifies global classes/functions/constants.
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => null,
            'import_functions' => null,
        ],

        /**
         * Operator.
         */
        // Unary operators should be placed adjacent to their operands.
        'unary_operator_spaces' => true,
        // Binary operators should be surrounded by at least one space.
        'binary_operator_spaces' => true,
        // Concatenation should be used with at least one whitespace around.
        'concat_space' => ['spacing' => 'one'],
        // Logical NOT operators `(!)` should have one trailing whitespace.
        'not_operator_with_successor_space' => true,

        /**
         * PHPDoc.
         */
        // Scalar types should always be written in the same form. int not integer, bool not boolean, float not real or double.
        'phpdoc_scalar' => true,
        // Single line @var PHPDoc should have proper spacing.
        'phpdoc_single_line_var_spacing' => true,
        // @var and @type annotations of classy properties should not contain the name.
        'phpdoc_var_without_name' => true,
        // There should not be blank lines between docblock and the documented element.
        'no_blank_lines_after_phpdoc' => true,
        // Phpdocs short descriptions should end in either a full stop, exclamation mark, or question mark.
        'phpdoc_summary' => true,
        // Phpdocs should start and end with content, excluding the very first and last line of the docblocks.
        'phpdoc_trim' => true,

        /**
         * Semicolon.
         */
        // Remove duplicated semicolons.
        'no_empty_statement' => true,

        /**
         * String Notation.
         */
        // Convert double quotes to single quotes for simple strings.
        'single_quote' => true,

        /**
         * Whitespace.
         */
        // An empty line feed should precede a return statement.
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        // Removes line breaks between use statements.
        'no_extra_blank_lines' => ['tokens' => ['use']],
    ])
    ->setFinder($finder)
    ->setUsingCache(true);
