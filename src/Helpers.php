<?php

/*
 * This file is part of the simpmelie/Helper-utils package.
 *
 * (c) simpmelie <melie647@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Simpmelie\Utils\Util;
use Simpmelie\Utils\Active;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if (!function_exists('active_class')) {
    /**
     * Get the active class if the condition is not falsy
     *
     * @param        $condition
     * @param string $activeClass
     * @param string $inactiveClass
     *
     * @return string
     */
    function active_class($condition, $activeClass = 'active', $inactiveClass = '')
    {
        return app('active')->getClassIf($condition, $activeClass, $inactiveClass);
    }
}
if (!function_exists('if_uri')) {
    /**
     * Check if the URI of the current request matches one of the specific URIs
     *
     * @param array|string $uris
     *
     * @return bool
     */
    function if_uri($uris)
    {
        return app('active')->checkUri($uris);
    }
}
if (!function_exists('if_uri_pattern')) {
    /**
     * Check if the current URI matches one of specific patterns (using `str_is`)
     *
     * @param array|string $patterns
     *
     * @return bool
     */
    function if_uri_pattern($patterns)
    {
        return app('active')->checkUriPattern($patterns);
    }
}
if (!function_exists('if_query')) {
    /**
     * Check if one of the following condition is true:
     * + the value of $value is `false` and the current querystring contain the key $key
     * + the value of $value is not `false` and the current value of the $key key in the querystring equals to $value
     * + the value of $value is not `false` and the current value of the $key key in the querystring is an array that
     * contains the $value
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool
     */
    function if_query($key, $value)
    {
        return app('active')->checkQuery($key, $value);
    }
}
if (!function_exists('if_route')) {
    /**
     * Check if the name of the current route matches one of specific values
     *
     * @param array|string $routeNames
     *
     * @return bool
     */
    function if_route($routeNames)
    {
        return app('active')->checkRoute($routeNames);
    }
}
if (!function_exists('if_route_pattern')) {
    /**
     * Check the current route name with one or some patterns
     *
     * @param array|string $patterns
     *
     * @return bool
     */
    function if_route_pattern($patterns)
    {
        return app('active')->checkRoutePattern($patterns);
    }
}
if (!function_exists('if_route_param')) {
    /**
     * Check if the parameter of the current route has the correct value
     *
     * @param $param
     * @param $value
     *
     * @return bool
     */
    function if_route_param($param, $value)
    {
        return app('active')->checkRouteParam($param, $value);
    }
}
if (!function_exists('if_action')) {
    /**
     * Return 'active' class if current route action match one of provided action names
     *
     * @param array|string $actions
     *
     * @return bool
     */
    function if_action($actions)
    {
        return app('active')->checkAction($actions);
    }
}
if (!function_exists('if_controller')) {
    /**
     * Check if the current controller class matches one of specific values
     *
     * @param array|string $controllers
     *
     * @return bool
     */
    function if_controller($controllers)
    {
        return app('active')->checkController($controllers);
    }
}
if (!function_exists('current_controller')) {
    /**
     * Get the current controller class
     *
     * @return string
     */
    function current_controller()
    {
        return app('active')->getController();
    }
}
if (!function_exists('current_method')) {
    /**
     * Get the current controller method
     *
     * @return string
     */
    function current_method()
    {
        return app('active')->getMethod();
    }
}
if (!function_exists('current_action')) {
    /**
     * Get the current action string
     *
     * @return string
     */
    function current_action()
    {
        return app('active')->getAction();
    }
}

if (!function_exists('text_filter')) {
    /**
     * 过滤标签，输出没有html的干净的文本
     *
     * @return string
     */
    function text_filter($str)
    {
        return Util::textFilter($str);
    }
}

if (!function_exists('rules_to_messages')) {
    /**
     * 验证规则转换对应语言提示
     *
     * @return string
     */
    function rules_messages($rules)
    {
        return Util::rulesToMessages($rules);
    }
}

if (!function_exists('number_chinese')) {
    /**
     * 数字转中文大写
     *
     * @return string
     */
    function number_chinese($number, $isRmb = false)
    {
        return Util::numberToChinese($number, $isRmb = false);
    }
}

if (!function_exists('str_part_hide')) {
    /**
     * 隐藏部分字符串
     *
     * @param string $str           
     * @param int    $start         开始位置
     * @param int    $length        隐藏字符长度 
     * @param string $replacement   要替换的字符串
     * @return string
     */
    function str_part_hide($str = '', $start = 1, $length = 3, $replacement = '*')
    {
        return Util::strPartHide($str, $start, $length, $replacement);
    }
}

if (!function_exists('str_highlight')) {
    /**
     * 高亮字符 根据指定字符截取前后字符
     *
     * @param string|array $strings
     * @param string       $content
     * @param int          $limit
     * @return string
     */
    function str_highlight($strings, $content, $limit = 0)
    {
        return Util::strHighlight($strings, $content, $limit);
    }
}

if (!function_exists('url_b64encode')) {
    /**
     * URL安全的字符串编码
     * 
     * @param  string  $string
     * @return string
     */
    function url_b64encode($string)
    {
        return Util::urlB64encode($string);
    }
}

if (!function_exists('url_b64decode')) {
    /**
     * URL安全的字符串解码：
     * 
     * @param  string  $string
     * @return string
     */
    function url_b64decode($string)
    {
        return Util::urlB64decode($string);
    }
}

if (!function_exists('jy_encrypt')) {
    /**
     * 系统加密方法
     * 
     * @param string   $data 要加密的字符串
     * @param int      $expire 过期时间 单位 秒
     * @param string   $authKey 加密盐
     * @return string
     */
    function jy_encrypt($data, $expire = 0, $authKey = null)
    {
        return Util::jyEncrypt($data, $expire, $authKey);
    }
}

if (!function_exists('jy_decrypt')) {
    /**
     * 系统解密方法
     *
     * @param  string  $data 要解密的字符串 （必须是jyEncrypt方法加密的字符串）
     * @param  string  $authKey 加密盐
     * @return string
     */
    function jy_decrypt($string)
    {
        return Util::jyDecrypt($string);
    }
}

if (!function_exists('set_seo_meta')) {
    /**
     * 设置 SEO Meta 标签 (Title、Keywords、Description)
     *
     * @param string|null $title       页面标题
     * @param string|null $keywords    关键字
     * @param string|null $description 描述
     * @return void
     */
    function set_seo_meta($title = null, $keywords = null, $description = null)
    {
        Util::setSeoMeta($title, $keywords, $description);
    }
}

if (!function_exists('seo_title')) {
    /**
     * 获取页面 Title，可附加站点后缀
     *
     * @param string $default   未设置时的默认标题
     * @param string $suffix     站点后缀（站点名）
     * @param string $separator  标题与后缀的分隔符
     * @return string
     */
    function seo_title($default = '', $suffix = '', $separator = ' - ')
    {
        return Util::seoTitle($default, $suffix, $separator);
    }
}

if (!function_exists('seo_keywords')) {
    /**
     * 获取页面 Keywords
     *
     * @param string $default 未设置时的默认关键字
     * @return string
     */
    function seo_keywords($default = '')
    {
        return Util::seoKeywords($default);
    }
}

if (!function_exists('seo_description')) {
    /**
     * 获取页面 Description
     *
     * @param string $default 未设置时的默认描述
     * @return string
     */
    function seo_description($default = '')
    {
        return Util::seoDescription($default);
    }
}

if (!function_exists('seo_meta_html')) {
    /**
     * 输出 SEO Meta 的 HTML 标签（title、keywords、description）
     *
     * @param string $titleSuffix    标题后缀（站点名）
     * @param string $titleSeparator 标题分隔符
     * @return string
     */
    function seo_meta_html($titleSuffix = '', $titleSeparator = ' - ')
    {
        return Util::seoMetaHtml($titleSuffix, $titleSeparator);
    }
}

if (!function_exists('pagination')) {
    /**
     * 生成分页链接 HTML
     *
     * URL 生成遵循 Laravel 原生路由（无 .html 后缀），支持三种方式：
     *   1. $urlPattern 为闭包（推荐）：接收页码，返回 URL 字符串
     *   2. $urlPattern 为字符串：使用 :page 占位符
     *   3. options.route 指定命名路由：自动以 page 参数生成 URL
     *
     * @param int                  $total        总记录数
     * @param int                  $currentPage  当前页码
     * @param int                  $perPage      每页显示数
     * @param string|callable|null $urlPattern   URL 模式或闭包
     * @param array                $options      额外选项（prev_text/next_text/show_count/class/route/route_param）
     * @return string
     */
    function pagination($total, $currentPage = 1, $perPage = 15, $urlPattern = null, $options = [])
    {
        return Util::pagination($total, $currentPage, $perPage, $urlPattern, $options);
    }
}
