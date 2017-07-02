<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 01.07.17
 * Time: 11:17
 */

namespace Dawen\FeatureToggle\Handler\Factory;

use Dawen\FeatureToggle\Feature\FeatureFactoryInterface;
use Dawen\FeatureToggle\Handler\ConfigHandler;

/**
 * Class ConfigHandlerFactory
 *
 * @package Dawen\FeatureToggle\Handler\Factory
 */
class ConfigHandlerFactory
{
    /**
     * @var FeatureFactoryInterface
     */
    private $featureFactory;

    /**
     * ConfigHandlerFactory constructor.
     *
     * @param FeatureFactoryInterface $featureFactory
     */
    public function __construct(FeatureFactoryInterface $featureFactory)
    {
        $this->featureFactory = $featureFactory;
    }

    /**
     * Created a new instance of ConfigHandler with instantiated features
     *
     * @param array       $features
     * @param bool        $handlerEnabled
     * @param null|string $name
     * @return ConfigHandler
     * @author dawen
     */
    public function create(array $features, $handlerEnabled = true, $name = null)
    {
        $handler = new ConfigHandler($handlerEnabled, $name);

        foreach ($features as $feature) {
            $handler->addFeature($this->featureFactory->create($feature));
        }

        return $handler;
    }
}