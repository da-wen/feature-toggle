<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 01.07.17
 * Time: 18:01
 */

namespace Dawen\FeatureToggle\Tests\Unit;

use Dawen\FeatureToggle\Feature\Feature;
use Dawen\FeatureToggle\Feature\FeatureFactoryInterface;
use Dawen\FeatureToggle\FeatureToggleManager;
use Dawen\FeatureToggle\FeatureToggleManagerInterface;
use Dawen\FeatureToggle\Handler\FeatureToggleHandlerInterface;

class FeatureToggleManagerStrategyAffirmativeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureFactoryInterface */
    private $featureFactory;

    /** @var FeatureToggleManagerInterface */
    private $manager;

    protected function setUp()
    {
        parent::setUp();

        $this->featureFactory = $this->createMock(FeatureFactoryInterface::class);

        $this->manager = new FeatureToggleManager($this->featureFactory);
    }

    protected function tearDown()
    {
        $this->featureFactory = null;
        $this->manager = null;

        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(FeatureToggleManagerInterface::class, $this->manager);
        $this->assertInstanceOf(FeatureToggleManager::class, $this->manager);
    }

    public function testIsEnabledAffirmative()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $feature1 = new Feature('my-feature1', true);
        $feature2 = new Feature('my-feature2', false);

        $handlerName = 'my-handler1';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler */
        $handler = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler->expects($this->exactly(2))->method('getName')->willReturn($handlerName);
        $handler
            ->expects($this->exactly(3))
            ->method('isEnabled')
            ->withConsecutive(
                [$feature1->getName()],
                ['not-existing'],
                [$feature2->getName()]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ENABLED,
                FeatureToggleHandlerInterface::FEATURE_ABSTAIN,
                FeatureToggleHandlerInterface::FEATURE_DISABLED
            );

        $this->manager->addHandler($handler, 20);

        $this->assertTrue($this->manager->isEnabled($feature1->getName()));
        $this->assertFalse($this->manager->isEnabled('not-existing'));
        $this->assertFalse($this->manager->isEnabled($feature2->getName()));
    }

    public function testIsEnabledAffirmativeDisableToggle()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $feature1 = new Feature('my-feature1', true);
        $feature2 = new Feature('my-feature2', false);

        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler */
        $handler = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler->expects($this->never())->method('isEnabled');

        $this->manager->addHandler($handler, 20);
        $this->manager->disable();

        $this->assertFalse($this->manager->isEnabled($feature1->getName()));
        $this->assertFalse($this->manager->isEnabled('not-existing'));
        $this->assertFalse($this->manager->isEnabled($feature2->getName()));
    }

    public function testIsEnabledAffirmativeEnableToggle()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $feature1 = new Feature('my-feature1', true);
        $feature2 = new Feature('my-feature2', false);

        $this->manager = new FeatureToggleManager($this->featureFactory,
            FeatureToggleManagerInterface::STRATEGY_AFFIRMATIVE, false);

        $handlerName1 = 'my-handler1';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler */
        $handler = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler->expects($this->exactly(3))->method('getName')->willReturn($handlerName1);
        $handler->expects($this->never())->method('isEnabled');

        $this->manager->addHandler($handler, 20);
        $this->assertFalse($this->manager->isEnabled($feature1->getName()));
        $this->assertFalse($this->manager->isEnabled('not-existing'));
        $this->assertFalse($this->manager->isEnabled($feature2->getName()));

        $this->manager->removeHandler($handler);
        $this->manager->enable();

        $handler = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);
        $handler->expects($this->exactly(3))
            ->method('isEnabled')
            ->withConsecutive(
                [$feature1->getName()],
                ['not-existing'],
                [$feature2->getName()]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ENABLED,
                FeatureToggleHandlerInterface::FEATURE_ABSTAIN,
                FeatureToggleHandlerInterface::FEATURE_DISABLED
            );
        $this->manager->addHandler($handler, 20);

        $this->assertTrue($this->manager->isEnabled($feature1->getName()));
        $this->assertFalse($this->manager->isEnabled('not-existing'));
        $this->assertFalse($this->manager->isEnabled($feature2->getName()));
    }
}