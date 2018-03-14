<?php

/**
 * This file is part of the contentful-management.php package.
 *
 * @copyright 2015-2017 Contentful GmbH
 * @license   MIT
 */
declare(strict_types=1);

namespace Contentful\Management\Resource;

use Contentful\Core\Api\DateTimeImmutable;
use Contentful\Management\Proxy\Extension\EntryProxyExtension;
use Contentful\Management\Resource\Behavior\ArchivableTrait;
use Contentful\Management\Resource\Behavior\CreatableInterface;
use Contentful\Management\Resource\Behavior\DeletableTrait;
use Contentful\Management\Resource\Behavior\PublishableTrait;
use Contentful\Management\Resource\Behavior\UpdatableTrait;

/**
 * Entry class.
 *
 * This class represents a resource with type "Entry" in Contentful.
 *
 * @see https://www.contentful.com/developers/docs/references/content-management-api/#/reference/entries
 */
class Entry extends BaseResource implements CreatableInterface
{
    use EntryProxyExtension,
        ArchivableTrait,
        DeletableTrait,
        PublishableTrait,
        UpdatableTrait;

    /**
     * @var array[]
     */
    protected $fields = [];

    /**
     * Entry constructor.
     *
     * @param string $contentTypeId
     */
    public function __construct(string $contentTypeId)
    {
        parent::__construct('Entry', ['contentType' => ['sys' => ['id' => $contentTypeId, 'linkType' => 'ContentType']]]);
    }

    /**
     * Returns an array to be used by "json_encode" to serialize objects of this class.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $fields = [];

        foreach ($this->fields as $fieldName => $fieldData) {
            $fields[$fieldName] = [];

            foreach ($fieldData as $locale => $data) {
                $fields[$fieldName][$locale] = $this->getFormattedData($data);
            }
        }

        return [
            'sys' => $this->sys,
            'fields' => (object) $fields,
        ];
    }

    /**
     * Formats data for JSON encoding.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    private function getFormattedData($data)
    {
        if ($data instanceof DateTimeImmutable) {
            return (string) $data;
        }

        if (\is_array($data)) {
            return \array_map([$this, 'getFormattedData'], $data);
        }

        return $data;
    }

    /**
     * @param string $name
     * @param string $locale
     *
     * @return mixed
     */
    public function getField(string $name, string $locale)
    {
        return $this->fields[$name][$locale] ?? null;
    }

    /**
     * @param string|null $locale
     *
     * @return array
     */
    public function getFields(string $locale = null): array
    {
        if (null === $locale) {
            return $this->fields;
        }

        $fields = [];
        foreach ($this->fields as $name => $field) {
            $fields[$name] = $field[$locale] ?? null;
        }

        return $fields;
    }

    /**
     * @param string $name
     * @param string $locale
     * @param mixed  $value
     *
     * @return static
     */
    public function setField(string $name, string $locale, $value)
    {
        if (!isset($this->fields[$name])) {
            $this->fields[$name] = [];
        }

        $this->fields[$name][$locale] = $value;

        return $this;
    }

    /**
     * Provides simple setX/getX capabilities,
     * without recurring to code generation.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $action = \mb_substr($name, 0, 3);
        if ('get' !== $action && 'set' !== $action) {
            \trigger_error(\sprintf(
                'Call to undefined method %s::%s()',
                static::class,
                $name
            ), E_USER_ERROR);
        }

        $field = $this->extractFieldName($name);

        return 'get' === $action
            ? $this->getField($field, ...$arguments)
            : $this->setField($field, ...$arguments);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function extractFieldName(string $name): string
    {
        return \lcfirst(\mb_substr($name, 3));
    }
}
