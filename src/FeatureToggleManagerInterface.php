<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 30.06.17
 * Time: 21:05
 */

namespace Dawen\FeatureToggle;

use Dawen\FeatureToggle\Exception\FeatureToggleManagerException;
use Dawen\FeatureToggle\Handler\FeatureToggleHandlerInterface;

/**
 * Interface FeatureToggleManagerInterface
 *
 * @package Dawen\FeatureToggle
 */
interface FeatureToggleManagerInterface
{
    /* DEFAULT: first found handler will return his status */
    const STRATEGY_AFFIRMATIVE = 'affirmative';
    /* when majority of positive is given */
    const STRATEGY_CONSENSUS = 'consensus';
    /* when no handler has a negative value */
    const STRATEGY_UNANIMOUS = 'unanimous';

    /**
     * Adds a handler.
     *
     * @param FeatureToggleHandlerInterface $handler
     * @param int                           $priority
     * @throws FeatureToggleManagerException
     * @author dawen
     */
    public function addHandler(FeatureToggleHandlerInterface $handler, $priority = 0);

    /**
     * Enables the logic of the manager
     *
     * @author dawen
     */
    public function enable();

    /**
     * Disables the manager and isEnabled method will return false
     *
     * @author dawen
     */
    public function disable();

    /**
     * Aggregates all set features of each handler and merge them into one array
     *
     * @return array
     * @author dawen
     */
    public function getFeatures();

    /**
     * Returns handlers in prioritized order without priority value
     *
     * @return array
     * @author dawen
     */
    public function getHandlers();

    /**
     * Gets a handler by name.
     *
     * @param string $handlerName
     * @return FeatureToggleHandlerInterface
     * @throws FeatureToggleManagerException
     * @author dawen
     */
    public function getHandler($handlerName);

    /**
     * Checks if a feature is enabled
     *
     * @param string $featureName
     * @return bool
     * @author dawen
     */
    public function isEnabled($featureName);

    /**
     * Removes a handler
     *
     * @param FeatureToggleHandlerInterface $handler
     * @return bool
     * @author dawen
     */
    public function removeHandler(FeatureToggleHandlerInterface $handler);

}