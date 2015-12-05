<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('files')
    ->exclude('vendor')
    ->in(__DIR__);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        /**
         * symfony
         */
        // Ensure there is no code on the same line as the PHP open tag and it is followed by a blankline.
        'blankline_after_open_tag',
        // Remove duplicated semicolons.
        'duplicate_semicolon',
        // Removes extra empty lines.
        'extra_empty_lines',
        // PHP multi-line arrays should have a trailing comma.
        'multiline_array_trailing_comma',
        // There should be no empty lines after class opening brace.
        'no_blank_lines_after_class_opening',
        // There should not be blank lines between docblock and the documented element.
        'no_empty_lines_after_phpdocs',
        // Phpdocs short descriptions should end in either a full stop, exclamation mark, or question mark.
        'phpdoc_short_description',
        // Phpdocs should start and end with content, excluding the very first and last line of the docblocks.
        'phpdoc_trim',
        // Removes line breaks between use statements.
        'remove_lines_between_uses',
        // An empty line feed should precede a return statement.
        'return',
        // There should be exactly one blank line before a namespace declaration.
        'single_blank_line_before_namespace',
        // Convert double quotes to single quotes for simple strings.
        'single_quote',
        // Unused use statements must be removed.
        'unused_use',

        /**
         * contrib
         */
        // Concatenation should be used with at least one whitespace around.
        'concat_with_spaces',
        // Ensure there is no code on the same line as the PHP open tag.
        'newline_after_open_tag',
        // Ordering use statements.
        'ordered_use',
    ])
    ->setUsingCache(true)
    ->finder($finder);
