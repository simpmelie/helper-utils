<?php

/*
 * This file is part of the simpmelie/Helper-utils package.
 *
 * (c) simpmelie <melie647@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Simpmelie\Utils\Validation;

/**
 * 兑奖码核销校验
 *
 * 用于兑奖码/核销码的格式校验、有效期校验、状态校验、次数校验
 *
 * @example
 * // 校验兑换码格式
 * Redemption::validateCode('ABCD-1234-EFGH');
 *
 * // 校验核销码格式
 * Redemption::validateVerifyCode('XK20260101235');
 *
 * // 校验有效期
 * Redemption::validateExpiry('2026-01-01', '2026-12-31');
 *
 * // 完整核销校验
 * $redemption = new Redemption([
 *     'code' => 'ABCD-1234-EFGH',
 *     'verify_code' => 'XK20260101235',
 *     'start_at' => '2026-01-01',
 *     'end_at' => '2026-12-31',
 *     'max_times' => 1,
 *     'used_times' => 0,
 *     'status' => Redemption::STATUS_PENDING,
 * ]);
 * $redemption->verify();
 */
class Redemption
{
    /** 核销状态：待核销 */
    public const STATUS_PENDING   = 'pending';

    /** 核销状态：已核销 */
    public const STATUS_VERIFIED = 'verified';

    /** 核销状态：已过期 */
    public const STATUS_EXPIRED   = 'expired';

    /** 核销状态：已作废 */
    public const STATUS_REVOKED   = 'revoked';

    /** 兑换码格式：大写字母+数字，以 - 分段 */
    public const CODE_PATTERN = '/^[A-Z0-9]{4}(-[A-Z0-9]{4}){1,3}$/';

    /** 核销码格式：XK + 日期 + 序号 */
    public const VERIFY_CODE_PATTERN = '/^XK\d{11}$/';

    /**
     * 兑换码
     *
     * @var string|null
     */
    protected ?string $code = null;

    /**
     * 核销码
     *
     * @var string|null
     */
    protected ?string $verifyCode = null;

    /**
     * 有效开始时间
     *
     * @var string|null
     */
    protected ?string $startAt = null;

    /**
     * 有效结束时间
     *
     * @var string|null
     */
    protected ?string $endAt = null;

    /**
     * 最大核销次数
     *
     * @var int
     */
    protected int $maxTimes = 1;

    /**
     * 已核销次数
     *
     * @var int
     */
    protected int $usedTimes = 0;

    /**
     * 核销状态
     *
     * @var string
     */
    protected string $status = self::STATUS_PENDING;

    /**
     * 校验错误信息
     *
     * @var array<string>
     */
    protected array $errors = [];

    /**
     * @param array $data 初始化数据
     */
    public function __construct(array $data = [])
    {
        foreach (['code', 'verifyCode', 'verify_code', 'startAt', 'start_at', 'endAt', 'end_at', 'maxTimes', 'max_times', 'usedTimes', 'used_times', 'status'] as $key) {
            if (isset($data[$key])) {
                $prop = str_replace('_', '', ucwords($key, '_'));
                $prop = lcfirst($prop);
                if (property_exists($this, $prop)) {
                    $this->{$prop} = $data[$key];
                }
            }
        }
    }

    /**
     * 校验兑换码格式
     *
     * @param string|null $code
     * @return bool
     */
    public static function validateCode(?string $code): bool
    {
        if (empty($code)) {
            return false;
        }

        return (bool) preg_match(self::CODE_PATTERN, $code);
    }

    /**
     * 校验核销码格式
     *
     * @param string|null $code
     * @return bool
     */
    public static function validateVerifyCode(?string $code): bool
    {
        if (empty($code)) {
            return false;
        }

        return (bool) preg_match(self::VERIFY_CODE_PATTERN, $code);
    }

    /**
     * 校验有效期
     *
     * @param string $startAt 开始日期 Y-m-d
     * @param string $endAt   结束日期 Y-m-d
     * @return bool
     */
    public static function validateExpiry(string $startAt, string $endAt): bool
    {
        $now   = date('Y-m-d');
        $start = date('Y-m-d', strtotime($startAt));
        $end   = date('Y-m-d', strtotime($endAt));

        if ($start === '1970-01-01' || $end === '1970-01-01') {
            return false;
        }

        return $now >= $start && $now <= $end;
    }

    /**
     * 校验核销次数
     *
     * @param int $usedTimes 已核销次数
     * @param int $maxTimes   最大核销次数
     * @return bool
     */
    public static function validateTimes(int $usedTimes, int $maxTimes): bool
    {
        return $maxTimes > 0 && $usedTimes < $maxTimes;
    }

    /**
     * 校验核销状态
     *
     * @param string $status
     * @return bool
     */
    public static function validateStatus(string $status): bool
    {
        return in_array($status, [
            self::STATUS_PENDING,
            self::STATUS_VERIFIED,
            self::STATUS_EXPIRED,
            self::STATUS_REVOKED,
        ], true);
    }

    /**
     * 判断是否可核销
     *
     * @return bool
     */
    public function canVerify(): bool
    {
        $this->errors = [];

        // 校验兑换码格式
        if (!self::validateCode($this->code)) {
            $this->errors[] = '兑换码格式不正确';
        }

        // 校验核销码格式
        if (!self::validateVerifyCode($this->verifyCode)) {
            $this->errors[] = '核销码格式不正确';
        }

        // 校验有效期
        if ($this->startAt && $this->endAt) {
            if (!self::validateExpiry($this->startAt, $this->endAt)) {
                $this->errors[] = '不在有效期内';
            }
        }

        // 校验核销次数
        if (!self::validateTimes($this->usedTimes, $this->maxTimes)) {
            $this->errors[] = '核销次数已用完';
        }

        // 校验状态
        if ($this->status !== self::STATUS_PENDING) {
            $this->errors[] = "当前状态为[{$this->status}]，不可核销";
        }

        return empty($this->errors);
    }

    /**
     * 执行核销
     *
     * @return bool
     */
    public function verify(): bool
    {
        if (!$this->canVerify()) {
            return false;
        }

        $this->usedTimes++;
        $this->status = self::STATUS_VERIFIED;

        return true;
    }

    /**
     * 获取校验错误信息
     *
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * 生成核销码
     *
     * @return string
     */
    public static function generateVerifyCode(): string
    {
        $date = date('Ymd');
        $seq  = str_pad((string) mt_rand(0, 999), 3, '0', STR_PAD_LEFT);

        return 'XK' . $date . $seq;
    }

    /**
     * 生成兑换码
     *
     * @param int $segments 段数 (2-4)
     * @return string
     */
    public static function generateCode(int $segments = 2): string
    {
        $segments = max(2, min(4, $segments));
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $parts = [];

        for ($i = 0; $i < $segments; $i++) {
            $part = '';
            for ($j = 0; $j < 4; $j++) {
                $part .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $parts[] = $part;
        }

        return implode('-', $parts);
    }
}
