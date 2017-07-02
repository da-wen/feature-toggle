<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 01.07.17
 * Time: 09:51
 */

namespace Dawen\FeatureToggle\Feature;

/**
 * Class FeatureFactory
 *
 * @package Dawen\FeatureToggle\Feature
 */
class FeatureFactory implements FeatureFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function create(array $properties)
    {
        $feature = new Feature($properties['name']);
        $feature->fromArray($properties);

        return $feature;
    }
}