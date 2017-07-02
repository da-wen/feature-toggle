<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 02.07.17
 * Time: 14:06
 */

namespace Dawen\FeatureToggle\Tests\Unit\Handler;

use Dawen\FeatureToggle\Exception\FeatureToggleHandlerException;
use Dawen\FeatureToggle\Feature\Feature;
use Dawen\FeatureToggle\Feature\FeatureFactory;
use Dawen\FeatureToggle\Feature\FeatureFactoryInterface;
use Dawen\FeatureToggle\Feature\FeatureInterface;
use Dawen\FeatureToggle\Handler\ConfigHandler;
use Dawen\FeatureToggle\Handler\Factory\ConfigHandlerFactory;
use Dawen\FeatureToggle\Handler\FeatureToggleHandlerInterface;

class ConfigHandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigHandlerFactory
     */
    private $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FeatureFactoryInterface
     */
    private $featureFactoryMock;

    protected function setUp()
    {
        parent::setUp();

        $this->featureFactoryMock = $this->createMock(FeatureFactoryInterface::class);

        $this->factory = new ConfigHandlerFactory($this->featureFactoryMock);
    }

    protected function tearDown()
    {
        $this->featureFactoryMock = null;
        $this->factory = null;

        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(ConfigHandlerFactory::class, $this->factory);
    }

    public function testCreateWithNameSetToNull()
    {
        $this->featureFactoryMock->expects($this->never())->method('create');

        $result = $this->factory->create([], true);
        $this->assertInstanceOf(ConfigHandler::class, $result);
        $this->assertSame('feature-toggle.handler.config', $result->getName());
    }

    public function testCreateWithName()
    {
        $name = 'my-handler-name';
        $this->featureFactoryMock->expects($this->never())->method('create');

        $result = $this->factory->create([], true, $name);
        $this->assertInstanceOf(ConfigHandler::class, $result);
        $this->assertSame($name, $result->getName());
    }

    public function testCreateWithEmptyFeatures()
    {
        $this->featureFactoryMock->expects($this->never())->method('create');

        $result = $this->factory->create([], true);
        $this->assertInstanceOf(ConfigHandler::class, $result);
        $this->assertTrue(is_array($result->getFeatures()));
        $this->assertEmpty($result->getFeatures());
    }

    public function testCreateWithFeatures()
    {
        $features = $this->createFeatures();
        $featureMock = $this->createMock(FeatureInterface::class);
        $featureMock->expects($this->exactly(3))
            ->method('getName')
            ->willReturnOnConsecutiveCalls($features[0]['name'], $features[1]['name'], $features[2]['name']);
        $this->featureFactoryMock->expects($this->exactly(3))
            ->method('create')
            ->willReturn($featureMock);

        $result = $this->factory->create($features, true);
        $this->assertInstanceOf(ConfigHandler::class, $result);
        $this->assertTrue(is_array($result->getFeatures()));
        $this->assertCount(3, $result->getFeatures());
    }

    public function testCreateWithMultipleFeatures()
    {
        $featureFactory = new FeatureFactory();

        $features = $this->createFeatures();
        $this->featureFactoryMock->expects($this->exactly(3))
            ->method('create')
            ->withConsecutive([$features[0]], [$features[1]], [$features[2]])
            ->willReturnOnConsecutiveCalls(
                $featureFactory->create($features[0]),
                $featureFactory->create($features[1]),
                $featureFactory->create($features[2])
            );

        $result = $this->factory->create($features, true);
        $this->assertInstanceOf(ConfigHandler::class, $result);
        $this->assertTrue(is_array($result->getFeatures()));
        $this->assertCount(3, $result->getFeatures());
        $this->assertSame(1, $result->isEnabled($features[0]['name']));
        $this->assertSame(-1, $result->isEnabled($features[1]['name']));
    }

    public function testCreateWithDisabled()
    {
        $featureFactory = new FeatureFactory();

        $features = $this->createFeatures();
        $this->featureFactoryMock->expects($this->exactly(3))
            ->method('create')
            ->withConsecutive([$features[0]], [$features[1]], [$features[2]])
            ->willReturnOnConsecutiveCalls(
                $featureFactory->create($features[0]),
                $featureFactory->create($features[1]),
                $featureFactory->create($features[2])
            );

        $result = $this->factory->create($features, false);
        $this->assertInstanceOf(ConfigHandler::class, $result);
        $this->assertTrue(is_array($result->getFeatures()));
        $this->assertCount(3, $result->getFeatures());
        $this->assertSame(0, $result->isEnabled($features[0]['name']));
        $this->assertSame(0, $result->isEnabled($features[1]['name']));
    }


    private function createFeatures()
    {
        return [
            [
                'name' => 'MY-FEATURE-1',
                'enabled' => true,
                'description' => 'blah blah 1',
                'options' => ['sample' => 1]
            ],
            [
                'name' => 'MY-FEATURE-2',
                'enabled' => false,
                'description' => 'blah blah 2',
                'options' => ['sample' => 1]
            ],
            [
                'name' => 'MY-FEATURE-3',
                'enabled' => true,
                'description' => 'blah blah 3',
                'options' => ['sample' => 3]
            ],
        ];
    }

}