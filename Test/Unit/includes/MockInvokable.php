<?php
/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */
declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\includes;
/**
 * Class MockInvokable.
 *
 * This class is using to mock a class that has __invoke method.
 */
class MockInvokable
{
    public function __invoke()
    {
    }
}
