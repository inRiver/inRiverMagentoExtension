<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api;

/**
 * Interface PublisherInterface
 */
interface PublisherInterface
{
    /**
     * Get the bulk description
     *
     * @return string
     */
    public function getBulkDescription(): string;

    /**
     * Get the topic
     *
     * @return string
     */
    public function getTopic(): string;
}
