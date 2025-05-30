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

namespace MongoDB;

use MongoDB\Driver\Exception\LogicException;
use MongoDB\Driver\WriteResult;

/**
 * Result class for a single-document insert operation.
 */
class InsertOneResult
{
    public function __construct(private WriteResult $writeResult, private mixed $insertedId)
    {
    }

    /**
     * Return the number of documents that were inserted.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see InsertOneResult::isAcknowledged()
     * @throws LogicException if the write result is unacknowledged
     */
    public function getInsertedCount(): int
    {
        return $this->writeResult->getInsertedCount();
    }

    /**
     * Return the inserted document's ID.
     *
     * If the document had an ID prior to inserting (i.e. the driver did not
     * need to generate an ID), this will contain its "_id". Any
     * driver-generated ID will be a MongoDB\BSON\ObjectId instance.
     */
    public function getInsertedId(): mixed
    {
        return $this->insertedId;
    }

    /**
     * Return whether this insert was acknowledged by the server.
     *
     * If the insert was not acknowledged, other fields from the WriteResult
     * (e.g. insertedCount) will be undefined.
     *
     * If the insert was not acknowledged, other fields from the WriteResult
     * (e.g. insertedCount) will be undefined and their getter methods should
     * not be invoked.
     */
    public function isAcknowledged(): bool
    {
        return $this->writeResult->isAcknowledged();
    }
}
