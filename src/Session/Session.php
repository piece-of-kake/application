<?php

namespace PoK\Session;

use PoK\Exception\ServerError\InternalServerErrorException;

/**
 * Class Session
 * Built based on:
 * https://www.php.net/manual/en/session.security.ini.php
 * https://www.php.net/manual/en/features.session.security.management.php
 *
 * @package PoK\Session
 */
class Session
{
    const CRATED_AT_TIME = 'ct';
    const DESTROYED_AT_TIME = 'da';
    const NEW_SESSION_ID = 'nsid';
    const CSRF_TOKEN = 'csrf';

    public function __construct($expirationTime = 300, $obsoleteTime = 900, $useCSRF = true)
    {
        session_start();
        if (!$this->has(self::CRATED_AT_TIME)) {
            // New session
            $this
                ->set(self::CRATED_AT_TIME, time())
                ->set(self::CSRF_TOKEN, $this->generateCSRHToken());
        } elseif ($this->get(self::CRATED_AT_TIME) < time() - $expirationTime && !$this->has(self::DESTROYED_AT_TIME)) {
            // Expired session
            $this->regenerate();
        } elseif ($this->has(self::DESTROYED_AT_TIME)) {
            // Obslete session
            if ($this->get(self::DESTROYED_AT_TIME) < time() - $obsoleteTime) {
                // ToDo: Remove all authentication status of this users session.
                // ToDo: record the falure along with an IP and any valuble information for further investigtion. Might be an attack or just a consequence of bad network connection.
                throw(new InternalServerErrorException('Deleted session - record created'));
            }
            if ($this->has(self::NEW_SESSION_ID)) {
                // Not fully expired yet. Could be lost cookie by unstable network.
                // Try again to set proper session ID cookie.
                // NOTE: Do not try to set session ID again if you would like to remove
                // authentication flag.
                session_commit();
                session_id($this->get(self::NEW_SESSION_ID));
                session_start();
            }
        }
    }

    /**
     * IMPORTANT!!!
     * Be very cautious when using this.
     * Use only when logging users out or similar situations.
     * DO NOT USE THIS FOR SESSION EXPIRATION!
     */
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

    public function unset($key)
    {
        unset($_SESSION[$key]);
        return $this;
    }

    public function checkCSRF($token)
    {
        return $this->hashEqual(self::CSRF_TOKEN, $token);
    }

    public function getCSRF()
    {
        return $this->get(self::CSRF_TOKEN);
    }

    public function hashEqual($key, $value)
    {
        return hash_equals($_SESSION[$key], $value);
    }

    public function getId()
    {
        return session_id();
    }

    public function regenerate()
    {
        // Backing up old data
        $oldSessionData = (array) $_SESSION;

        // New session ID is required to set proper session ID
        // when session ID is not set due to unstable network.
        $newSessionId = session_create_id();
        $this
            ->set(self::NEW_SESSION_ID, $newSessionId)
            ->set(self::DESTROYED_AT_TIME, time()); // Set destroy timestamp

        // Write and close current session;
        session_commit();

        // Start session with new session ID
        session_id($newSessionId);
        ini_set('session.use_strict_mode', 0);
        session_start(); // If we use session_regenerate_id here we will switch from the old one to the new one without writing the new session ID into the old session
        ini_set('session.use_strict_mode', 1);

        // Replacing old session data into the new session
        $_SESSION = $oldSessionData;

        // Clean the new session
        $this
            ->set(self::CRATED_AT_TIME, time())
            ->set(self::CSRF_TOKEN, $this->generateCSRHToken());

        return $this;
    }

    private function generateCSRHToken()
    {
        return bin2hex(random_bytes(32));
    }
}