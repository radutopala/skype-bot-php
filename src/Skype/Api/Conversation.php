<?php

namespace Skype\Api;

class Conversation extends BaseApi implements ApiInterface
{
    /**
     * Sends an activity message
     *
     * @param string $target In format of 8:<username> or 19:<group>
     * @param string $text The message
     * @param array $suggestedActions
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function activity($target, $text, $suggestedActions = [])
    {
        $json = [
            'type' => 'message/text',
            'text' => $text,
        ];

        if (!empty($suggestedActions)) {
            $json['suggestedActions']['actions'] = $suggestedActions;
        }

        return $this->request('POST', '/v3/conversations/' . $target . '/activities', [
            'json' => $json
        ]);
    }
}
