<?php
/**
 * Stopwatch plugin for Craft CMS 4.x
 *
 * This plugin calculates how much time it takes to read the page and watch the videos
 *
 * @link      percipio.london
 * @copyright Copyright (c) 2022 percipio.london
 */

namespace percipiolondon\stopwatch\services;

use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\base\Component;
use craft\elements\MatrixBlock;
use craft\fields\Matrix;
use craft\fields\Assets;
use craft\helpers\StringHelper;
use percipiolondon\stopwatch\models\TimeModel;
use percipiolondon\stopwatch\Stopwatch;
use spicyweb\embeddedassets\Plugin;
use yii\base\ErrorException;

/**
 * CraftstopwatchService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    percipio.london
 * @package   Craftstopwatch
 * @since     1.0.0
 */
class StopwatchService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     * Stopwatch::$plugin->stopwatch->calculateReadTime()
     *
     * @return mixed
     * @throws \craft\errors\InvalidFieldException
     */
    public function calculateReadTime(Entry|array $element, bool $showSeconds = true): TimeModel
    {
        $totalSeconds = 0;

        if ($element instanceof Entry) {
            $fields = $element->getFieldValues();

            // Provided value is an entry
            foreach ($fields as $key => $field) {
                try {

                    // If field is a matrix then loop through fields in block
                    if ($field instanceof \craft\elements\db\MatrixBlockQuery) {
                        foreach ($element->getFieldValue($key)->all() as $block) {
                            $blockFields = $block->getFieldValues();

                            foreach ($blockFields as $blockKey => $blockField) {
                                $value = $block->getFieldValue($blockKey);
                                $seconds = $this->_valToSeconds($value);
                                $totalSeconds += $seconds;
                            }
                        }
                    } elseif ($field instanceof \verbb\supertable\elements\db\SuperTableBlockQuery) {
                        foreach ($element->getFieldValue($key)->all() as $block) {
                            $blockFields = $block->getFieldValues();

                            foreach ($blockFields as $blockKey => $blockField) {
                                if ($blockField instanceof Matrix) {
                                    foreach ($block->getFieldValue($blockKey)->all() as $matrix) {
                                        $matrixFields = $matrix->getFieldLayout()->getFields();

                                        foreach ($matrixFields as $matrixField) {
                                            $value = $matrix->getFieldValue($matrixField->handle);
                                            $seconds = $this->_valToSeconds($value);
                                            $totalSeconds += $seconds;
                                        }
                                    }
                                } else {
                                    $value = $block->getFieldValue($blockKey);
                                    $seconds = $this->_valToSeconds($value);
                                    $totalSeconds += $seconds;
                                }
                            }
                        }
                    } elseif ($field instanceof \craft\elements\db\AssetQuery) {
                        //@TODO: integrate YT API connection to read out the duration
                        if(Craft::$app->plugins->isPluginEnabled('embeddedassets')) {
//                            Craft::dd(Plugin::$plugin->methods->getEmbeddedAsset($element->getFieldValue($key)->one()));
                        }
                    } else {
                        $value = $element->getFieldValue($key);
                        $seconds = $this->_valToSeconds($value);
                        $totalSeconds += $seconds;
                    }
                } catch (ErrorException $e) {
                    continue;
                }
            }
        } elseif(is_array($element)) {
            // Provided value is a matrix field
            foreach ($element as $block) {
                if ($block instanceof MatrixBlock) {
                    $blockFields = $block->getFieldValues();

                    foreach ($blockFields as $key => $blockField) {
                        $value = $block->getFieldValue($key);
                        $seconds = $this->_valToSeconds($value);
                        $totalSeconds += $seconds;
                    }
                }
            }
        }

        $data = [
            'seconds' => $totalSeconds,
            'showSeconds' => $showSeconds,
        ];

        return new TimeModel($data);
    }

    /**
     * @param $value
     * @return int
     */
    private function _valToSeconds($value): int
    {
        $settings = Stopwatch::$plugin->getSettings();
        $wpm = $settings->wordsPerMinute;

        $string = StringHelper::toString($value);
        $wordCount = StringHelper::countWords($string);
        return floor($wordCount / $wpm * 60);
    }
}
