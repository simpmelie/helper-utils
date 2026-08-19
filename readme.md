# Laravel 13 Helper-utils - 工具函数库

[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Laravel 13](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)

## 功能特性

- Active 类：路由/控制器/URI 匹配与 active 状态判断
- JavaScript Transformer：将 PHP 变量转换为 JS 变量
- 自定义验证规则：手机号、中文、字母数字等
- 工具函数：文本过滤、数字转中文大写、字符串隐藏、高亮、URL 安全编解码、加解密
- Tag 标签模型与 HasTags Trait
- 身份证验证，兑奖码核销校验
- SEO Meta：Title、Keywords、Description 设置与调用
- 网页分页：生成分页链接

## 安装要求

- PHP >= 8.2
- Laravel Framework ^13.0

## 安装

```sh
composer require simpmelie/helper-utils
```

#### 发布配置文件

```sh
php artisan vendor:publish --provider="Simpmelie\Utils\UtilsServiceProvider"
```

## SEO Meta（Title、Keywords、Description）

在控制器中设置，在视图中调用：

```php
// 控制器中设置
set_seo_meta('页面标题', '关键字1,关键字2', '页面描述');

// 或只设置其中一项
set_seo_meta(title: '页面标题');
set_seo_meta(keywords: '关键字1,关键字2');
set_seo_meta(description: '页面描述');
```

在 Blade 视图中调用：

```blade
{{-- 单独调用 --}}
<title>{{ seo_title(default: '默认标题', suffix: '站点名称') }}</title>
<meta name="keywords" content="{{ seo_keywords() }}" />
<meta name="description" content="{{ seo_description() }}" />

{{-- 一次性输出所有 SEO 标签 --}}
{!! seo_meta_html(titleSuffix: '站点名称') !!}
```

## 网页分页

生成分页链接 HTML，支持上下页、页码区间与省略号。URL 生成遵循 Laravel 原生路由，无 `.html` 后缀。

### URL 生成方式

1. **闭包（推荐，最 Laravel 化）**：传入接收页码、返回 URL 的闭包，内部可用 `route()`、`url()` 等原生辅助函数。
2. **命名路由**：通过 `options.route` 指定命名路由，自动以 `page` 参数生成 URL（参数名可用 `route_param` 自定义）。
3. **字符串占位符**：使用 `:page` 作为页码占位符。

参数说明：

| 参数          | 说明                                          | 默认值           |
| ------------- | ---------------------------------------------| --------------- |
| total         | 总记录数                                      | -               |
| currentPage   | 当前页码                                      | 1               |
| perPage       | 每页显示数                                    | 15              |
| urlPattern    | URL 模式字符串、闭包，或 null（用 options.route） | `null`          |
| options       | 额外选项（见下表）                            | `[]`            |

`options` 选项：

| 键          | 说明                              | 默认值      |
| ----------- | --------------------------------- | ----------- |
| prev_text   | 上一页文字                        | `上一页`    |
| next_text   | 下一页文字                        | `下一页`    |
| show_count  | 显示的页码数量                    | `5`         |
| class       | 外层 ul 的 class                  | `pagination`|
| route       | 命名路由名（用于生成无后缀 URL）  | `null`      |
| route_param | 命名路由的页码参数名              | `page`      |

### 示例

```php
// 1) 闭包（推荐）：使用 Laravel 原生 route() 生成无后缀 URL
{!! pagination($total, $page, 10, function ($p) {
    return route('news.list', ['page' => $p]);
}) !!}

// 2) 命名路由：直接指定路由名
{!! pagination($total, $page, 10, null, [
    'route' => 'news.list',
]) !!}
// 等价于 route('news.list', ['page' => $page])

// 3) 自定义路由参数名
{!! pagination($total, $page, 10, null, [
    'route' => 'news.list',
    'route_param' => 'p',
]) !!}

// 4) 字符串占位符（保持兼容）
{!! pagination($total, $page, 10, '/news?page=:page') !!}
```
