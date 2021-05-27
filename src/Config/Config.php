<?php

namespace PoK\Config;

use PoK\Exception\ServerError\InternalServerErrorException;

class Config
{
    public static function get($configFilePath, $string, $default = null)
    {
        $parsedString = explode('.', $string);

        if (count($parsedString) < 1) {
            // Invalid configuration name
            throw new InternalServerErrorException();
        }

        try {
            $configuration = include $configFilePath;
        }
        catch (\Exception $ex) {
            // Configuration file doesn't exist
            throw new InternalServerErrorException();
        }

        try {
            $configValue = self::getConfigValueFromArray($parsedString, $configuration);
        }
        catch (\Exception $ex) {
            return $default;
        }

        return $configValue;
    }

    private static function getConfigValueFromArray($arrayPath, $array)
    {
        if (isset($arrayPath[0])) {
            if (isset($arrayPath[1])) {
                return self::getConfigValueFromArray(array_slice($arrayPath, 1), $array[$arrayPath[0]]);
            }
            else {
                if (!array_key_exists($arrayPath[0], $array))
                    throw new InternalServerErrorException();
                return $array[$arrayPath[0]];
            }
        }
    }
}