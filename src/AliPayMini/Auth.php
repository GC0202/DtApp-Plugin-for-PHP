<?php
/**
 * PHP常用函数
 * (c) Chaim <gc@dtapp.net>
 */

namespace DtApp\AliPayMini;

/**
 * 授权
 * Class Auth
 * @package DtApp\AliPayMini
 */
class Auth extends Base
{
    /**
     * 小程序AppId
     * @var string|string
     */
    private $appId;

    /**
     * 小程序AppSecret
     * @var string|string
     */
    private $appSecret;

    /**
     * 仅支持JSON
     * @var string
     */
    private $format = 'json';

    /**
     * 请求使用的编码格式，如utf-8,gbk,gb2312等
     * @var string
     */
    private $post_charset = 'UTF-8';

    /**
     *  商户生成签名字符串所使用的签名算法类型，目前支持RSA2和RSA，推荐使用RSA2
     * @var string
     */
    private $sign_type = 'RSA2';

    /**
     * 调用的接口版本
     * @var string
     */
    private $api_version = '1.0';

    /**
     * 小程序信息
     * Auth constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!empty($config['appId'])) $this->appId = $config['appId'];
        if (!empty($config['appSecret'])) $this->appSecret = $config['appSecret'];
        parent::__construct($config);
    }

    /**
     * 换取授权访问令牌
     * https://docs.open.alipay.com/api_9/alipay.system.oauth.token/
     * Array
     * (
     * [access_token] => 访问令牌。通过该令牌调用需要授权类接口 authbseBd4fbf781a00b43499d84747cfe324X20
     * [alipay_user_id] => 20881072656705515503611382012020
     * [expires_in] => 访问令牌的有效时间，单位是秒。 31536000
     * [re_expires_in] => 刷新令牌的有效时间，单位是秒。  31536000
     * [refresh_token] => 刷新令牌。通过该令牌可以刷新access_token authbseBb3941249aa37467698ece60996338X20
     * [user_id] => 支付宝用户的唯一userId 2088212587578201
     * )
     * @param $code
     * @return bool
     */
    protected function token($code)
    {
        $timestamp = date("Y-m-d H:i:s");
        $params['app_id'] = $this->appId;
        $params['method'] = 'alipay.system.oauth.token';
        $params['format'] = $this->format;
        $params['charset'] = $this->post_charset;
        $params['sign_type'] = $this->sign_type;
        $params['timestamp'] = $timestamp;
        $params['version'] = $this->api_version;
        $params['grant_type'] = "authorization_code";
        $params['code'] = $code;
        ksort($params); //对将要签名的数组排序
        $string = $this->toUrlParam($params);// 将数组转换成字符串
        $params['sign'] = $this->sign($string, $this->appSecret); //将字符串签名
        $params = http_build_query($params);
        $get_url = "$this->gateway_url?$params";
        $http_get = $this->tool->reqGetHttp($get_url, '', true);
        if (isset($http_get['alipay_system_oauth_token_response'])) return $http_get['alipay_system_oauth_token_response'];
        return false;
    }
}
