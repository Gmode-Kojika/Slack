<?php
/**
 * SlackAPIクラス
 *
 * API URL
 *  https://api.slack.com/methods
 *
 * 対応状況
 *  chat
 *   -> chat.postMessage
 */

final class SlackAPI
{
    const API_URL = 'https://slack.com/api/';

    static $instance = array();
    public $api_token;

    static function getInstance($api_token)
    {
        if (!isset(self::$instance[$api_token])) {
            self::$instance[$api_token] = new self($api_token);
        }

        return self::$instance[$api_token];
    }

    public function __construct($api_token)
    {
        $this->api_token = $api_token;
    }

    /**
     * send
     */
    public function sendGet($add_url, $data)
    {
        $url = self::API_URL . $add_url . "?token=" . $this->api_token;

        foreach ($data as $key => $value) {
            if ($value === NULL) {
                continue;
            }
            $url .= '&' . urlencode($key) . '=' . urlencode($value);
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, TRUE);                     // データを送信するのにPOSTを使用
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);           // 返り値を 文字列で取得
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json;charset=\"utf-8\"") );

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * chatPostMessage
     *  指定した部屋にメッセージを送信します
     *  パラメータ指定方法はサイトを確認
     *  https://api.slack.com/methods/chat.postMessage
     *
     *  @param $channel : チャンネル
     *  @param $mix : 送信メッセージ or パラメータ配列
     */
    public function chatPostMessage($channel, $mix)
    {
        // 送信データの設定
        if (is_array($mix)) {
            $data = $mix;
        } else {
            $data = array(
                'text'    => $mix,
                'as_user' => true,
            );
        }

        $data['channel'] = $channel;
        $add_url = 'chat.postMessage';
        return $this->sendGet($add_url, $data);
    }

    /**
     * chatBotMessage
     *  指定した部屋にBotとしてメッセージを送信します
     *  パラメータ指定方法はサイトを確認
     *  https://api.slack.com/methods/chat.postMessage
     *
     *  @param $channel : チャンネル
     *  @param $message : 送信メッセージ
     */
    public function chatBotMessage($channel, $mix, $username = 'bot', $icon = NULL)
    {
        // 送信データの設定
        if (is_array($mix)) {
            $data = $mix;
        } else {
            $data = array(
                'text'     => $mix,
                'username' => $username,
            );

            if ($icon !== NULL) {
                if (substr($icon, 0, 1) == ':') {
                    $data['icon_emoji'] = $icon;
                } else {
                    $data['icon_url'] = $icon;
                }
            }
        }

        $data['as_user'] = false;
        return $this->chatPostMessage($channel, $data);
    }
}

