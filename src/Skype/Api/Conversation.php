<?php

namespace Skype\Api;

class Conversation extends BaseApi implements ApiInterface
{
    /**
     * Sends an activity message
     *
     * @param string $target In format of 8:<username> or 19:<group>
     * @param string $content The message
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function activity($target, $content)
    {
        return $this->request('POST', '/v3/conversations/' . $target . '/activities', [
            'json' => [
                "type" => "message/text",
                "text" => $content
            ]
        ]);
    }
}
