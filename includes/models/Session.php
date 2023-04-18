<?php

class Session {
    private SessionStatus $status = SessionStatus::UNDEFINED; 
    private static Session $instance;

    /**
     * Start the session.
     * 
     * If a session has already been started, we simply return that session.
     * @return Session The session.
     */
    static function start() {
        if (!isset(self::$instance)) self::$instance = new self;
        
        if(self::$instance->status == SessionStatus::STARTED) return self::$instance;
        
        if(session_start())
            self::$instance->status = SessionStatus::STARTED;
        else
            self::$instance->status = SessionStatus::UNDEFINED;

        return self::$instance;
    }

    /**
     * End the current session.
     * @throws LogicException Of the session hasn't been started yet.
     */
    public function kill() {
        if(!$this->exists()) throw new LogicException("You must start a session before killing it!");

        session_unset();
        session_destroy();
        $this->status == SessionStatus::DEAD;
    }


    /**
     * Set data to the session.
     * @param string $key The key of the data to set.
     * @param mixed $value The value associated with the key.
     * @throws LogicException If the session hasn't been started yet.
     * @throws InvalidArgumentException If the session doesn't contain the key.
     */
    public function __set(string $key, mixed $value) {
        if(!$this->exists()) throw new LogicException("You must start a session before putting data into it!");

        $_SESSION[$key] = $value;
    }

    /**
     * Get data from the session.
     * @param string $key The key of the data to remove.
     * @return any The value associated with the key.
     * @throws LogicException If the session hasn't been started yet.
     * @throws InvalidArgumentException If the session doesn't contain the key.
     */
    public function __get(string $key) {
        if(!$this->exists()) throw new LogicException("You must start a session before getting data from it!");
        if(!$this->isset($key)) throw new InvalidArgumentException("This key doesn't exist in the session!");
        
        return $_SESSION[$key];
    }

    /**
     * Remove data from the session.
     * @param string $key The key of the data to remove.
     * @throws LogicException Of the session hasn't been started yet.
     */
    public function remove(string $key) {
        if(!$this->exists()) throw new LogicException("You must start a session before removing data from it!");
        
        unset($_SESSION[$key]);
    }

    /**
     * Check if session key exists.
     * @param string $key The key to check for.
     * @return bool `true` if the key exists. `false` otherwise.
     */
    public function isset(string $key) {
        if(!$this->exists()) return false;
        
        return isset($_SESSION[$key]);
    }

    /**
     * Check if the session exists.
     */
    public function exists() {
        return $this->status == SessionStatus::STARTED;
    }
}

enum SessionStatus {
    case STARTED;
    case DEAD;
    case UNDEFINED;
}