<?php

namespace Gerhardn\NatisBot\Helper;

use Symfony\Component\Yaml\Yaml;

class ParameterLoader
{
    private $parameterBag;

    public function __construct()
    {
        $value = Yaml::parseFile(__DIR__."/../../parameters.yml");

        $this->parameterBag = $value['parameters'];
    }

    public function get($value)
    {
        return $this->parameterBag[$value];
    }
}