<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 30.06.17
 * Time: 19:47
 */

namespace Dawen\FeatureToggle;

use Dawen\FeatureToggle\Exception\FeatureToggleManagerException;
use Dawen\FeatureToggle\Feature\FeatureFactoryInterface;
use Dawen\FeatureToggle\Feature\FeatureInterface;
use Dawen\FeatureToggle\Handler\FeatureToggleHandlerInterface;

/**
 * Class FeatureToggleManager
 *
 * @package Dawen\FeatureToggle
 */
class FeatureToggleManager implements FeatureToggleManagerInterface
{
    /**
     * @var FeatureFactoryInterface
     */
    private $featureFactory;

    /**
     * @var string
     */
    private $strategy;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * Info: [priority][handlerName]
     *
     * @var array
     */
    private $handlers = [];

    /**
     * @param FeatureFactoryInterface $featureFactory
     * @param string                  $strategy
     * @param bool                    $enabled
     */
    public function __construct(
        FeatureFactoryInterface $featureFactory,
        $strategy = self::STRATEGY_AFFIRMATIVE,
        $enabled = true
    ) {
        $strategyMethod = 'isEnabled' . ucfirst($strategy);
        if (!is_callable([$this, $strategyMethod])) {
            throw new \InvalidArgumentException(sprintf('The strategy "%s" is not supported.', $strategy));
        }

        $this->featureFactory = $featureFactory;
        $this->strategy = $strategyMethod;
        $this->enabled = (bool)$enabled;
    }

    /**
     * @inheritdoc
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * @inheritdoc
     */
    public function addHandler(FeatureToggleHandlerInterface $handler, $priority = 0)
    {
        $registeredHandlers = $this->getHandlers();

        if (isset($registeredHandlers[$handler->getName()])) {
            throw new FeatureToggleManagerException('Handler ' . $handler->getName() . ' already registered');
        }

        $this->handlers[(int)$priority][$handler->getName()] = $handler;
        ksort($this->handlers);
    }

    /**
     * @inheritdoc
     */
    public function getFeatures()
    {
        $features = [];

        /** @var FeatureToggleHandlerInterface $handler */
        foreach ($this->getHandlers() as $handler) {
            /** @var FeatureInterface $feature */
            foreach ($handler->getFeatures() as $feature) {
                $featureName = $feature->getName();
                if (!isset($features[$featureName])) {
                    $clonedFeature = clone $feature;
                    $clonedFeature->setEnabled($this->isEnabled($featureName));

                    $features[$featureName] = $this->featureFactory->create($clonedFeature->toArray());
                }
            }
        }

        return $features;
    }

    /**
     * @inheritdoc
     */
    public function getHandlers()
    {
        $handlers = [];

        foreach ($this->handlers as $priority => $registeredHandlers) {
            foreach ($registeredHandlers as $handlerName => $handler) {
                $handlers[$handlerName] = $handler;
            }
        }

        return $handlers;
    }

    /**
     * @inheritdoc
     */
    public function getHandler($handlerName)
    {
        $handlers = $this->getHandlers();

        if (!isset($handlers[$handlerName])) {
            throw new FeatureToggleManagerException('Handler is not set');
        }

        return $handlers[$handlerName];
    }

    /**
     * @inheritdoc
     */
    public function isEnabled($featureName)
    {
        if (false === $this->enabled) {
            return false;
        }

        return $this->{$this->strategy}($featureName);
    }

    /**
     * @inheritdoc
     */
    public function removeHandler(FeatureToggleHandlerInterface $handler)
    {
        $name = $handler->getName();

        foreach ($this->handlers as $priority => $registeredHandlers) {
            foreach ($registeredHandlers as $handlerName => $handler) {
                if ($name == $handlerName) {
                    unset($this->handlers[$priority][$handlerName]);

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Converts value into bool
     *
     * @param int $value
     * @return bool
     * @author dawen
     */
    private function convertToBool($value)
    {
        switch ($value) {
            case FeatureToggleHandlerInterface::FEATURE_ENABLED:
                return true;
                break;
        }

        return false;
    }

    /**
     * Checks if feature is enabled by first found registered feature. Not registered features are treated as disabled
     *
     * @param string $featureName
     * @return bool
     * @author dawen
     */
    private function isEnabledAffirmative($featureName)
    {
        foreach ($this->handlers as $priority => $registeredHandlers) {
            /**
             * @var FeatureToggleHandlerInterface $handler
             */
            foreach ($registeredHandlers as $handlerName => $handler) {
                $isEnabledValue = $handler->isEnabled($featureName);

                if (FeatureToggleHandlerInterface::FEATURE_ABSTAIN != $isEnabledValue) {
                    return $this->convertToBool($isEnabledValue);
                }
            }
        }

        return false;
    }

    /**
     * Checks if feature is enabled treated by majority. Not registered features are treated as disabled
     *
     * @param string $featureName
     * @return bool
     * @author dawen
     */
    private function isEnabledConsensus($featureName)
    {
        $enabled = 0;
        foreach ($this->handlers as $priority => $registeredHandlers) {
            /**
             * @var FeatureToggleHandlerInterface $handler
             */
            foreach ($registeredHandlers as $handlerName => $handler) {
                $enabled += $handler->isEnabled($featureName);
            }
        }

        return $enabled > 0 ? true : false;
    }

    /**
     * Checks if feature is enabled. First disabled will force returning false. Not registered features are treated as
     * disabled
     *
     * @param string $featureName
     * @return bool
     * @author dawen
     */
    private function isEnabledUnanimous($featureName)
    {
        $enabled = 0;
        foreach ($this->handlers as $priority => $registeredHandlers) {
            /**
             * @var FeatureToggleHandlerInterface $handler
             */
            foreach ($registeredHandlers as $handlerName => $handler) {
                $featureIsEnabledValue = $handler->isEnabled($featureName);

                switch ($featureIsEnabledValue) {
                    case FeatureToggleHandlerInterface::FEATURE_ENABLED:
                        ++$enabled;
                        break;

                    case FeatureToggleHandlerInterface::FEATURE_DISABLED:
                        return false;

                    default:
                        break;
                }

            }
        }

        return $enabled > 0 ? true : false;
    }

}