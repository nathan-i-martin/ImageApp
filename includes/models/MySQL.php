<?php

class MySQL {
    private string $hostname;
    private int $port;
    private string $username;
    private ?string $password;
    private string $database;
    private ?PDO $connection;

    function __construct(?string $hostname, ?int $port, ?string $username, ?string $password, string $database) {
        $this->hostname = $hostname ?? "localhost";
        $this->port     = $port     ?? 3306;
        $this->username = $username ?? "root";
        $this->password = $password ?? null;
        $this->database = $database;
    }

    function __destruct() {
        try {
            $this->close();
        } catch(Exception $e) {}
    }

    /**
     * Connect to the database using the credentials provided.
     * @return PDO A PDO object used to interact with the database.
     * @throws BadMethodCallException If you attempt to connect to the database if this MySQL instance is already connected.
     * @throws PDOException If there is an exception while establishing a connection.
     */
    public function connect() {
        if(isset($this->connection)) throw new BadMethodCallException("This MySQL interface is already connected!");

        $this->connection = new PDO(
            "mysql:host=$this->hostname;dbname=$this->database;port=$this->port;",
            $this->username,
            $this->password
        );

        return $this->connection;
    }

    /**
     * Close the connection to the database.
     * @throws BadMethodCallException If you attempt to close the connection to the database while this MySQL instance isn't connected.
     */
    public function close() {
        if(empty($this->connection)) throw new BadMethodCallException("There is no MySQL connection to close!");
        
        $this->connection = null;
    }

    /**
     * Create a MySQL instance from a predefined configuration.
     * @param string $connectionName The name of the configuration to load.
     * @return MySQL A MySQL instance.
     * @throws Exception If there is no configuration matching the name provided.
     * @throws RuntimeException If there is no `database` defined in the targeted config.
     */
    static function loadConfig(string $connectionName) {
        include(str_replace("models","mysql_config.php",__DIR__)); 
        
        if(!isset($mysql_config[$connectionName])) throw new Exception("No connection configuration was found with the name: $connectionName");
        
        $connectionConfig = $mysql_config[$connectionName];

        if(empty($connectionConfig["database"])) throw new RuntimeException("You must define a `database` field in your chosen config: $connectionName");
        
        return new MySQL(
            $connectionConfig["hostname"] ?? null,
            $connectionConfig["port"]     ?? null,
            $connectionConfig["username"] ?? null,
            $connectionConfig["password"] ?? null,
            $connectionConfig["database"]
        );
    }

    function __toString() {
        if(isset($this->connection)) return "MySQL (Connected): $this->hostname:$this->port - database:$this->database - user:$this->username";
        return "MySQL: $this->hostname:$this->port - user:$this->username";
    }
}