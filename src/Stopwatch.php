<?php
/**
 * Stopwatch plugin for Craft CMS 4.x
 *
 * This plugin calculates how much time it takes to read the page and watch the videos
 *
 * @link      percipio.london
 * @copyright Copyright (c) 2022 percipio.london
 */

namespace percipiolondon\stopwatch;

use craft\base\Model;
use percipiolondon\stopwatch\models\Settings;
use percipiolondon\stopwatch\services\StopwatchService;
use percipiolondon\stopwatch\twigextensions\StopwatchTwigExtension;

use Craft;
use craft\base\Plugin;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    percipio.london
 * @package   Craftstopwatch
 * @since     1.0.0
 *
 * @property  StopwatchService $stopwatch
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class Stopwatch extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Craftstopwatch::$plugin
     *
     * @var Stopwatch
     */
    public static Stopwatch $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSection = false;

    /**
     * @var \percipiolondon\typesense\models\Settings|Model|null
     */
    public static Settings|Model|null $settings = null;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Craftstopwatch::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'stopwatch' => StopwatchService::class,
        ]);

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new StopwatchTwigExtension());

        Craft::info(
            Craft::t(
                'stopwatch',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return Settings
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string|null The rendered settings HTML
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate(
            'stopwatch/settings',
            [
                'settings' => $this->settings,
            ]
        );
    }
}
