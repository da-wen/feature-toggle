<?php
/**
 * Created by PhpStorm.
 * User: dawen
 * Date: 01.07.17
 * Time: 10:03
 */

namespace Dawen\FeatureToggle\Feature;

/**
 * Class Feature
 *
 * @package Dawen\FeatureToggle\Feature
 */
class Feature implements FeatureInterface
{
    /**
     * @var string
     */
    private $name = null;

    /**
     * @var bool
     */
    private $enabled = false;

    /**
     * @var null|string
     */
    private $description = null;

    /**
     * @var array
     */
    private $options = [];

    public function __construct($name, $enabled = false, $desciption = null, array $options = [])
    {
        $this->setName($name);
        $this->setEnabled($enabled);
        $this->setDescription($desciption);
        $this->setOptions($options);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @inheritdoc
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function hasOptions()
    {
        return count($this->options) > 0;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            'name'        => $this->name,
            'enabled'     => $this->enabled,
            'description' => $this->description
        ];
    }

    /**
     * Maps data from array to object
     *
     * @param array $properties
     * @author dawen
     */
    public function fromArray(array $properties)
    {
        if (isset($properties['name'])) {
            $this->setName($properties['name']);
        }

        if (isset($properties['enabled'])) {
            $this->setEnabled($properties['enabled']);
        }

        if (isset($properties['description'])) {
            $this->setDescription($properties['description']);
        }

        if (isset($properties['options'])) {
            $this->setOptions($properties['options']);
        }
    }
}