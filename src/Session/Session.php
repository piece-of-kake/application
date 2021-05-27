<?php

namespace PoK\Session;

class Session
{
    const SESSION_START_TIME = 'start';
    const SESSION_LAST_REGENERATE_TIME = 'last_regenerate';

    public function __construct($regenerates = false)
    {
        session_start();
        if ($regenerates) $this->injectStartTime();
    }

    public function reset()
    {
        $this->resetStartTime();
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

    public function regenerateIfNecessary(int $regeneratePeriod)
    {
        if (
            !$this->has(self::SESSION_LAST_REGENERATE_TIME) ||
            $this->get(self::SESSION_LAST_REGENERATE_TIME) < time() - $regeneratePeriod
        ) {
            session_regenerate_id(true);
            $this->set(self::SESSION_LAST_REGENERATE_TIME, time());
        }
        return $this;
    }

    public function checkExpiration(int $expirationPeriod)
    {
        if (
            !$this->has(self::SESSION_START_TIME) ||
            $this->get(self::SESSION_START_TIME) < time() - $expirationPeriod
        ) {
            $this->destroy();
        }
        return $this;
    }

    private function injectStartTime()
    {
        if (
            !$this->has(self::SESSION_START_TIME) ||
            !$this->has(self::SESSION_LAST_REGENERATE_TIME)
        ) {
            $this->resetStartTime();
        }
    }

    private function resetStartTime()
    {
        $this->set(self::SESSION_START_TIME, time());
        $this->set(self::SESSION_LAST_REGENERATE_TIME, time());
        return $this;
    }
}