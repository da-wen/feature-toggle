<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 01.07.17
 * Time: 19:44
 */

namespace Dawen\FeatureToggle\Tests\Unit\Handler;

use Dawen\FeatureToggle\Exception\FeatureToggleHandlerException;
use Dawen\FeatureToggle\Feature\Feature;
use Dawen\FeatureToggle\Handler\ConfigHandler;
use Dawen\FeatureToggle\Handler\FeatureToggleHandlerInterface;

class ConfigHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigHandler
     */
    private $handler;

    protected function setUp()
    {
        parent::setUp();

        $this->handler = new ConfigHandler();
    }

    protected function tearDown()
    {
        $this->handler = null;

        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(FeatureToggleHandlerInterface::class, $this->handler);
        $this->assertInstanceOf(ConfigHandler::class, $this->handler);
    }

    public function testGetName()
    {
        $this->assertSame('feature-toggle.handler.config', $this->handler->getName());
    }

    public function testGetNameCustom()
    {
        $name = 'test-handler';
        $handler = new ConfigHandler(true, $name);

        $this->assertSame($name, $handler->getName());
    }

    public function testIsEnabledWithoutFeatures()
    {
        $this->assertSame(FeatureToggleHandlerInterface::FEATURE_ABSTAIN, $this->handler->isEnabled('no-registered'));
    }

    public function testAddFeatureException()
    {
        $feature = new Feature('my-feature', true);

        $this->handler->addFeature($feature);

        try {
            $this->handler->addFeature($feature);
        } catch (FeatureToggleHandlerException $exception) {
            $this->assertSame('Feature ' . $feature->getName() . ' already registered', $exception->getMessage());

            return;
        }

        $this->fail('exception not thrown');
    }

    public function testGetFeatures()
    {
        $feature1 = new Feature('my-feature1', true);
        $feature2 = new Feature('my-feature2', false);

        $this->handler->addFeature($feature1);
        $this->handler->addFeature($feature2);

        $this->assertCount(2, $this->handler->getFeatures());
        $this->assertContains($feature1, $this->handler->getFeatures());
        $this->assertContains($feature2, $this->handler->getFeatures());
    }

    public function testIsEnabled()
    {
        $feature1 = new Feature('my-feature1', true);
        $feature2 = new Feature('my-feature2', false);

        $this->handler->addFeature($feature1);
        $this->handler->addFeature($feature2);

        $this->assertSame(FeatureToggleHandlerInterface::FEATURE_ABSTAIN, $this->handler->isEnabled('no-registered'));
        $this->assertSame(
            FeatureToggleHandlerInterface::FEATURE_ENABLED,
            $this->handler->isEnabled($feature1->getName())
        );
        $this->assertSame(
            FeatureToggleHandlerInterface::FEATURE_DISABLED,
            $this->handler->isEnabled($feature2->getName())
        );
    }

    public function testDisableEnableToggles()
    {
        $feature1 = new Feature('my-feature1', true);
        $feature2 = new Feature('my-feature2', false);

        $this->handler->addFeature($feature1);
        $this->handler->addFeature($feature2);

        $this->assertSame(FeatureToggleHandlerInterface::FEATURE_ABSTAIN, $this->handler->isEnabled('no-registered'));
        $this->assertSame(
            FeatureToggleHandlerInterface::FEATURE_ENABLED,
            $this->handler->isEnabled($feature1->getName())
        );
        $this->assertSame(
            FeatureToggleHandlerInterface::FEATURE_DISABLED,
            $this->handler->isEnabled($feature2->getName())
        );

        $this->handler->disable();

        $this->assertSame(FeatureToggleHandlerInterface::FEATURE_ABSTAIN, $this->handler->isEnabled('no-registered'));
        $this->assertSame(
            FeatureToggleHandlerInterface::FEATURE_ABSTAIN,
            $this->handler->isEnabled($feature1->getName())
        );
        $this->assertSame(
            FeatureToggleHandlerInterface::FEATURE_ABSTAIN,
            $this->handler->isEnabled($feature2->getName())
        );

        $this->handler->enable();

        $this->assertSame(FeatureToggleHandlerInterface::FEATURE_ABSTAIN, $this->handler->isEnabled('no-registered'));
        $this->assertSame(
            FeatureToggleHandlerInterface::FEATURE_ENABLED,
            $this->handler->isEnabled($feature1->getName())
        );
        $this->assertSame(
            FeatureToggleHandlerInterface::FEATURE_DISABLED,
            $this->handler->isEnabled($feature2->getName())
        );
    }

}