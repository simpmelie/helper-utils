<?php

/*
 * This file is part of the simpmelie/Helper-utils package.
 *
 * (c) simpmelie <melie647@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simpmelie\Utils;

use Simpmelie\Utils\JavaScript\Transformer;

class Util
{
    /**
     * SEO Meta 标签存储
     *
     * @var array
     */
    protected static $seoMeta = [
        'title' => '',
        'keywords' => '',
        'description' => '',
    ];
    /**
    * 过滤标签，输出没有html的干净的文本
    *
    * @param string $text 文本内容
    * @return string 处理后内容
    */
    public static function textFilter($text)
    {
        $text = html_entity_decode(nl2br($text), ENT_QUOTES, 'UTF-8');

        return trim(addslashes(strip_tags($text, "")));
    }

    /**
     * 验证规则转换对应语言提示
     *
     * @param array $rules
     * @return array
     */
    public static function rulesToMessages(array $rules)
    {
        $messages = [];

        $lenMsg = ['min' => '不能小于','max' => '不能大于','size' => '必须为'];

        foreach ($rules as $field => $rule) {
            $rule = is_array($rule) ? $rule : explode('|', $rule);
            $fieldTitle = trans('validation.attributes.' . $field);

            foreach ($rule as $val) {
                $ruleName = explode(':', $val)[0];
                $key = $field . '.' . $ruleName;
                if( isset($lenMsg[$ruleName])) {
                    $message = [
                        'numeric' => $fieldTitle .$lenMsg[$ruleName].':' . $ruleName,
                        'string' => $fieldTitle. '长度'.$lenMsg[$ruleName].':' . $ruleName . '个字符'
                    ];
                } elseif ($ruleName == 'between') {
                    $message = $fieldTitle . '的(大小,长度等)只能在:min和:max之间.';
                } else {
                    $message = str_replace(':attribute ', $fieldTitle, trans('validation.' . $ruleName));
                }

                $messages[$key] = $message;
            }
        }

        return $messages;
    }
   
    /**
     *
     * 个，十，百，千，万，十万，百万，千万，亿，十亿，百亿，千亿，万亿，十万亿， 百万亿，千万亿，兆；此函数亿乘以亿为兆
     * 以「十」开头，如十五，十万，十亿等。两位数以上，在数字中部出现，则用「一十几」， 如一百一十，一千零一十，一万零一十等
     *「二」和「两」的问题。两亿，两万，两千，两百，都可以，但是20只能是二十， 200用二百也更好。22,2222,2222是「二十二亿两千二百二十二万两千二百二十二」
     *
     * 关于「零」和「〇」的问题，数字中一律用「零」，只有页码、年代等编号中数的空位才能用「〇」。数位中间无论多少个0，都读成一个「零」。2014是「两千零一十四」，
     * 20014 是「二十万零一十四」，201400是「二十万零一千四百」
     *
     * @param  integer $number
     * @param  boolean $isRmb
     * @return string
     * @throws \Exception
     */
    public static function numberToChinese($number, $isRmb = false)
    {
        // 判断正确数字
        if (!preg_match('/^-?\d+(\.\d+)?$/', $number)) {
            throw new \Exception('number_chinese() wrong number', 1);
        }

        list($integer, $decimal) = explode('.', $number . '.0');
        // 检测是否为负数
        $symbol = '';
        if (substr($integer, 0, 1) == '-') {
            $symbol = '负';
            $integer = substr($integer, 1);
        }
        if (preg_match('/^-?\d+$/', $number)) {
            $decimal = null;
        }
        $integer = ltrim($integer, '0');
        // 准备参数
        $numArr  = ['', '一', '二', '三', '四', '五', '六', '七', '八', '九', '.' => '点'];
        $descArr = ['', '十', '百', '千', '万', '十', '百', '千', '亿', '十', '百', '千', '万亿', '十', '百', '千', '兆', '十', '百', '千'];
        if ($isRmb) {
            $number = substr(sprintf("%.5f", $number), 0, -1);
            $numArr  = ['', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖', '.' => '点'];
            $descArr = ['', '拾', '佰', '仟', '万', '拾', '佰', '仟', '亿', '拾', '佰', '仟', '万亿', '拾', '佰', '仟', '兆', '拾', '佰', '仟'];
            $rmbDescArr = ['角', '分', '厘', '毫'];
        }
        // 整数部分拼接
        $integerRes = '';
        $count = strlen($integer);
        if ($count > max(array_keys($descArr))) {
            throw new \Exception('number2chinese() number too large.', 1);
        } else if ($count == 0) {
            $integerRes = '零';
        } else {
            for ($i = 0; $i < $count; $i++) {
                $n = $integer[$i];      // 位上的数
                $j = $count - $i - 1;   // 单位数组 $descArr 的第几位
                // 零零的读法
                $isLing = $i > 1                    // 去除首位
                    && $n !== '0'                   // 本位数字不是零
                    && $integer[$i - 1] === '0';    // 上一位是零
                $cnZero = $isLing ? '零': '';
                $cnNum  = $numArr[$n];
                // 单位读法
                $isEmptyDanwei = ($n == '0' && $j % 4 != 0)     // 是零且一断位上
                    || substr($integer, $i - 3, 4) === '0000';  // 四个连续0
                $descMark = isset($cnDesc) ? $cnDesc : '';
                $cnDesc = $isEmptyDanwei ? '' : $descArr[$j];
                // 第一位是一十
                if ($i == 0 && $cnNum == '一' && $cnDesc == '十') $cnNum = '';
                // 二两的读法
                $isChangeEr = $n > 1 && $cnNum == '二'       // 去除首位
                    && !in_array($cnDesc, ['', '十', '百'])  // 不读两\两十\两百
                    && $descMark !== '十';                   // 不读十两
                if ($isChangeEr ) $cnNum = '两';
                $integerRes .=  $cnZero . $cnNum . $cnDesc;
            }
        }
        // 小数部分拼接
        $decimalRes = '';
        $count = strlen($decimal);
        if ($decimal === null) {
            $decimalRes = $isRmb ? '整' : '';
        } else if ($decimal === '0') {
            $decimalRes = '零';
        } else if ($count > max(array_keys($descArr))) {
            throw new \Exception('number2chinese() number too large.', 1);
        } else {
            for ($i = 0; $i < $count; $i++) {
                if ($isRmb && $i > count($rmbDescArr) - 1) break;
                $n = $decimal[$i];
                $cnZero = $n === '0' ? '零' : '';
                $cnNum  = $numArr[$n];
                $cnDesc = $isRmb ? $rmbDescArr[$i] : '';
                $decimalRes .=  $cnZero . $cnNum . $cnDesc;
            }
        }
        // 拼接结果
        $res = $symbol . ($isRmb ?
            $integerRes . ($decimalRes === '零' ? '元整' : "元$decimalRes"):
            $integerRes . ($decimalRes ==='' ? '' : "点$decimalRes"));
        return $res;
    }

    /**
     * 隐藏部分字符串
     *
     * @param string $str           
     * @param int    $start         开始位置
     * @param int    $length        隐藏字符长度 
     * @param string $replacement   要替换的字符串
     * @return string
     */
    public static function strPartHide($str = '', $start = 1, $length = 3, $replacement = '*' )
    {
        $len = mb_strlen($str,'utf-8');
        if ($len > intval($start+$length)) {
            $str1 = mb_substr($str, 0, $start, 'utf-8');
            $str2 = mb_substr($str,intval($start+$length),NULL,'utf-8');
        } else {
            $str1 = mb_substr($str, 0, 1,'utf-8');
            $str2 = mb_substr($str, $len-1, 1,'utf-8');
            $length = $len - 2;
        }

        for ($i = 0; $i < $length; $i++) {
            $str1 .= $replacement;
        }

        return $str1 . $str2;
    }

    /**
     * 高亮字符 根据指定字符截取前后字符
     *
     * @param string|array $strings
     * @param string       $content
     * @param int          $limit
     * @return string
     */
    public static function strHighlight($strings, $content, $limit = 0)
    {
        $strings = (array)$strings;

        if($limit > 0) {
            $str = $strings[0];
            $content = UtilsService::textFilter($content);

            $index = mb_strpos($content, $str);

            if ($index === false && isset($strings[1]) ) {
                $index = mb_strpos($content, $strings[1]);
            }

            $len = mb_strlen($content);

            if ($index !== false) {
                $startIndex = ($index < ($limit / 2)) ? 0 : ($index - ($limit / 2));
                $limit = (($len - $startIndex) > $limit) ? $limit : $len - $startIndex;
                $content = mb_substr($content, $startIndex, $limit);
            } else {
                $content = mb_substr($content, 0, $limit);
            }

            $content = $len < $limit ? $content : $content . '...';
        }

        $replace = array_map(function ($str) {
            return '<b class="str-highlight">'.$str.'</b>';
        },$strings);

        return str_replace($strings, $replace, $content);
    }

    /**
     * URL安全的字符串编码
     * @param  string  $string
     * @return string
     */
    public static function urlB64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * URL安全的字符串解码：
     * @param  string  $string
     * @return string
     */
    public static function urlB64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * 系统加密方法
     * 
     * @param string  $data 要加密的字符串
     * @param int     $expire 过期时间 单位 秒
     * @param string   $authKey 加密盐
     * @return string
     */
    public static function jyEncrypt($data, $expire = 0, $authKey = null)
    {
        $authKey = !is_null($authKey) ? $authKey : config('zr.data_auth_key', 'E4veMd^&]JD6V:AKTk[|Z=)Hi2+z>rnO;yhU{wl(');

        $key = md5($authKey);
        $data = base64_encode($data);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = '';
        
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }
        
        $str = sprintf('%010d', $expire ? $expire + time() : 0);
        
        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
        }
        return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
    }

    /**
     * 系统解密方法
     * 
     * @param  string  $data 要解密的字符串 （必须是jyEncrypt方法加密的字符串）
     * @param string   $authKey 加密盐
     * @return string
     */
    public static function jyDecrypt($data, $authKey = null)
    {
        $authKey = !is_null($authKey) ? $authKey : config('zr.data_auth_key', 'E4veMd^&]JD6V:AKTk[|Z=)Hi2+z>rnO;yhU{wl(');

        $key = md5($authKey);

        $data = str_replace(array('-', '_'), array('+', '/'), $data);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data = base64_decode($data);
        $expire = substr($data, 0, 10);
        $data = substr($data, 10);
        
        if ($expire > 0 && $expire < time()) {
            return '';
        }
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);
        $char = $str = '';
        
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }
        
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return base64_decode($str);
    }

    /**
     * Undocumented function
     *
     * @param mixed  $var
     * @param string $namespace
     * @return string
     */
    public function javascript($var, $namespace = null)
    {
        $namespace = is_null($namespace) ? config('utils.js_namespace', 'window') : $namespace;

        $transformer = new Transformer(
            new LaravelViewBinder(app('events'), config('utils.bind_js_vars_to_this_view')),
            $namespace
        );

        $transformer->put($var);

        return $transformer;
    }

    /**
     * 设置 SEO Meta 标签 (Title、Keywords、Description)
     *
     * @param string|null $title       页面标题
     * @param string|null $keywords    关键字
     * @param string|null $description 描述
     * @return void
     */
    public static function setSeoMeta($title = null, $keywords = null, $description = null)
    {
        if (!is_null($title)) static::$seoMeta['title'] = $title;
        if (!is_null($keywords)) static::$seoMeta['keywords'] = $keywords;
        if (!is_null($description)) static::$seoMeta['description'] = $description;
    }

    /**
     * 获取页面 Title，可附加站点后缀
     *
     * @param string $default   未设置时的默认标题
     * @param string $suffix     站点后缀（站点名）
     * @param string $separator  标题与后缀的分隔符
     * @return string
     */
    public static function seoTitle($default = '', $suffix = '', $separator = ' - ')
    {
        $title = static::$seoMeta['title'] !== '' ? static::$seoMeta['title'] : $default;

        if ($suffix !== '') {
            $title = $title !== '' ? $title . $separator . $suffix : $suffix;
        }

        return $title;
    }

    /**
     * 获取页面 Keywords
     *
     * @param string $default 未设置时的默认关键字
     * @return string
     */
    public static function seoKeywords($default = '')
    {
        return static::$seoMeta['keywords'] !== '' ? static::$seoMeta['keywords'] : $default;
    }

    /**
     * 获取页面 Description
     *
     * @param string $default 未设置时的默认描述
     * @return string
     */
    public static function seoDescription($default = '')
    {
        return static::$seoMeta['description'] !== '' ? static::$seoMeta['description'] : $default;
    }

    /**
     * 输出 SEO Meta 的 HTML 标签（title、keywords、description）
     *
     * @param string $titleSuffix    标题后缀（站点名）
     * @param string $titleSeparator 标题分隔符
     * @return string
     */
    public static function seoMetaHtml($titleSuffix = '', $titleSeparator = ' - ')
    {
        $html = '';

        $title = static::seoTitle('', $titleSuffix, $titleSeparator);
        if ($title !== '') {
            $html .= '<title>' . e($title) . '</title>' . PHP_EOL;
        }

        $keywords = static::seoKeywords();
        if ($keywords !== '') {
            $html .= '<meta name="keywords" content="' . e($keywords) . '" />' . PHP_EOL;
        }

        $description = static::seoDescription();
        if ($description !== '') {
            $html .= '<meta name="description" content="' . e($description) . '" />' . PHP_EOL;
        }

        return $html;
    }

    /**
     * 生成分页链接 HTML
     *
     * URL 生成方式（遵循 Laravel 原生路由，无 .html 后缀）：
     *   1. $urlPattern 为闭包（推荐）：接收页码，返回 URL 字符串
     *      例：function ($page) { return route('news.list', ['page' => $page]); }
     *   2. $urlPattern 为字符串：使用 :page 占位符，例：'/news?page=:page'
     *   3. options.route 指定命名路由：自动以 page 参数生成 URL
     *
     * @param int                                                    $total        总记录数
     * @param int                                                    $currentPage  当前页码
     * @param int                                                    $perPage      每页显示数
     * @param string|callable|null                                   $urlPattern   URL 模式或闭包
     * @param array                                                  $options      额外选项（prev_text/next_text/show_count/class/route/route_param）
     * @return string
     */
    public static function pagination($total, $currentPage = 1, $perPage = 15, $urlPattern = null, $options = [])
    {
        $totalPages = (int) ceil($total / $perPage);
        if ($totalPages <= 1) {
            return '';
        }

        $currentPage = max(1, min((int) $currentPage, $totalPages));

        $options = array_merge([
            'prev_text'    => '上一页',
            'next_text'    => '下一页',
            'show_count'   => 5,
            'class'        => 'pagination',
            'route'        => null,
            'route_param'  => 'page',
        ], $options);

        // URL 生成器：优先使用闭包，其次命名路由，最后字符串占位符
        $buildUrl = function ($page) use ($urlPattern, $options) {
            if (is_callable($urlPattern)) {
                return call_user_func($urlPattern, $page);
            }

            if (!empty($options['route'])) {
                $params = [$options['route_param'] => $page];
                return route($options['route'], $params);
            }

            $pattern = is_string($urlPattern) ? $urlPattern : '?page=:page';
            return str_replace(':page', $page, $pattern);
        };

        $html = '<ul class="' . e($options['class']) . '">';

        // 上一页
        if ($currentPage > 1) {
            $html .= '<li><a href="' . e($buildUrl($currentPage - 1)) . '">' . $options['prev_text'] . '</a></li>';
        } else {
            $html .= '<li class="disabled"><span>' . $options['prev_text'] . '</span></li>';
        }

        // 计算页码显示区间
        $showCount = (int) $options['show_count'];
        $start = max(1, $currentPage - (int) floor($showCount / 2));
        $end = min($totalPages, $start + $showCount - 1);
        $start = max(1, $end - $showCount + 1);

        // 首页与省略号
        if ($start > 1) {
            $html .= '<li><a href="' . e($buildUrl(1)) . '">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="ellipsis"><span>...</span></li>';
            }
        }

        // 页码
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="active"><span>' . $i . '</span></li>';
            } else {
                $html .= '<li><a href="' . e($buildUrl($i)) . '">' . $i . '</a></li>';
            }
        }

        // 末页与省略号
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= '<li class="ellipsis"><span>...</span></li>';
            }
            $html .= '<li><a href="' . e($buildUrl($totalPages)) . '">' . $totalPages . '</a></li>';
        }

        // 下一页
        if ($currentPage < $totalPages) {
            $html .= '<li><a href="' . e($buildUrl($currentPage + 1)) . '">' . $options['next_text'] . '</a></li>';
        } else {
            $html .= '<li class="disabled"><span>' . $options['next_text'] . '</span></li>';
        }

        $html .= '</ul>';

        return $html;
    }

}
