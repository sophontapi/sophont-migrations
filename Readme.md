# DB Migrations

#### Installation
```sh
$ composer require currencysolutions/zf2-migrations
```

#### Usage

```sh
$ php vendor/bin/migration.php new <moduleName>
```
> Generate new migration file where **moduleName** is an existing module in you project. After generation you can find new created migration file under **migrations** directory in specified module (e.g. zf2-news)

```sh
$ php vendor/bin/migration.php status
```
> Command displays migrations current state

```sh
$ php vendor/bin/migration.php migrate
```
> Execute all available unexecuted migrations

#### A valid migration file
Every migration file has a set of rules
1. A migration file should contain annotation tag @description which describes migration intent.
2. A migration file should implement **up** method which is executed during migration
3. A migration file should implement **down** method whcih is executed during rollback
4. A migration namespace should be CurrencySolutions\Migrations
5. Your migration has to extend from CurrencySolutions\Migrations\Migrations\AbstractMigration class

Additionally you can
1. If a migration concerns any jira issue you should specify issue number in @issue tag (e.g. @issue CSMS-1250)
2. You can create environment specific migration by @env tag (eg @env docker_test) and migration will be skipped in all  environments other then specified one

# WARNING: Your build will fail if your commit contains an invalid migration file

> To execute sql inside **up** and **down** method call $this->addSql($sqlString). There are several useful methods available which can be used within this methods

**A valid migration file example**

```sh
namespace CurrencySolutions\Migrations;

use CurrencySolutions\Migrations\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 * @description Create new `status` field in user table  (required)
 * @issue DESC-1222/CSMS-540/OPLATFORM-100  (optional)
 * @env docker-test  (optional)
 */
class Version20161021132420 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE `users` ADD COLUMN `status` VARCHAR(45) NULL AFTER `auth_token`;
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE `users` DROP COLUMN `status``;
        ");
    }
}

```