<?php

namespace App\Helper;

use Symfony\Component\Dotenv\Dotenv;

/**
 * Class DotEnvHelper
 *
 * @method static string someEnvKeyName()
 */
class DotEnvHelper
{

    /**
     * @var DotEnvHelper
    */
    private static DotEnvHelper $instance;


    private function __construct()
    {
        (new Dotenv(true))->load(__DIR__ . '/../../.env');
    }

    /**
     * @param string $envKey env variable name
     * @return string
     */
    public function getValueByKey(string $envKey): string
    {
        return (string)getenv($envKey);
    }

    /**
     * @return $this
    */
    public static function obj(): self
    {
        return self::$instance ?? (self::$instance = new DotEnvHelper());
    }


    /**
     * @var string $method
     * @var array $arguments
     * @return bool
     * @throws
     */
    public static function __callStatic(string $method, array $arguments)
    {
        preg_match_all(
            '/([A-Z]+)/u',
            $method,
            $kA
        );
        if (!isset($kA[0])) return false;

        foreach(array_flip(array_flip($kA[0])) as $sep)
            $method = str_replace($sep, "_" . $sep, $method);

        $envKey = strtoupper($method);

        foreach ($arguments as $envKeyPart)
            $envKey .= "_" . strtoupper($envKeyPart);

        return static::obj()->getValueByKey($envKey);
    }
}