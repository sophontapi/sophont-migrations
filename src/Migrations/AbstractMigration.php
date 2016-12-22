<?php

namespace Sophont\Migrations\Migrations\Migrations;

use \Doctrine\DBAL\Migrations\AbstractMigration as DoctrineAbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Zend\Code\Reflection\DocBlockReflection;

abstract class AbstractMigration extends DoctrineAbstractMigration
{
    public function preUp(Schema $schema)
    {
        if($env = $this->getTag("env") !== null) {
            $this->skipIf(getenv("APPLICATION_ENV") !== $env);
            return;
        }

        $desc = $env = $this->getTag("description");;
        echo sprintf(
            "Executing migration \"%s\" \n",
            $desc
        );
    }

    /**
     * @param $name
     * @return false|\Zend\Code\Reflection\DocBlock\Tag\TagInterface
     */
    private function getTag($name)
    {
        $reflection = new \ReflectionClass(get_class($this));

        $docBlock = new DocBlockReflection($reflection->getDocComment());

        $tag = $docBlock->getTag($name);
        if($tag === false) {
            return null;
        }
        return $docBlock->getTag($name)->getContent();
    }
}