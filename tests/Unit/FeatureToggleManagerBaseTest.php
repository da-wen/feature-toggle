<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 01.07.17
 * Time: 17:10
 */

namespace Dawen\FeatureToggle\Tests\Unit;

use Dawen\FeatureToggle\Exception\FeatureToggleManagerException;
use Dawen\FeatureToggle\Feature\Feature;
use Dawen\FeatureToggle\Feature\FeatureFactoryInterface;
use Dawen\FeatureToggle\FeatureToggleManager;
use Dawen\FeatureToggle\FeatureToggleManagerInterface;
use Dawen\FeatureToggle\Handler\FeatureToggleHandlerInterface;

class FeatureToggleManagerBaseTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The strategy "notValidStrategy" is not supported.
     */
    public function testConstructorException()
    {
        $this->featureFactory->expects($this->never())->method('create');

        new FeatureToggleManager($this->featureFactory, 'notValidStrategy');
    }

    public function testAddHandler()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $handlerName = 'my-handler';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler */
        $handler = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler->expects($this->exactly(2))->method('getName')->willReturn($handlerName);

        $this->manager->addHandler($handler);

        $registeredHandlers = $this->manager->getHandlers();
        $this->assertCount(1, $registeredHandlers);
        $this->assertSame($handler, $registeredHandlers[$handlerName]);
    }

    public function testAddHandlerException()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $handlerName = 'my-handler';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler */
        $handler = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler->expects($this->exactly(4))->method('getName')->willReturn($handlerName);

        $this->manager->addHandler($handler);

        try {
            $this->manager->addHandler($handler);
        } catch (FeatureToggleManagerException $exception) {
            $this->assertContains('Handler ' . $handlerName . ' already registered', $exception->getMessage());
            return;
        }

        $this->fail('exception was not thrown');
    }

    public function testAddHandlerPriority()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $handlerName1 = 'my-handler1';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler1 */
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);
        $this->manager->addHandler($handler1, 20);

        $handlerName2 = 'my-handler2';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler2 */
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler2->expects($this->exactly(2))->method('getName')->willReturn($handlerName2);
        $this->manager->addHandler($handler2, 20);

        $handlerName3 = 'my-handler3';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler3 */
        $handler3 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler3->expects($this->exactly(2))->method('getName')->willReturn($handlerName3);
        $this->manager->addHandler($handler3);

        $handlerName4 = 'my-handler4';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler4 */
        $handler4 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler4->expects($this->exactly(2))->method('getName')->willReturn($handlerName4);
        $this->manager->addHandler($handler4, 99);

        $registeredHandlers = $this->manager->getHandlers();

        $this->assertCount(4, $registeredHandlers);
        $this->assertSame([$handlerName3, $handlerName1, $handlerName2, $handlerName4],
            array_keys($registeredHandlers));
    }

    public function testRemoveHandler()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $handlerName1 = 'my-handler1';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler1 */
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);
        $this->manager->addHandler($handler1, 20);

        $handlerName2 = 'my-handler2';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler2 */
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler2->expects($this->exactly(3))->method('getName')->willReturn($handlerName2);
        $this->manager->addHandler($handler2, 20);

        $handlerName3 = 'my-handler3';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler3 */
        $handler3 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler3->expects($this->exactly(3))->method('getName')->willReturn($handlerName3);
        $this->manager->addHandler($handler3);

        $handlerName4 = 'my-handler4';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler4 */
        $handler4 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler4->expects($this->exactly(2))->method('getName')->willReturn($handlerName4);
        $this->manager->addHandler($handler4, 99);

        $registeredHandlers = $this->manager->getHandlers();

        $this->assertCount(4, $registeredHandlers);
        $this->assertSame([$handlerName3, $handlerName1, $handlerName2, $handlerName4],
            array_keys($registeredHandlers));

        $this->assertTrue($this->manager->removeHandler($handler3));
        $this->assertTrue($this->manager->removeHandler($handler2));

        $registeredHandlers = $this->manager->getHandlers();
        $this->assertCount(2, $registeredHandlers);
        $this->assertSame([$handlerName1, $handlerName4], array_keys($registeredHandlers));
    }

    public function testRemoveHandlerNotExisting()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $handlerName1 = 'my-handler1';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler1 */
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);
        $this->manager->addHandler($handler1, 20);

        $handlerName2 = 'my-handler2';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler2 */
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler2->expects($this->exactly(2))->method('getName')->willReturn($handlerName2);
        $this->manager->addHandler($handler2, 20);

        $handlerName3 = 'my-handler3';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler3 */
        $handler3 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler3->expects($this->exactly(2))->method('getName')->willReturn($handlerName3);
        $this->manager->addHandler($handler3);

        $handlerName4 = 'my-handler4';
        $handler4 = $this->createMock(FeatureToggleHandlerInterface::class);
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler4 */
        $handler4->expects($this->exactly(2))->method('getName')->willReturn($handlerName4);
        $this->manager->addHandler($handler4, 99);

        $registeredHandlers = $this->manager->getHandlers();

        $this->assertCount(4, $registeredHandlers);
        $this->assertSame([$handlerName3, $handlerName1, $handlerName2, $handlerName4],
            array_keys($registeredHandlers));

        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $notExistingHandler */
        $notExistingHandler = $this->createMock(FeatureToggleHandlerInterface::class);
        $this->assertFalse($this->manager->removeHandler($notExistingHandler));

        $registeredHandlers = $this->manager->getHandlers();
        $this->assertCount(4, $registeredHandlers);
        $this->assertSame([$handlerName3, $handlerName1, $handlerName2, $handlerName4],
            array_keys($registeredHandlers));
    }

    public function testGetHandler()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $handlerName1 = 'my-handler';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler1 */
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);

        $handlerName2 = 'my-handler2';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler2 */
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler2->expects($this->exactly(2))->method('getName')->willReturn($handlerName2);


        $this->manager->addHandler($handler1);
        $this->manager->addHandler($handler2);

        $this->assertSame($handler1, $this->manager->getHandler($handlerName1));
        $this->assertSame($handler2, $this->manager->getHandler($handlerName2));
    }

    public function testGetHandlerException()
    {
        $this->featureFactory->expects($this->never())->method('create');

        $handlerName1 = 'my-handler';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler1 */
        $handler1 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler1->expects($this->exactly(2))->method('getName')->willReturn($handlerName1);

        $handlerName2 = 'my-handler2';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler2 */
        $handler2 = $this->createMock(FeatureToggleHandlerInterface::class);
        $handler2->expects($this->exactly(2))->method('getName')->willReturn($handlerName2);


        $this->manager->addHandler($handler1);
        $this->manager->addHandler($handler2);

        try {
            $this->manager->getHandler('not-existing');
        } catch (FeatureToggleManagerException $exception) {
            $this->assertContains('Handler is not set', $exception->getMessage());
            return;
        }

        $this->fail('exception not thrown');
    }

    public function testGetFeatures()
    {
        $featureName1 = 'my-feature-1';

        $handlerName = 'my-handler1';
        /** @var \PHPUnit_Framework_MockObject_MockObject|FeatureToggleHandlerInterface $handler */
        $handler = $this->createMock(FeatureToggleHandlerInterface::class);

        $handler->expects($this->exactly(2))->method('getName')->willReturn($handlerName);
        $handler->expects($this->exactly(1))->method('getFeatures')->willReturn([$featureName1 => new Feature($featureName1)]);
        $handler
            ->expects($this->exactly(1))
            ->method('isEnabled')
            ->withConsecutive(
                [$featureName1]
            )
            ->willReturnOnConsecutiveCalls(
                FeatureToggleHandlerInterface::FEATURE_DISABLED
            );

        $this->featureFactory
            ->expects($this->exactly(1))
            ->method('create')
            ->with(
                ['name' => $featureName1, 'enabled' => false, 'description' => null]
            )
            ->willReturnOnConsecutiveCalls(
                new Feature($featureName1, false)
            );

        $this->manager->addHandler($handler, 20);

        $features = $this->manager->getFeatures();
        $this->assertCount(1, $features);
        $this->assertEquals(new Feature($featureName1, false), $features[$featureName1]);
    }
}