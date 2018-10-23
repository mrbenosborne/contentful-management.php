<?php

/**
 * This file is part of the contentful/contentful-management package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Management\SystemProperties;

class User extends BaseSystemProperties
{
    use Component\CreatedAtTrait,
        Component\UpdatedAtTrait,
        Component\VersionedTrait;

    /**
     * User constructor.
     *
     * @param array $sys
     */
    public function __construct(array $sys)
    {
        $this->init('User', $sys['id'] ?? '');

        $this->initCreatedAt($sys);
        $this->initUpdatedAt($sys);
        $this->initVersioned($sys);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return \array_merge(
            parent::jsonSerialize(),
            $this->jsonSerializeCreatedAt(),
            $this->jsonSerializeUpdatedAt(),
            $this->jsonSerializeVersioned()
        );
    }
}
