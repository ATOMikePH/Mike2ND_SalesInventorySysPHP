<?php

if (!defined("DB_USER")) {
    define("DB_USER", 'root');
}

if (!defined("DB_PASSWORD")) {
    define("DB_PASSWORD", '');
}

if (!defined("DB_NAME")) {
    define("DB_NAME", 'atom_sms');
}

if (!defined("DB_HOST")) {
    define("DB_HOST", 'localhost');
}

if (!defined("BACKUP_DIR")) {
    define("BACKUP_DIR", 'backup_file');
}

define("TABLES", '*');
define('IGNORE_TABLES', array(
    'tbl_token_auth',
    'token_auth'
));
define("CHARSET", 'utf8');
define("GZIP_BACKUP_FILE", false);
define("DISABLE_FOREIGN_KEY_CHECKS", true);
define("BATCH_SIZE", 1000);

$userProfile = getenv('USERPROFILE'); 

$backupDir = "";

class Backup_Database
{
    var $host;
    var $username;
    var $passwd;
    var $dbName;
    var $charset;
    var $conn;
    var $backupDir;
    var $backupFile;
    var $gzipBackupFile;
    var $output;
    var $disableForeignKeyChecks;
    var $batchSize;

    public function __construct($host, $username, $passwd, $dbName, $backupDir, $charset = 'utf8')
    {
        $this->host = $host;
        $this->username = $username;
        $this->passwd = $passwd;
        $this->dbName = $dbName;
        $this->charset = $charset;
        $this->conn = $this->initializeDatabase();
        $this->backupDir = $backupDir ? $backupDir : '.';
        $this->backupFile = 'EJJ' . $this->dbName . '-' . date("Ymd_His", time()) . '.sql';
        $this->gzipBackupFile = defined('GZIP_BACKUP_FILE') ? GZIP_BACKUP_FILE : false;
        $this->disableForeignKeyChecks = defined('DISABLE_FOREIGN_KEY_CHECKS') ? DISABLE_FOREIGN_KEY_CHECKS : true;
        $this->batchSize = defined('BATCH_SIZE') ? BATCH_SIZE : 1000;
        $this->output = '';
    }

    protected function initializeDatabase()
    {
        try {
            $conn = mysqli_connect($this->host, $this->username, $this->passwd, $this->dbName);
            if (mysqli_connect_errno()) {
                throw new Exception('ERROR connecting database: ' . mysqli_connect_error());
                die();
            }
            if (!mysqli_set_charset($conn, $this->charset)) {
                mysqli_query($conn, 'SET NAMES ' . $this->charset);
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
        }

        return $conn;
    }

    public function backupTables($tables = '*')
    {
        try {
            if ($tables == '*') {
                $tables = array();
                $result = mysqli_query($this->conn, 'SHOW TABLES');
                while ($row = mysqli_fetch_row($result)) {
                    $tables[] = $row[0];
                }
            } else {
                $tables = is_array($tables) ? $tables : explode(',', str_replace(' ', '', $tables));
            }

            $sql = 'CREATE DATABASE IF NOT EXISTS `' . $this->dbName . '`' . ";\n\n";
            $sql .= 'USE `' . $this->dbName . "`;\n\n";

            if ($this->disableForeignKeyChecks === true) {
                $sql .= "SET foreign_key_checks = 0;\n\n";
            }

            foreach ($tables as $table) {
                if (in_array($table, IGNORE_TABLES))
                    continue;
                $this->obfPrint("Backing up `" . $table . "` table..." . str_repeat('.', 50 - strlen($table)), 0, 0);

                $sql .= 'DROP TABLE IF EXISTS `' . $table . '`;';
                $stmt = $this->conn->prepare('SHOW CREATE TABLE `' . $table . '`');
                $stmt->execute();
                $result = $stmt->get_result();

                $row = $result->fetch_row();
                $sql .= "\n\n" . $row[1] . ";\n\n";

                $row = mysqli_fetch_row(mysqli_query($this->conn, 'SELECT COUNT(*) FROM `' . $table . '`'));
                $numRows = $row[0];

                $numBatches = intval($numRows / $this->batchSize) + 1;

                for ($b = 1; $b <= $numBatches; $b++) {

                    $query = 'SELECT * FROM `' . $table . '` LIMIT ' . ($b * $this->batchSize - $this->batchSize) . ',' . $this->batchSize;
                    $result = mysqli_query($this->conn, $query);
                    $realBatchSize = mysqli_num_rows($result);

                    if ($realBatchSize > 0) {

                        $sql .= "INSERT INTO `" . $table . "` VALUES\n";
                        $values = array();
                        while ($row = mysqli_fetch_assoc($result)) {
                            foreach ($row as &$value) {
                                $value = "'" . $this->conn->real_escape_string($value) . "'";
                            }
                            $values[] = '(' . implode(', ', $row) . ')';
                        }
                        $sql .= implode(",\n", $values) . ";\n\n";
                    }

                    $this->obfPrint(".", 0, 0);
                }

                $this->obfPrint(" OK\n", 0, 0);
            }

            if ($this->disableForeignKeyChecks === true) {
                $sql .= "SET foreign_key_checks = 1;\n\n";
            }

            if ($this->gzipBackupFile === true) {
                $this->backupFile .= '.gz';
            }

            if (!is_dir($this->backupDir)) {
                mkdir($this->backupDir, 0777, true);
            }

            file_put_contents($this->backupDir . '/' . $this->backupFile, $sql);

            $this->obfPrint("Backup complete.\n", 0, 0);

            if ($this->gzipBackupFile === true) {
                $this->obfPrint("File compressed with gzip.\n", 0, 0);
            }

            $this->output = $this->backupDir . '/' . $this->backupFile;
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
        }
    }

    protected function obfPrint($text, $newline_before = 1, $newline_after = 1)
    {
        $this->output .= $text;
    }

    public function getBackupDir()
    {
        return $this->backupDir;
    }

    public function getBackupFile()
    {
        return $this->backupFile;
    }
}
// Initialize and perform backup
$backupDatabase = new Backup_Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $backupDir);
$backupDatabase->backupTables(TABLES);

// Force download the backup file
$backupFilePath = $backupDatabase->getBackupDir() . '/' . $backupDatabase->getBackupFile();
if (file_exists($backupFilePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($backupFilePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backupFilePath));
    readfile($backupFilePath);
    exit;
} else {
    echo 'Backup file not found.';
}
?>
