<?php

namespace App;

use App\Base\BaseObject;

/**
 * Class UploadedFile
 * @package App
 *
 * @property-read string $baseName
 * @property-read string $extension
 * @property-read bool $hasError
 */
class UploadedFile extends BaseObject
{
    public string $name;
    public string $tempName;
    public string $type;
    public int $size;
    public int $error;

    private static ?array $_files = null;

    /**
     * @return UploadedFile|null
     */
    public static function getInstance(): ?UploadedFile
    {
        $name = array_key_first($_FILES);
        $files = self::loadFiles();

        return isset($files[$name]) ? new static($files[$name]) : null;
    }

    /**
     * @return UploadedFile[]
     */
    public static function getInstances(): array
    {
        $name = array_key_first($_FILES);
        $files = self::loadFiles();

        if (isset($files[$name])) {
            return [new static($files[$name])];
        }

        $results = [];

        foreach ($files as $key => $file) {
            if (str_starts_with($key, "{$name}[")) {
                $results[] = new static($file);
            }
        }

        return $results;
    }

    /**
     * @return void
     */
    public static function reset(): void
    {
        self::$_files = null;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function saveAs(string $file): bool
    {
        return !$this->hasError && move_uploaded_file($this->tempName, FILES . $file);
    }

    /**
     * @return string
     */
    public function getBaseName(): string
    {
        $pathInfo = pathinfo('_' . $this->name, PATHINFO_FILENAME);
        $length = mb_strlen($pathInfo, '8bit');

        return mb_substr($pathInfo, 1, $length, '8bit');
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
    }

    /**
     * @return bool
     */
    public function getHasError(): bool
    {
        return $this->error !== UPLOAD_ERR_OK;
    }

    /**
     * @return array
     */
    private static function loadFiles(): array
    {
        if (self::$_files === null) {
            self::$_files = [];
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $key => $info) {
                    self::loadFilesRecursive($key, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error']);
                }
            }
        }

        return self::$_files;
    }

    /**
     * @param string $key
     * @param mixed $names
     * @param mixed $tempNames
     * @param mixed $types
     * @param mixed $sizes
     * @param mixed $errors
     * @return void
     */
    private static function loadFilesRecursive(string $key, mixed $names, mixed $tempNames, mixed $types, mixed $sizes, mixed $errors): void
    {
        if (is_array($names)) {
            foreach ($names as $i => $name) {
                self::loadFilesRecursive($key . '[' . $i . ']', $name, $tempNames[$i], $types[$i], $sizes[$i], $errors[$i]);
            }
        } elseif ((int)$errors !== UPLOAD_ERR_NO_FILE) {
            self::$_files[$key] = [
                'name' => $names,
                'tempName' => $tempNames,
                'type' => $types,
                'size' => $sizes,
                'error' => $errors,
            ];
        }
    }
}
