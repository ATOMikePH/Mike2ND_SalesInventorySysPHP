<?php

$backup = new BackupMyProject('../../ATOM_SMS');

print_r($backup);

/*
Then your have the object properties to determine the backup

$backup = BackupMyProject Object
(
    [project_path] => ./path/to/project/yada
    [backup_file] => ./project_backups/yada.zip
)

Alternatively set the second parameter and just send the project as a download.
BackupMyProject('./path/to/project/yada', true);
*/



class BackupMyProject {
    // Project files working directory - automatically created
    public $PWD;

    /**
     * Class properties.
     *
     * @var string $project_path
     * @var string $backup_file
     */
    public $project_path;
    public $backup_file;

    /**
     * Class construct.
     *
     * @param string $path
     * @param bool $download
     */
    function __construct($path = null, $download = false)
    {
        // Check construct argument
        if (!$path) die(__CLASS__ . ' Error: Missing construct param: $path');
        if (!file_exists($path)) die(__CLASS__ . ' Error: Path not found: ' . htmlentities($path));
        if (!is_readable($path)) die(__CLASS__ . ' Error: Path not readable: ' . htmlentities($path));

        $userProfile = getenv('USERPROFILE'); 

        // Set the download path dynamically
        $this->PWD = "{$userProfile}\\Downloads\\";

        // Set working vars
        $this->project_path = rtrim($path, '/');
        $timestamp = date('Ymd_His'); // Get current date and time as a timestamp
        $this->backup_file = $this->PWD . basename($this->project_path) . '_Application_' . $timestamp . '.zip';

        // Make project backup folder
        if (!file_exists($this->PWD)) {
            mkdir($this->PWD, 0777, true); // Set permission to 0777 for full access
        }

        // Zip project files
        try {
            $this->zipcreate($this->project_path, $this->backup_file);
        } catch (Exception $e) {
            die($e->getMessage());
        }

        if ($download !== false) {
            // Set the download filename dynamically based on timestamp
            $downloadFilename = 'system_' . $timestamp . '.zip';

            // Change the Content-Disposition header to suggest a filename
            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . sprintf("%u", filesize($this->backup_file)));
            readfile($this->backup_file);
            // Cleanup
            unlink($this->backup_file);
            // Exit to prevent any additional output
            exit;
        }
    }

    /**
     * Create zip from extracted/fixed project.
     *
     * @uses ZipArchive
     * @uses RecursiveIteratorIterator
     * @param string $source
     * @param string $destination
     * @return bool
     */
    function zipcreate($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            throw new Exception(__CLASS__ . ' Fatal error: ZipArchive required to use BackupMyProject class');
        }
        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            throw new Exception(__CLASS__ . ' Error: ZipArchive::open() failed to open path');
        }
        $source = str_replace('\\', '/', realpath($source));
        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file) {
                $file = str_replace('\\', '/', realpath($file));
                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        return $zip->close();
    }
}