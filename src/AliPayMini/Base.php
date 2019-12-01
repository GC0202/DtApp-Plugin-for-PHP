<?php
/**
 * PHP常用函数
 * (c) Chaim <gc@dtapp.net>
 */

namespace DtApp\AliPayMini;

use DtApp\Tool\Tool;

/**
 * 支付宝小程序
 * Class Base
 * @package DtApp\AliPayMini
 */
class Base extends Client
{
    protected $tool;

    protected $gateway_url = 'https://openapi.alipay.com/gateway.do';

    public function __construct()
    {
        $this->tool = new Tool();
    }

    /**
     * 签名
     * @param string $data 数据
     * @param string $priKey 应用私钥
     * @return string
     */
    protected function sign($data, $priKey)
    {
        $str = $priKey;
        $str = chunk_split($str, 64, "\n");
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n$str-----END RSA PRIVATE KEY-----\n";
        $binary_signature = "";
        openssl_sign($data, $binary_signature, $private_key, OPENSSL_ALGO_SHA256);
        $sign = base64_encode($binary_signature);
        return $sign;
    }

    /**
     * 格式化参数格式化成url参数
     * @param $param
     * @return string
     */
    protected function toUrlParam($param)
    {
        $buff = "";
        foreach ($param as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
}
