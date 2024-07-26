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

    /**
     * Session constructor.
     * @param int $expirationTime Seconds after creation until the session expires.
     * @param int $regenerationTime Seconds after expiration (not after creation) until the session can regenerate. After that the session is reset (all data cleared).
     * @param int $obsoleteTime Seconds after an old session which was reset or renewed will be considered as foul play.
     * @param bool $useCSRF
     * @throws InternalServerErrorException
     */
    public function __construct($expirationTime, $regenerationTime, $obsoleteTime, $useCSRF = false)
    {
        session_start();
        if (!$this->has(self::CRATED_AT_TIME)) {
            // New session
            $this
                ->set(self::CRATED_AT_TIME, time())
                ->set(self::CSRF_TOKEN, $this->generateCSRFToken());
        } elseif ($this->get(self::CRATED_AT_TIME) < time() - $expirationTime - $regenerationTime && !$this->has(self::DESTROYED_AT_TIME)) {
            // Expired session unable to renew
            $this->restart();
        } elseif ($this->get(self::CRATED_AT_TIME) < time() - $expirationTime && !$this->has(self::DESTROYED_AT_TIME)) {
            // Expired session able to renew
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

    /**
     * This will create a new empty session and relate the old one to it.
     * This should not be used from the outside context.
     * It is used internally by the expiration mechanism to be able to detect fraudulent access.
     *
     * For outside context use $this->destroy() to log the user out, reset or destroy the session.
     * @return Session
     */
    private function restart()
    {
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

        // Clean the new session
        $this
            ->set(self::CRATED_AT_TIME, time())
            ->set(self::CSRF_TOKEN, $this->generateCSRFToken());

        return $this;
    }

    /**
     * This will create a new session and migrate the data into it.
     * The old session will be marked as obsolete by setting the destruction time.
     * @return Session
     */
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
            ->set(self::CSRF_TOKEN, $this->generateCSRFToken());

        return $this;
    }

    private function generateCSRFToken()
    {
        return bin2hex(random_bytes(32));
    }
}