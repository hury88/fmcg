<?php

namespace Recharge\Pushbox\Adapter;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Tinpont\Pushbox\Adapter;
use Tinpont\Pushbox\Message;

/**
 * UMS adapter.
 *
 * @package Recharge\Pushbox\Adapter
 */
class Top extends Adapter
{
    /**
     * @var \GuzzleHttp\Client $client
     */
    protected $client;

    /**
     * @var string
     */
    protected $baseUri = 'http://gw.api.taobao.com/router/rest';

    /**
     * 默认参数
     *
     * @var array
     */
    protected $defaultParams = [
        'v' => '2.0',
        'format' => 'json',
        'sign_method' => 'md5',
        'method' => 'taobao.open.sms.sendmsg',
    ];

    /**
     * @var int
     */
    protected $signatureId;

    /**
     * 模板列表
     *
     * @var array
     */
    protected $templates;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->defaultParams['app_key'] = $this->getOption('app_key');
        $this->signatureId = $this->getOption('signature_id');
        $this->templates = $this->getOption('templates');
    }

    /**
     * 验证手机号码是否正确
     *
     * @param string $token
     * @return bool
     */
    protected function isValidToken($token)
    {
        return (bool)preg_match('/^1\d{10}$/i', $token);
    }

    /**
     * 短信消息发送
     *
     * @param string|\Tinpont\Pushbox\Message $message
     * @return Adapter
     */
    public function push($message)
    {
        $this->success = $this->fails = [];

        $message = $this->getMessage($message);
        $template = $message->getText();
        $messageOptions = $message->getOptions();

        foreach ($this->getDevices() as $device) {
            $context = array_merge($messageOptions, $device->getOptions());

            $token = $device->getToken();
            $result = $this->send($template, $token, $context);

            if ($result['code'] === 1) {
                $this->success[] = $token;
            } else {
                $this->fails[] = $token;
            }
        }

        return $this;
    }

    /**
     * 发送充值成功短信
     *
     * @param string|array $text
     * @return \Tinpont\Pushbox\Adapter
     */
    public function pushRecharged($text)
    {
        is_array($text) && $text = head($text);

        return $this->push(new Message('recharged', ['item' => $text]));
    }

    /**
     * 发送短信
     *
     * @param string $template
     * @param string $mobile
     * @param array $context
     * @return array
     */
    protected function send($template, $mobile, array $context)
    {
        $templateId = array_get($this->templates, $template);
        if (empty($templateId)) {
            throw new \LogicException('Template id not exists for ' . $template);
        }

        $params = [
            'signature_id' => $this->signatureId,
            'template_id' => $templateId,
            'mobile' => $mobile,
            'context' => $context,
        ];

        return $this->handleResponse($this->post(['send_message_request' => json_encode($params)]));
    }

    /**
     * post提交请求
     *
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function post(array $params = [])
    {
        $data = array_merge($params, $this->defaultParams);
        $data['timestamp'] = date('Y-m-d H:i:s');
        $data['sign'] = $this->getSign($data);

        return $this->getClient()->post(null, [
            'form_params' => $data,
        ]);
    }

    /**
     * 处理结果
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    protected function handleResponse(ResponseInterface $response)
    {
        $json = (array)json_decode($response->getBody()->getContents(), true);
        $result = $json ? last($json) : [];
        $result && $result = $result['result'];

        if (!isset($result['code']) || $result['code'] !== 1) {
            info('Top sms error', $json);
        }

        return $result;
    }

    /**
     * 淘宝签名计算
     *
     * @param array $params
     * @return string
     */
    protected function getSign(array $params)
    {
        $appSecret = $this->getOption('app_secret');
        $string = $appSecret;

        ksort($params);
        foreach ($params as $key => $value) {
            if ($key != 'sign') {
                $string .= ($key . $value);
            }
        }

        return strtoupper(md5($string . $appSecret));
    }

    /**
     * Get the current Client instance.
     *
     * @return \GuzzleHttp\Client $client
     */
    protected function getClient()
    {
        if ($this->client) {
            return $this->client;
        }

        return $this->client = new Client(['base_uri' => $this->baseUri]);
    }
}
