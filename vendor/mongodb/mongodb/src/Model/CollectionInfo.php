<?php
/*
 * Copyright 2015-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB\Model;

use ArrayAccess;
use MongoDB\Exception\BadMethodCallException;

use function array_key_exists;

/**
 * Collection information model class.
 *
 * This class models the collection information returned by the listCollections
 * command or, for legacy servers, queries on the "system.namespaces"
 * collection. It provides methods to access options for the collection.
 *
 * @see \MongoDB\Database::listCollections()
 * @see https://github.com/mongodb/specifications/blob/master/source/enumerate-collections.rst
 * @template-implements ArrayAccess<string, mixed>
 */
class CollectionInfo implements ArrayAccess
{
    /** @param array $info Collection info */
    public function __construct(private array $info)
    {
    }

    /**
     * Return the collection info as an array.
     *
     * @see https://php.net/oop5.magic#language.oop5.magic.debuginfo
     */
    public function __debugInfo(): array
    {
        return $this->info;
    }

    /**
     * Return the maximum number of documents to keep in the capped collection.
     *
     * @deprecated 1.0 Deprecated in favor of using getOptions
     */
    public function getCappedMax(): ?int
    {
        /* The MongoDB server might return this number as an integer or float */
        return isset($this->info['options']['max']) ? (integer) $this->info['options']['max'] : null;
    }

    /**
     * Return the maximum size (in bytes) of the capped collection.
     *
     * @deprecated 1.0 Deprecated in favor of using getOptions
     */
    public function getCappedSize(): ?int
    {
        /* The MongoDB server might return this number as an integer or float */
        return isset($this->info['options']['size']) ? (integer) $this->info['options']['size'] : null;
    }

    /**
     * Return information about the _id index for the collection.
     */
    public function getIdIndex(): array
    {
        return (array) ($this->info['idIndex'] ?? []);
    }

    /**
     * Return the "info" property of the server response.
     *
     * @see https://mongodb.com/docs/manual/reference/command/listCollections/#output
     */
    public function getInfo(): array
    {
        return (array) ($this->info['info'] ?? []);
    }

    /**
     * Return the collection name.
     *
     * @see https://mongodb.com/docs/manual/reference/command/listCollections/#output
     */
    public function getName(): string
    {
        return (string) $this->info['name'];
    }

    /**
     * Return the collection options.
     *
     * @see https://mongodb.com/docs/manual/reference/command/listCollections/#output
     */
    public function getOptions(): array
    {
        return (array) ($this->info['options'] ?? []);
    }

    /**
     * Return the collection type.
     *
     * @see https://mongodb.com/docs/manual/reference/command/listCollections/#output
     */
    public function getType(): string
    {
        return (string) $this->info['type'];
    }

    /**
     * Return whether the collection is a capped collection.
     *
     * @deprecated 1.0 Deprecated in favor of using getOptions
     */
    public function isCapped(): bool
    {
        return ! empty($this->info['options']['capped']);
    }

    /**
     * Check whether a field exists in the collection information.
     *
     * @see https://php.net/arrayaccess.offsetexists
     * @psalm-param array-key $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->info);
    }

    /**
     * Return the field's value from the collection information.
     *
     * @see https://php.net/arrayaccess.offsetget
     * @psalm-param array-key $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->info[$offset];
    }

    /**
     * Not supported.
     *
     * @see https://php.net/arrayaccess.offsetset
     * @throws BadMethodCallException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw BadMethodCallException::classIsImmutable(self::class);
    }

    /**
     * Not supported.
     *
     * @see https://php.net/arrayaccess.offsetunset
     * @throws BadMethodCallException
     */
    public function offsetUnset(mixed $offset): void
    {
        throw BadMethodCallException::classIsImmutable(self::class);
    }
}
