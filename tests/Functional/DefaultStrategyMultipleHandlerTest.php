<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 02.07.17
 * Time: 10:44
 */

namespace Dawen\FeatureToggle\Tests\Functional;

use Dawen\FeatureToggle\Feature\FeatureFactory;
use Dawen\FeatureToggle\FeatureToggleManager;
use Dawen\FeatureToggle\Handler\Factory\ConfigHandlerFactory;

class DefaultStrategyMultipleHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FeatureToggleManager */
    private $manager;

    protected function setUp()
    {
        parent::setUp();

        $featureFactory = new FeatureFactory();
        $configHandlerFactory = new ConfigHandlerFactory($featureFactory);
        $this->manager = new FeatureToggleManager($featureFactory);
        $this->manager->addHandler($configHandlerFactory->create($this->getFeatures1(), false));
        $this->manager->addHandler($configHandlerFactory->create($this->getFeatures2(), true, 'second'));
        $this->manager->addHandler($configHandlerFactory->create($this->getFeatures1(), true, 'third'));
    }

    protected function tearDown()
    {
        $this->manager = null;

        parent::tearDown();
    }

    public function testAllFeatures()
    {
        $this->assertFalse($this->manager->isEnabled('MY-FEATURE-1'));
        $this->assertTrue($this->manager->isEnabled('MY-FEATURE-2'));
        $this->assertFalse($this->manager->isEnabled('MY-FEATURE-3'));
    }


    private function getFeatures1()
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

    private function getFeatures2()
    {
        return [
            [
                'name' => 'MY-FEATURE-1',
                'enabled' => false,
                'description' => 'blah blah 1',
                'options' => ['sample' => 1]
            ],
            [
                'name' => 'MY-FEATURE-2',
                'enabled' => true,
                'description' => 'blah blah 2',
                'options' => ['sample' => 1]
            ],
            [
                'name' => 'MY-FEATURE-3',
                'enabled' => false,
                'description' => 'blah blah 3',
                'options' => ['sample' => 3]
            ],
        ];
    }
}