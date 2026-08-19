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
- 身份证验证

## 安装要求

- PHP >= 8.2
- Laravel Framework ^13.0

## 安装

```sh
composer require simpmelie/Helper-utils
```

#### 发布配置文件

```sh
php artisan vendor:publish --provider="Simpmelie\Utils\UtilsServiceProvider"
```

## 版权信息

本软件基于 MIT 许可证发布，详见 [LICENSE](LICENSE) 文件。
