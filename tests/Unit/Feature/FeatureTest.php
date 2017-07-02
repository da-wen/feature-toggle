<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 01.07.17
 * Time: 20:19
 */

namespace Dawen\FeatureToggle\Tests\Unit\Feature;

use Dawen\FeatureToggle\Feature\Feature;
use Dawen\FeatureToggle\Feature\FeatureInterface;

class FeatureTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $feature = new Feature('my-feature');

        $this->assertInstanceOf(FeatureInterface::class, $feature);
        $this->assertInstanceOf(Feature::class, $feature);
    }

    public function testGetName()
    {
        $name = 'my-feature';
        $feature = new Feature($name);

        $this->assertSame($name, $feature->getName());
    }

    public function testConstructorDefaults()
    {
        $name = 'my-feature';
        $feature = new Feature($name);

        $this->assertFalse($feature->isEnabled());
        $this->assertNull($feature->getDescription());
    }

    public function testIsEnabled()
    {
        $name = 'my-feature';
        $feature = new Feature($name, true);

        $this->assertTrue($feature->isEnabled());
    }

    public function testGetDescription()
    {
        $name = 'my-feature';
        $description = 'my description';
        $feature = new Feature($name, true, $description);

        $this->assertNotNull($feature->getDescription());
        $this->assertSame($description, $feature->getDescription());
    }

    public function testSetGetOptions()
    {
        $name = 'my-feature';
        $description = 'my description';
        $feature = new Feature($name, true, $description, ['my' => 'val']);

        $this->assertCount(1, $feature->getOptions());
        $this->assertContains('val', $feature->getOptions());

        $feature->setOptions(['one', 'two']);
        $this->assertCount(2, $feature->getOptions());
        $this->assertContains('one', $feature->getOptions());
        $this->assertContains('two', $feature->getOptions());
    }

    public function testHasOptionsTrue()
    {
        $name = 'my-feature';
        $feature = new Feature($name, true, null, ['my' => 'val']);

        $this->assertTrue($feature->hasOptions());
    }

    public function testHasOptionsFalse()
    {
        $name = 'my-feature';
        $feature = new Feature($name);

        $this->assertFalse($feature->hasOptions());
    }

    public function testToArray()
    {
        $name = 'my-feature';
        $description = 'my new description';
        $enabled = false;

        $feature = new Feature($name, $enabled, $description);

        $featureArray = $feature->toArray();

        $this->assertTrue(isset($featureArray['name']));
        $this->assertSame($feature->getName(), $featureArray['name']);
        $this->assertTrue(isset($featureArray['enabled']));
        $this->assertSame($feature->isEnabled(), $featureArray['enabled']);
        $this->assertTrue(isset($featureArray['description']));
        $this->assertSame($feature->getDescription(), $featureArray['description']);
    }

    public function testFromArray()
    {
        $name = 'my-feature';

        $feature = new Feature($name);

        $values = [
            'name'        => 'new-name',
            'enabled'     => true,
            'description' => 'desc 123',
            'options'     => ['oprion-one']
        ];

        $feature->fromArray($values);

        $this->assertSame($values['name'], $feature->getName());
        $this->assertSame($values['enabled'], $feature->isEnabled());
        $this->assertSame($values['description'], $feature->getDescription());
        $this->assertSame($values['options'], $feature->getOptions());
    }
}