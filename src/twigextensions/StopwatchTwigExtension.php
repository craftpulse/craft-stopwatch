<?php
/**
 * Stopwatch plugin for Craft CMS 4.x
 *
 * This plugin calculates how much time it takes to read the page and watch the videos
 *
 * @link      percipio.london
 * @copyright Copyright (c) 2022 percipio.london
 */

namespace percipiolondon\stopwatch\twigextensions;

use craft\elements\Entry;
use percipiolondon\stopwatch\models\TimeModel;
use percipiolondon\stopwatch\Stopwatch;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    percipio.london
 * @package   Craftstopwatch
 * @since     1.0.0
 */
class StopwatchTwigExtension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'stopwatch';
    }

    /**
     * Returns an array of Twig filters, used in Twig templates via:
     *
     *      {{ 'something' | someFilter }}
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
//            new TwigFilter('someFilter', [$this, 'someInternalFunction']),
        ];
    }

    /**
     * Returns an array of Twig functions, used in Twig templates via:
     *
     *      {% set this = someFunction('something') %}
     *
    * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('stopwatch', [$this, 'calculateFunction']),
        ];
    }

    /**
     * @param mixed $element
     * @param bool $showpSeconds
     * @return TimeModel
     * @throws InvalidFieldException
     */
    public function calculateFunction(mixed $element, bool $showSeconds = true): TimeModel
    {
        return Stopwatch::$plugin->stopwatch->calculateReadTime($element, $showSeconds);
    }
}
