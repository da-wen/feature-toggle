<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 01.07.17
 * Time: 18:58
 */

namespace Dawen\FeatureToggle\Tests\Unit;

use Dawen\FeatureToggle\Feature\FeatureFactoryInterface;
use Dawen\FeatureToggle\FeatureToggleManager;
use Dawen\FeatureToggle\FeatureToggleManagerInterface;
use Dawen\FeatureToggle\Handler\FeatureToggleHandlerInterface;

class FeatureToggleManagerStrategyUnanimousTest extends \PHPUnit_Framework_TestCase
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

    public function testIsEnabledUnanimousExpectingTrueWithAllEnabled()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $this->manager = new FeatureToggleManager($this->featureFactory,
            FeatureToggleManagerInterface::STRATEGY_UNANIMOUS);

        $featureName1 = 'my-feature-1';

        $handlerName1 = 'my-handler1';
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName2 = 'my-handler2';
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName3 = 'my-handler3';
        $handler3 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);
        $handler1
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ENABLED
            );

        $handler2->expects($this->exactly(2))->method('getName')->willReturn($handlerName2);
        $handler2
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ENABLED
            );

        $handler3->expects($this->exactly(2))->method('getName')->willReturn($handlerName3);
        $handler3
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ENABLED
            );

        $this->manager->addHandler($handler1, 20);
        $this->manager->addHandler($handler2, 30);
        $this->manager->addHandler($handler3, 40);

        $this->assertTrue($this->manager->isEnabled($featureName1));
    }

    public function testIsEnabledUnanimousExpectingTrueWithOneAbstain()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $this->manager = new FeatureToggleManager($this->featureFactory,
            FeatureToggleManagerInterface::STRATEGY_UNANIMOUS);

        $featureName1 = 'my-feature-1';

        $handlerName1 = 'my-handler1';
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName2 = 'my-handler2';
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName3 = 'my-handler3';
        $handler3 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);
        $handler1
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ENABLED
            );

        $handler2->expects($this->exactly(2))->method('getName')->willReturn($handlerName2);
        $handler2
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ABSTAIN
            );

        $handler3->expects($this->exactly(2))->method('getName')->willReturn($handlerName3);
        $handler3
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ENABLED
            );

        $this->manager->addHandler($handler1, 20);
        $this->manager->addHandler($handler2, 30);
        $this->manager->addHandler($handler3, 40);

        $this->assertTrue($this->manager->isEnabled($featureName1));
    }

    public function testIsEnabledUnanimousExpectingFalseWithOneDisabled()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $this->manager = new FeatureToggleManager($this->featureFactory,
            FeatureToggleManagerInterface::STRATEGY_UNANIMOUS);

        $featureName1 = 'my-feature-1';

        $handlerName1 = 'my-handler1';
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName2 = 'my-handler2';
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName3 = 'my-handler3';
        $handler3 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);
        $handler1
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ENABLED
            );

        $handler2->expects($this->exactly(2))->method('getName')->willReturn($handlerName2);
        $handler2
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_DISABLED
            );

        $handler3->expects($this->exactly(2))->method('getName')->willReturn($handlerName3);
        $handler3->expects($this->never())->method('isEnabled');

        $this->manager->addHandler($handler1, 20);
        $this->manager->addHandler($handler2, 30);
        $this->manager->addHandler($handler3, 40);

        $this->assertFalse($this->manager->isEnabled($featureName1));
    }

    public function testIsEnabledUnanimousExpectingFalseWithOneDisabledAndOneAbstain()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $this->manager = new FeatureToggleManager($this->featureFactory,
            FeatureToggleManagerInterface::STRATEGY_UNANIMOUS);

        $featureName1 = 'my-feature-1';

        $handlerName1 = 'my-handler1';
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName2 = 'my-handler2';
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName3 = 'my-handler3';
        $handler3 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);
        $handler1
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ABSTAIN
            );

        $handler2->expects($this->exactly(2))->method('getName')->willReturn($handlerName2);
        $handler2
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_DISABLED
            );

        $handler3->expects($this->exactly(2))->method('getName')->willReturn($handlerName3);
        $handler3->expects($this->never())->method('isEnabled');

        $this->manager->addHandler($handler1, 20);
        $this->manager->addHandler($handler2, 30);
        $this->manager->addHandler($handler3, 40);

        $this->assertFalse($this->manager->isEnabled($featureName1));
    }

    public function testIsEnabledUnanimousExpectingFalseWithAllAbstain()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $this->manager = new FeatureToggleManager($this->featureFactory,
            FeatureToggleManagerInterface::STRATEGY_UNANIMOUS);

        $featureName1 = 'my-feature-1';

        $handlerName1 = 'my-handler1';
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName2 = 'my-handler2';
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handlerName3 = 'my-handler3';
        $handler3 = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);
        $handler1
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ABSTAIN
            );

        $handler2->expects($this->exactly(2))->method('getName')->willReturn($handlerName2);
        $handler2
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ABSTAIN
            );

        $handler3->expects($this->exactly(2))->method('getName')->willReturn($handlerName3);
        $handler3
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_ABSTAIN
            );

        $this->manager->addHandler($handler1, 20);
        $this->manager->addHandler($handler2, 30);
        $this->manager->addHandler($handler3, 40);

        $this->assertFalse($this->manager->isEnabled($featureName1));
    }
}