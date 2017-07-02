<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 30.06.17
 * Time: 22:33
 */

namespace Dawen\FeatureToggle\Feature;

/**
 * Interface FeatureInterface
 *
 * @package Dawen\FeatureToggle\Feature
 */
interface FeatureInterface
{

    /**
     * Gets the name of the feature
     *
     * @return string
     * @author dawen
     */
    public function getName();

    /**
     * Sets the name of the feature
     *
     * @param string $name
     * @author dawen
     */
    public function setName($name);

    /**
     * Gets the status of a feature
     *
     * @return bool
     * @author dawen
     */
    public function isEnabled();

    /**
     * Sets enabled as bool
     *
     * @param bool $enabled
     * @author dawen
     */
    public function setEnabled($enabled);

    /**
     * Gets the description of a feature
     *
     * @return null|string
     * @author dawen
     */
    public function getDescription();

    /**
     * Sets description
     *
     * @param string $description
     * @author dawen
     */
    public function setDescription($description);

    /**
     * Checks if options are not empty
     *
     * @return bool
     * @author dawen
     */
    public function hasOptions();

    /**
     * Get options
     *
     * @return array
     * @author dawen
     */
    public function getOptions();

    /**
     * Sets the options
     *
     * @param array $options
     * @author dawen
     */
    public function setOptions(array $options);

    /**
     * Transforms object into array
     *
     * @return array
     * @author dawen
     */
    public function toArray();
}