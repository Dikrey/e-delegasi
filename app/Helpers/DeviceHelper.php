<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class DeviceHelper
{
    /**
     * Parse the User-Agent string into device, browser, and platform.
     *
     * @param string|null $userAgent
     * @return array{device: string, browser: string, platform: string}
     */
    public static function detect(?string $userAgent): array
    {
        $userAgent = strtolower($userAgent ?? '');

        return [
            'device' => static::device($userAgent),
            'browser' => static::browser($userAgent),
            'platform' => static::platform($userAgent),
        ];
    }

    /**
     * Best effort geolocation for an IP address.
     *
     * @param string|null $ip
     * @return string
     */
    public static function location(?string $ip): string
    {
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return __('device.location_local');
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return __('device.location_private');
        }

        try {
            $response = Http::timeout(3)
                ->acceptJson()
                ->get('http://ip-api.com/json/' . $ip . '?fields=status,country,regionName,city');

            if ($response->successful() && ($response->json('status') ?? null) === 'success') {
                $city = $response->json('city');
                $region = $response->json('regionName');
                $country = $response->json('country');

                return trim(implode(', ', array_filter([$city, $region, $country]))) ?: __('device.location_unknown');
            }
        } catch (\Throwable $exception) {
            // Silently fall back when the lookup service is unreachable.
        }

        return __('device.location_unknown');
    }

    /**
     * @param string $ua
     * @return string
     */
    protected static function device(string $ua): string
    {
        if (preg_match('/ipad/i', $ua) || (preg_match('/macintosh/i', $ua) && preg_match('/mobile/i', $ua))) {
            return __('device.tablet');
        }

        if (preg_match('/ipod|iphone/i', $ua)) {
            return __('device.mobile');
        }

        if (preg_match('/android/i', $ua)) {
            if (preg_match('/mobile/i', $ua)) {
                return __('device.mobile');
            }
            return __('device.tablet');
        }

        if (preg_match('/windows phone/i', $ua)) {
            return __('device.mobile');
        }

        return __('device.desktop');
    }

    /**
     * @param string $ua
     * @return string
     */
    protected static function browser(string $ua): string
    {
        if (preg_match('/edg\/([\d.]+)/i', $ua)) {
            return 'Edge';
        }

        if (preg_match('/opr\/([\d.]+)/i', $ua)) {
            return 'Opera';
        }

        if (preg_match('/chrome\/([\d.]+)/i', $ua)) {
            return 'Chrome';
        }

        if (preg_match('/firefox\/([\d.]+)/i', $ua)) {
            return 'Firefox';
        }

        if (preg_match('/safari\/([\d.]+)/i', $ua)) {
            return 'Safari';
        }

        if (preg_match('/msie ([\d.]+)|trident/i', $ua)) {
            return 'Internet Explorer';
        }

        return __('device.browser_unknown');
    }

    /**
     * @param string $ua
     * @return string
     */
    protected static function platform(string $ua): string
    {
        if (preg_match('/windows nt 10\.0/i', $ua)) {
            return 'Windows 10/11';
        }

        if (preg_match('/windows nt 6\.3/i', $ua)) {
            return 'Windows 8.1';
        }

        if (preg_match('/windows nt 6\.2|windows phone/i', $ua)) {
            return 'Windows 8';
        }

        if (preg_match('/windows nt 6\.1/i', $ua)) {
            return 'Windows 7';
        }

        if (preg_match('/android/i', $ua) && preg_match('/mobile/i', $ua)) {
            return 'Android Mobile';
        }

        if (preg_match('/android/i', $ua)) {
            return 'Android Tablet';
        }

        if (preg_match('/iphone|ipod/i', $ua)) {
            return 'iOS';
        }

        if (preg_match('/ipad/i', $ua) || (preg_match('/macintosh/i', $ua) && preg_match('/mobile/i', $ua))) {
            return 'iOS';
        }

        if (preg_match('/mac os x/i', $ua)) {
            return 'macOS';
        }

        if (preg_match('/linux/i', $ua)) {
            return 'Linux';
        }

        return __('device.platform_unknown');
    }

    /**
     * Boxicons icon name for a given device type.
     *
     * @param string $device
     * @return string
     */
    public static function icon(string $device): string
    {
        return match (strtolower($device)) {
            strtolower(__('device.mobile')) => 'bx bx-mobile-alt',
            strtolower(__('device.tablet')) => 'bx bx-devices',
            default => 'bx bx-desktop',
        };
    }
}