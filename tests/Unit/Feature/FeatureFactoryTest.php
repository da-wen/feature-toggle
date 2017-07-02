<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 01.07.17
 * Time: 20:13
 */

namespace Dawen\FeatureToggle\Tests\Unit\Feature;

use Dawen\FeatureToggle\Feature\FeatureFactory;
use Dawen\FeatureToggle\Feature\FeatureFactoryInterface;

class FeatureFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $featureFactory = new FeatureFactory();

        $this->assertInstanceOf(FeatureFactoryInterface::class, $featureFactory);
        $this->assertInstanceOf(FeatureFactory::class, $featureFactory);
    }

    public function testGetName()
    {
        $featureFactory = new FeatureFactory();

        $featureArray = ['name' => 'my-feature', 'enabled' => true, 'description' => 'my desc'];
        $feature = $featureFactory->create($featureArray);

        $this->assertInstanceOf(FeatureFactoryInterface::class, $featureFactory);
        $this->assertInstanceOf(FeatureFactory::class, $featureFactory);
        $this->assertSame($featureArray, $feature->toArray());
    }

}