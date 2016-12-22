<?php

namespace Sophont\Migrations\Validator;

use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\DocBlockReflection;
use Zend\Validator\AbstractValidator;

/**
 * Class MigrationFile
 *
 * @package Sophont\Migrations\Validator
 */
class MigrationFile extends AbstractValidator
{
    /** @var string $migrationClass */
    private $migrationClass;

    /** @var string $name */
    private $description;

    /** @var string $issue */
    private $issue;

    /**
     * @param mixed $migrationClass
     * @return bool
     */
    public function isValid($migrationClass)
    {
        $this->migrationClass = $migrationClass;
        return $this->hasValidDescription() &&
        $this->hasValidDownMethod() &&
        $this->hasValidUpMethod() &&
        $this->hasValidIssue();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * @return bool
     */
    private function hasValidDescription()
    {
        $reflection = new \ReflectionClass($this->migrationClass);

        $docBlock = new DocBlockReflection($reflection->getDocComment());
        $description = $docBlock->getTag("description");

        if ($description === false) {
            $this->description = "";
            return false;
        }

        $this->description = $description->getContent();
        return strlen($this->description) > 10; // Has some adequate description
    }

    /**
     * @return bool
     */
    private function hasValidIssue()
    {
        $reflection = new \ReflectionClass($this->migrationClass);

        $docBlock = new DocBlockReflection($reflection->getDocComment());

        if (!$docBlock->hasTag('issue')) {
            return true;
        }

        $issue = $docBlock->getTag("issue");
        return $issue !== false;
    }

    /**
     * Up method should exist and implemented in migration class!
     *
     * @return bool
     */
    private function hasValidUpMethod()
    {
        return $this->hasValidMethod('up');
    }

    /**
     * Down method should exist and implemented in migration class!
     *
     * @return bool
     */
    private function hasValidDownMethod()
    {
        return $this->hasValidMethod('down');
    }

    /**
     * @param $methodName
     * @return bool
     */
    private function hasValidMethod($methodName)
    {
        $method = new ClassReflection($this->migrationClass);
        $methodBody = $method->getMethod($methodName)->getBody();

        preg_match('/\\((.+)\\)/msU', $methodBody, $matchedSqlCommand);
        return count($matchedSqlCommand) >= 2;
    }
}