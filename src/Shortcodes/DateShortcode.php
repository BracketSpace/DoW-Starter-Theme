<?php

declare(strict_types=1);

namespace DoWStarterTheme\Shortcodes;

/**
 * DateShortcode class
 *
 * @phpstan-type Atts array{format: string}
 * @extends Shortcode<Atts>
 */
class DateShortcode extends Shortcode
{
    /**
     * Shortcode tag
     *
     * @var string
     */
    protected string $tag = 'date';

    /**
     * Shortcode attributes
     *
     * @var Atts
     */
    protected $atts = [
        'format' => 'Y',
    ];

    /**
     * Render callback.
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     *
     * @param  Atts   $atts    Shortcode attributes.
     * @param  string $content Shortcode content.
     * @return void
     */
    protected function render($atts, $content)
    {
        echo esc_html((string)current_time($atts['format']));
    }
}
