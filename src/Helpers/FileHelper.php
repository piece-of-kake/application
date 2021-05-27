<?php

namespace PoK\Helpers;

class FileHelper
{

    /**
     * Loops through the specified directory and removes all files that match the specified filename expression.
     *
     * @param string $directoryPath
     * @param string $filenameExpression
     */
    public static function deleteDirectoryFiles($directoryPath, $filenameExpression = '*')
    {
        foreach (glob("$directoryPath/$filenameExpression") as $file) {
            if(is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Retrieves an array of all directories within the specified directory, matching the directory expression.
     *
     * @param string $directoryPath
     * @param string $directoryExpression
     * @return array
     */
    public static function getAllDirectories($directoryPath, $directoryExpression = '*')
    {
        return array_filter(glob($directoryPath.'/'.$directoryExpression), 'is_dir');
    }

    /**
     * Retrieves an array of all directories within the specified directory recursively, matching the directory expression.
     * Result is a multi-dimensional array containing nested datasets in the following way:
     * - name
     * - subdirectories
     *
     * @param string $directoryPath
     * @return array
     */
    public static function getAllDirectoriesRecursively($directoryPath)
    {
        $directories = [];
        $result = self::getAllDirectories($directoryPath);
        foreach ($result as $directory) {
            $directories[] = [
//                'directory' => $directory, // This is dangerous for showing
                'name' => basename($directory),
                'subdirectories' => self::getAllDirectoriesRecursively($directory)
            ];
        }
        return $directories;
    }

    /**
     * Retrieves all files/directories from a specified directory, matching the file name expression.
     *
     * @param string $directoryPath
     * @param string $filenameExpression
     * @return array
     */
    public static function getDirectoryFiles($directoryPath, $filenameExpression = '*')
    {
        return glob($directoryPath.'/'.$filenameExpression);
    }

    public static function saveRawFileFromRequest($fileNameAndLocation)
    {
        return file_put_contents($fileNameAndLocation, file_get_contents('php://input'));
    }
}