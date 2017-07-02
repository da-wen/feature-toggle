<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 30.06.17
 * Time: 22:45
 */

namespace Dawen\FeatureToggle\Feature;

/**
 * Interface FeatureFactoryInterface
 *
 * @package Dawen\FeatureToggle\Feature
 */
interface FeatureFactoryInterface
{
    /**
     * Creates a new instance of FeatureInterface based class
     *
     * @param array $properties
     * @return FeatureInterface
     * @author dawen
     */
    public function create(array $properties);
}
