<?php
require_once 'Viadutoo/db/BaseStorage.php';

class SQLite3Storage extends BaseStorage {
    /** @var SQLite3Result|bool  SQLite3Result on success, FALSE on failure. */
    protected $_lastNativeResultFromStore;
    /** @var SQLite3 */
    private $_databaseHandle;
    /** @var string */
    private $_tableName;

    /**
     * @param string $databaseFilename
     */
    public function __construct($databaseFilename, $tableName = 'events') {
        $databaseFilename = strval($databaseFilename);
        $tableName = strval($tableName);

        $this->_tableName = $tableName;
        $this->_databaseHandle = new SQLite3($databaseFilename);

        if ($this->_databaseHandle->lastErrorCode() != 0) {
            throw new Exception('Unable to connect to DB.');
        }

        $this->_databaseHandle->exec(<<<"EOT"
            CREATE TABLE IF NOT EXISTS $tableName (
                id INTEGER PRIMARY KEY,
                headers STRING,
                body STRING
            )
EOT
        );
    }

    /**
     * @param string[] $headers
     * @param string $body
     * @return bool Success
     */
    public function store($headers, $body) {
        if (!is_array($headers)) {
            $headers = [$headers];
        }
        $body = strval($body);

        $tableName = $this->_tableName;
        $statement = $this->_databaseHandle
            ->prepare("INSERT INTO $tableName (id, headers, body) VALUES (null, :headers, :body)");
        $statement->bindValue(':headers', json_encode($headers), SQLITE3_TEXT);
        $statement->bindValue(':body', $body, SQLITE3_TEXT);
        $result = $statement->execute();

        $this->_lastNativeResultFromStore = $result;
        $this->_lastSuccessFromStore = ($result !== false);

        return $this->_lastSuccessFromStore;
    }
}