<?php

namespace PoK\Session;

class Session
{
    const SESSION_LAST_REGENERATE_TIME = 'last_regenerate';

    public function __construct($regenerates = false)
    {
        session_start();
        if ($regenerates && !$this->has(self::SESSION_LAST_REGENERATE_TIME)) $this->set(self::SESSION_LAST_REGENERATE_TIME, time());
    }

    public function destroy()
    {
        session_destroy();
    }

    public function has($key)
    {
        return array_key_exists($key, $_SESSION);
    }

    public function get($key)
    {
        return $_SESSION[$key];
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function hashEqual($key, $value)
    {
        return hash_equals($_SESSION[$key], $value);
    }

    public function shouldRegenerate(int $regeneratePeriod)
    {
        return
            !$this->has(self::SESSION_LAST_REGENERATE_TIME) ||
            $this->get(self::SESSION_LAST_REGENERATE_TIME) < time() - $regeneratePeriod;
    }

    public function regenerate()
    {
        session_regenerate_id(true);
        $this->set(self::SESSION_LAST_REGENERATE_TIME, time());
        return $this;
    }
}