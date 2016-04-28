<?php

namespace Skype\Api;

class Conversation extends BaseApi implements ApiInterface
{
    /**
     * Sends an activity message
     *
     * @param $target In format of 8:<username> or 19:<group>
     * @param $content The message
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function activity($target, $content)
    {
        return $this->request('POST', '/v2/conversations/' . $target . '/activities', [
            'json' => [
                'message' => [
                    'content' => $content
                ]
            ]
        ]);
    }
}
