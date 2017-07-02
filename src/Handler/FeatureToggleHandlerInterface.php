<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 30.06.17
 * Time: 21:07
 */

namespace Dawen\FeatureToggle\Handler;

/**
 * Interface FeatureToggleHandlerInterface
 *
 * @package Dawen\FeatureToggle\Handler
 */
interface FeatureToggleHandlerInterface
{

    const FEATURE_ENABLED = 1;
    const FEATURE_ABSTAIN = 0;
    const FEATURE_DISABLED = -1;

    /**
     * Enables the handler
     *
     * @author dawen
     */
    public function enable();

    /**
     * Disables the handler
     *
     * @author dawen
     */
    public function disable();

    /**
     * Checks if a feature is enabled.
     *
     * @param string $featureName
     * @return int
     * @author dawen
     */
    public function isEnabled($featureName);

    /**
     * Gets name of the handler
     *
     * @return string
     * @author dawen
     */
    public function getName();

    /**
     * Get all registered features
     *
     * @return array[Feature]
     * @author dawen
     */
    public function getFeatures();

}