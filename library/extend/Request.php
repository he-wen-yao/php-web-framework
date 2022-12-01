<?php

namespace he\extend;


class Request
{
    /**
     * GET参数
     */
    private static $_GET;
    /**
     * POST参数
     */
    private static $_POST;
    /**
     * 不限类型参数
     */
    private static $_PARAM;
    /*********************************** crsf令牌相关 *********************************/
    /**
     * 生成请求令牌
     * @param string $name 表单或url中的令牌名称
     * @param bool $type 生成模式 true 返回表单结构 false 返回url结构
     * @return string
     */
    public static function token(bool $type, string $name = '__token__'): string
    {
        # 生成Token随机令牌
        $str = 'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz123456789';
        $length = mb_strlen($str, 'utf-8');
        $Token = '';
        for ($i = 1; $i <= 32; $i++) {
            $rand = rand(1, ($length - 1));
            $Token .= mb_substr($str, $rand, 1, 'utf-8');
        }
        # 更新csrf_token
        \he\extend\Session::set($name . 'csrf_token', $Token);
        # 生成Token
        if ($type == true) {
            return "<input type='hidden' name='$name' value='$Token'>";
        }
        return $name . '=' . $Token;
    }

    /**
     * 验证请求令牌
     * @param string $name 表单或url中的令牌名称
     * @param bool $type 是否立即清除Token
     * @return bool
     */
    public static function isToken($name = '__token__', $type = false)
    {
        $token = \mimi\extend\Session::get($name . 'csrf_token');
        # 现在检测有没有创建过csrf_token
        if (empty($token)) {
            return false;
        }
        # 收集表单中关于csrf_token的信息
        if (!empty($_POST[$name])) {
            $csrf_token = $_POST[$name];
        } else if (!empty($_GET[$name])) {
            $csrf_token = $_GET[$name];
        } else {
            return false;
        }
        if ($type) {
            # 先清除csrf_token，防止重复提交
            \mimi\extend\Session::delete($name . 'csrf_token');
        }
        # 对比csrf_token是否正确
        if ($token != $csrf_token) {
            return false;
        }
        return true;
    }

    /**
     * 清除请求令牌
     * @param string $name 表单或url中的令牌名称
     */
    public static function deToken($name = '__token__')
    {
        # 先清除csrf_token，防止重复提交
        \mimi\extend\Session::delete($name . 'csrf_token');
    }
    /*********************************** URL相关 *************************************/
    /**
     * 获取当前域名
     * @return string
     */
    public static function domain()
    {
        return self::isHttps(true) . $_SERVER['SERVER_NAME'];
    }

    /**
     * 获取当前URL
     * @param bool $type 是否含域名
     * @return string
     */
    public static function url($type = false)
    {
        if ($type == true) {
            return self::domain() . $_SERVER['REQUEST_URI'];
        }
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取当前URL，不含?符号后的参数
     * @param bool $type 是否含域名
     * @return string
     */
    public static function baseUrl()
    {
        return self::domain() . $_SERVER['PHP_SELF'];
    }
    /*********************************** 其余助手 *************************************/
    /**
     * 获取客户端IP地址(转载至TP5.0.12)
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public static function ip($type = 0, $adv = true)
    {
        $type = $type ? 1 : 0;
        # 作用为缓存查询结果，防止多次调用
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        # IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? [$ip, $long] : ['0.0.0.0', 0];
        return $ip[$type];
    }

    /**
     * 设置获取GET参数
     * @param string $name 多级参数，使用.分号间隔
     * @param mixed $default 默认值
     * @param bool $filter 是否需要过滤参数
     * @return mixed
     */
    public static function get($name, $default = null, $filter = true)
    {
        if (self::isGet() == true) {
            return self::input($_GET, $name, $default, $filter);
        }
        return false;
    }

    /**
     * 设置获取POST参数
     * @param string $name 多级参数，使用.分号间隔
     * @param mixed $default 默认值
     * @param bool $filter 是否需要过滤参数
     * @return mixed
     */
    public static function post($name, $default = null, $filter = true)
    {
        if (self::isPost() == true) {
            return self::input($_POST, $name, $default, $filter);
        }
        return false;
    }

    /**
     * 设置获取PARAM参数
     * @param string $name 多级参数，使用.分号间隔
     * @param mixed $default 默认值
     * @param bool $filter 是否需要过滤参数
     * @return mixed
     */
    public static function param($name, $default = null, $filter = true)
    {
        if (ini_get('always_populate_raw_post_data')) {
            $param = file_get_contents('php://input');
        } else {
            if (count($_POST) > 0) {
                $param = $_POST;
            } else {
                $param = $_GET;
            }
        }
        return self::input($param, $name, $default, $filter);
    }

    /**
     * 获取变量 支持过滤和默认值
     * @param array $param 数据源
     * @param string $name 多级参数，使用.分号间隔
     * @param mixed $default 默认值
     * @param bool $filter 是否需要过滤参数
     * @return mixed
     */
    private static function input($param, $name, $default = null, $filter = true)
    {
        $data = explode('.', $name);
        $res = self::recursionParam($data, $default, $param);
        # 最终还是数组，则直接返回
        if (is_array($res)) {
            return $res;
        }
        # 过滤后返回内容
        return $res;
    }

    /**
     * 递归参数，检测无限参数类型
     * @param array $data 键名
     * @param mixed $default 默认值
     * @param array $array 数据源
     * @return mixed
     */
    private static function recursionParam($data, $default, $array)
    {
        # 递归获取最终的数据结果
        foreach ($data as $k => $v) {
            if (isset($array[$v]) && is_array($array[$v])) {
                unset($data[$k]);
                return self::recursionParam($data, $default, $array[$v]);
            } else if (isset($array[$v])) {
                return self::filter($array[$v]);
            } else {
                return self::filter($default);
            }
        }
        # 如果递归到最后还有数据源，则返回
        return $array;
    }

    /**
     * 循环过滤函数，过滤请求值
     * @param string|int $param 请求值
     * @return mixed
     */
    private static function filter($param)
    {
        $_FILTER = explode(',', \mimi\Config::get('default_filter'));
        # 循环过滤
        foreach ($_FILTER as $v) {
            $param = $v($param);
        }
        # 返回最终的结果
        return $param;
    }
    /*********************************** 助手函数 *************************************/
    /**
     * 检测是否使用https协议
     * @param bool $type 返回类型
     * @return mixed
     */
    public static function isHttps($type = false)
    {
        # 返回字符串
        if ($type) {
            if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
                return 'https://';
            }
            return 'http://';
        } else {
            # 返回布尔值
            if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
                return true;
            }
            return false;
        }
    }

    /**
     * 检测是否AJax请求
     * @return bool
     */
    public static function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
        return false;
    }

    /**
     * 检测是否Post请求
     * @return bool
     */
    public static function isPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) {
            return true;
        }
        return false;
    }

    /**
     * 检测是否Gst请求
     * @return bool
     */
    public static function isGet()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            return true;
        }
        return false;
    }

    /**
     * 检测是否Put请求
     * @return bool
     */
    public static function isPut()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            return true;
        }
        return false;
    }

    /**
     * 检测是否Delete请求
     * @return bool
     */
    public static function isDelete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            return true;
        }
        return false;
    }

    /**
     * 检测是否Patch请求
     * @return bool
     */
    public static function isPatch()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
            return true;
        }
        return false;
    }

    /**
     * 是否为OPTIONS请求
     * @return bool
     */
    public static function isOptions()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            return true;
        }
        return false;
    }

    /**
     * 检测是否使用手机访问
     * @return bool
     */
    public static function isMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }
}