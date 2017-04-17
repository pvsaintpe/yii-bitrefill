<?php

namespace bitrefill\base;

use phpDocumentor\Reflection\DocBlock;

class Object
{
    protected $otherAttributes = [];

    /**
     * Object constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $reflection = new \ReflectionClass($this);

        $filled = false;
        foreach ($config as $attribute => $value) {
            if (!property_exists($this, $attribute)) {
                $this->otherAttributes[$attribute] = $value;
                continue;
            }

            $this->{$attribute} = $value;
            $filled = true;
            $property = $reflection->getProperty($attribute);

            if (!($doc = new DocBlock($property->getDocComment()))) {
                continue;
            }

            /** @var DocBlock\Tag\VarTag $tag */
            if (!($tags = $doc->getTagsByName('var'))) {
                continue;
            }
            $tag = $tags[0];
            $type = $tag->getType();

            if (strpos($type, '[]') !== false) {
                $class = str_replace('[]', '', $tag->getType());
                if (!class_exists($class)) {
                    continue;
                }

                $this->{$attribute} = [];
                foreach ($value as $item) {
                    $this->{$attribute}[] = new $class($item);
                }
            } else {
                if (class_exists($type)) {
                    $this->{$attribute} = new $type($value);
                }
            }
        }

        if (!$filled) {
            $index = 0;
            foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                if (isset($config[$index])) {
                    $this->{$property->getName()} = $config[$index];
                }
                $index++;
            }
        }
    }

    /**
     * @return array
     */
    public function getOtherAttributes()
    {
        return $this->otherAttributes;
    }
}