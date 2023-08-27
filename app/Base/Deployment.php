<?php

namespace App\Base;

use App\App;
use App\Models\User;
use App\Response;

/**
 * Class Deployment
 * @package App\Base
 */
class Deployment extends BaseObject
{
    public Response $response;
    private string $dbName;

    private const USERS_TABLE = 'users';
    private const FILES_TABLE = 'files';
    private const DIRECTORIES_TABLE = 'directories';
    private const MAILS_TABLE = 'mail_queue';

    /**
     * Warning! Change admin credentials after application deployment!
     */
    private const ADMIN_NAME = 'Admin';
    private const ADMIN_EMAIL = 'admin@example.com';
    private const ADMIN_PASSWORD = '@dmin12-34!';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        App::$response = $this->response = new Response();
        $this->dbName = App::$db->name;

        parent::init();
    }

    /**
     * @return Response
     */
    public function start(): Response
    {
        if (App::$params['app_deployed']) {
            return $this->response(['message' => 'Application already configured']);
        }

        $this->createUsersTable();
        $this->createFilesTable();
        $this->createDirectoriesTable();
        $this->createMailsTable();
        $this->createAdmin();

        return $this->response(['message' => 'Application successfully configured']);
    }

    /**
     * @param array $data
     * @return Response
     */
    private function response(array $data = []): Response
    {
        $this->response->data = $data;
        return $this->response;
    }

    /**
     * @return void
     */
    private function createUsersTable(): void
    {
        $this->dropTable(self::USERS_TABLE);

        $sql = 'CREATE TABLE `' . $this->dbName . '`.`' . self::USERS_TABLE . '` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `role_id` TINYINT UNSIGNED NOT NULL,
          `name` VARCHAR(255) NOT NULL,
          `email` VARCHAR(255) NOT NULL,
          `password` VARCHAR(255) NOT NULL,
          `token` VARCHAR(255) NULL DEFAULT NULL,
          `last_request` DATETIME NULL DEFAULT NULL,
          `restoration_key` VARCHAR(64) NULL DEFAULT NULL,
          `key_exp_date` TIMESTAMP NULL DEFAULT NULL,
          `created_at` TIMESTAMP NOT NULL,
          `updated_at` TIMESTAMP NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)';

        $this->query($sql);
    }

    /**
     * @return void
     */
    private function createFilesTable(): void
    {
        $this->dropTable(self::FILES_TABLE);

        $sql = 'CREATE TABLE `' . $this->dbName . '`.`' . self::FILES_TABLE . '` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `user_id` INT UNSIGNED NOT NULL,
          `directory_id` INT UNSIGNED NULL DEFAULT NULL,
          `name` VARCHAR(255) NOT NULL,
          `real_name` VARCHAR(255) NOT NULL,
          `ext` VARCHAR(8) NOT NULL,
          `hash` VARCHAR(64) NOT NULL DEFAULT \'\',
          `state` TINYINT NOT NULL DEFAULT \'0\',
          `shared_to` VARCHAR(255) NULL DEFAULT \'\',
          `path` TEXT NOT NULL,
          `created_at` DATETIME NOT NULL,
          `updated_at` DATETIME NULL DEFAULT NULL,
          PRIMARY KEY (`id`))';

        $this->query($sql);
    }

    /**
     * @return void
     */
    private function createDirectoriesTable(): void
    {
        $this->dropTable(self::DIRECTORIES_TABLE);

        $sql = 'CREATE TABLE `' . $this->dbName . '`.`' . self::DIRECTORIES_TABLE . '` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `user_id` INT UNSIGNED NOT NULL,
          `parent_id` INT UNSIGNED NULL DEFAULT NULL,
          `name` VARCHAR(255) NOT NULL,
          `created_at` DATETIME NOT NULL,
          `updated_at` DATETIME NULL DEFAULT NULL,
          PRIMARY KEY (`id`));';

        $this->query($sql);
    }

    /**
     * @return void
     */
    private function createMailsTable(): void
    {
        $this->dropTable(self::MAILS_TABLE);

        $sql = 'CREATE TABLE `' . $this->dbName . '`.`' . self::MAILS_TABLE . '` (
          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
          `date_create` DATETIME NOT NULL,
          `from` VARCHAR(255) NOT NULL,
          `to` VARCHAR(255) NOT NULL,
          `subject` TEXT NOT NULL,
          `message` TEXT NOT NULL,
          `status` INT UNSIGNED NOT NULL DEFAULT \'0\',
          `attempt` TINYINT UNSIGNED NOT NULL DEFAULT \'0\',
          PRIMARY KEY (`id`))';

        $this->query($sql);
    }

    /**
     * @return void
     */
    private function createAdmin(): void
    {
        $sql = 'INSERT INTO `' . $this->dbName . '`.`' . self::USERS_TABLE . '`
            (`role_id`, `name`, `email`, `password`, `created_at`)
            VALUES (
                ' . User::ROLE_ADMIN . ',
                \'' . self::ADMIN_NAME . '\',
                \'' . self::ADMIN_EMAIL . '\',
                \'' . password_hash(self::ADMIN_PASSWORD, PASSWORD_DEFAULT) . '\',
                \'' . now() . '\'
            )';

        $this->query($sql);
    }

    /**
     * @param string $sql
     * @return void
     */
    private function query(string $sql): void
    {
        App::$db->createCommand($sql)->query();
    }

    /**
     * @param string $name
     * @return void
     */
    private function dropTable(string $name): void
    {
        $sql = 'DROP TABLE IF EXISTS `' . $this->dbName . '`.`' . $name . '`';
        $this->query($sql);
    }
}
