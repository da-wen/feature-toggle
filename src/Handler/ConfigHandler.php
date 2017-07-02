<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 30.06.17
 * Time: 21:12
 */

namespace Dawen\FeatureToggle\Handler;

use Dawen\FeatureToggle\Exception\FeatureToggleHandlerException;
use Dawen\FeatureToggle\Feature\FeatureInterface;

/**
 * Class ConfigHandler
 *
 * @package Dawen\FeatureToggle\Handler
 */
class ConfigHandler implements FeatureToggleHandlerInterface
{
    /**
     * @var string
     */
    private $name = 'feature-toggle.handler.config';

    /**
     * @var bool
     */
    private $handlerEnabled = true;

    /**
     * @var array[FeatureName] = Feature
     */
    private $features = [];

    /**
     * @param bool        $handlerEnabled
     * @param null|string $name
     */
    public function __construct($handlerEnabled = true, $name = null)
    {
        $this->handlerEnabled = $handlerEnabled;
        if (null !== $name && !empty($name)) {
            $this->name = $name;
        }
    }

    /**
     * Adds a feature to handler
     *
     * @param FeatureInterface $feature
     * @throws FeatureToggleHandlerException
     * @author dawen
     */
    public function addFeature(FeatureInterface $feature)
    {
        $name = $feature->getName();
        if (isset($this->features[$name])) {
            throw new FeatureToggleHandlerException('Feature ' . $name . ' already registered');
        }

        $this->features[$name] = $feature;
    }

    /**
     * @inheritdoc
     */
    public function enable()
    {
        $this->handlerEnabled = true;
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        $this->handlerEnabled = false;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled($featureName)
    {
        if (false === $this->handlerEnabled || !isset($this->features[$featureName])) {
            return self::FEATURE_ABSTAIN;
        }

        /** @var FeatureInterface $feature */
        $feature = $this->features[$featureName];


        return (true === $feature->isEnabled()) ? self::FEATURE_ENABLED : self::FEATURE_DISABLED;
    }

    /**
     * @inheritdoc
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }
}