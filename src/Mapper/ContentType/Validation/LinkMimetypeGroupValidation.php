<?php

/**
 * This file is part of the contentful/contentful-management package.
 *
 * @copyright 2015-2018 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

namespace Contentful\Management\Mapper\ContentType\Validation;

use Contentful\Management\Mapper\BaseMapper;
use Contentful\Management\Resource\ContentType\Validation\LinkMimetypeGroupValidation as ResourceClass;

/**
 * LinkMimetypeGroupValidation class.
 */
class LinkMimetypeGroupValidation extends BaseMapper
{
    /**
     * {@inheritdoc}
     */
    public function map($resource, array $data): ResourceClass
    {
        return $this->hydrate(ResourceClass::class, [
            'mimeTypeGroups' => $data['linkMimetypeGroup'],
        ]);
    }
}
