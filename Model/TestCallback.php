<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model;

use Inriver\Adapter\Api\Data\ValidationMessageInterface;
use Inriver\Adapter\Api\TestCallbackInterface;
use Inriver\Adapter\Helper\InriverCallback;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Webapi\Rest\Request;

class TestCallback implements TestCallbackInterface
{
    /** @var \Inriver\Adapter\Helper\InriverCallback */
    private $inriverCallback;

    /** @var \Magento\Framework\Stdlib\DateTime\DateTime */
    private $date;

    /** @var Request */
    private $request;

    /** @var \Inriver\Adapter\Api\Data\ValidationMessageInterface */
    private $validationMessage;

    /**
     * Test constructor.
     * @param \Inriver\Adapter\Helper\InriverCallback $inriverCallback
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Inriver\Adapter\Api\Data\ValidationMessageInterface $validationMessage
     */
    public function __construct(
        InriverCallback $inriverCallback,
        DateTime $date,
        Request $request,
        ValidationMessageInterface $validationMessage
    ) {
        $this->inriverCallback = $inriverCallback;
        $this->date = $date;
        $this->request = $request;
        $this->validationMessage = $validationMessage;
    }

    /**
     * Test Inriver callback information
     *
     * @param string $validationToken
     * @return \Inriver\Adapter\Api\Data\ValidationMessageInterface
     */
    public function get(string $validationToken): ValidationMessageInterface
    {
        $callbackUrl = $this->request->getHeader(InriverCallback::CALLBACK_HEADER);
        $response = $this->inriverCallback->sendResponse(
            $this->inriverCallback->getApiKey(),
            $callbackUrl,
            $validationToken
        );
        if ($response->getStatusCode() === 200) {
            $this->validationMessage->setIsValid(true);
            $this->validationMessage->setMessage('Success');
        } else {
            $this->validationMessage->setIsValid(false);
            $this->validationMessage->setMessage(
                'An error occured while doing the inRiver Callback: ' . $response->getReasonPhrase()
            );
        }

        return $this->validationMessage;
    }
}
