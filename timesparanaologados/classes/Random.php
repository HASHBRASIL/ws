<?php

class Random
{
    public static function RandomCompat_strlen($binary_string)
    {
        if (!is_string($binary_string)) {
            throw new TypeError('RandomCompat_strlen() expects a string');
        }
        return mb_strlen($binary_string, '8bit');
    }

    public static function RandomCompat_substr($binary_string, $start, $length = null)
    {
        if (!is_string($binary_string)) {
            throw new TypeError('RandomCompat_substr(): First argument should be a string');
        }
        if (!is_int($start)) {
            throw new TypeError('RandomCompat_substr(): Second argument should be an integer');
        }
        if ($length === null) {
            $length = RandomCompat_strlen($length) - $start;
        } elseif (!is_int($length)) {
            throw new TypeError('RandomCompat_substr(): Third argument should be an integer, or omitted');
        }
        return mb_substr($binary_string, $start, $length, '8bit');
    }

    public static function random_bytes($bytes)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $bytes; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        } else {
            static $fp = null;
            if (empty($fp)) {
                $fp = fopen('/dev/urandom', 'rb');
                if (!empty($fp)) {
                    $st = fstat($fp);
                    if (($st['mode'] & 0170000) !== 020000) {
                        fclose($fp);
                        $fp = false;
                    }
                }
                if (!empty($fp) && function_exists('stream_set_read_buffer')) {
                    stream_set_read_buffer($fp, 8);
                }
            }
            if (!is_int($bytes)) {
                throw new TypeError(
                    'Length must be an integer'
                );
            }
            if ($bytes < 1) {
                throw new Error(
                    'Length must be greater than 0'
                );
            }
            if (!empty($fp)) {
                $remaining = $bytes;
                $buf = '';
                do {
                    $read = fread($fp, $remaining);
                    if ($read === false) {
                        $buf = false;
                        break;
                    }
                    $remaining -= self::RandomCompat_strlen($read);
                    $buf .= $read;
                } while ($remaining > 0);
                if ($buf !== false) {
                    if (self::RandomCompat_strlen($buf) === $bytes) {
                        return $buf;
                    }
                }
            }
            throw new Exception(
                'PHP failed to generate random data.'
            );
        }

    }

    public static function random_int($min, $max)
    {
        if (!is_numeric($min)) {
            throw new TypeError(
                'random_int(): $min must be an integer'
            );
        }
        if (!is_numeric($max)) {
            throw new TypeError(
                'random_int(): $max must be an integer'
            );
        }
        $min = (int)$min;
        $max = (int)$max;
        if ($min > $max) {
            throw new Error(
                'Minimum value must be less than or equal to the maximum value'
            );
        }
        if ($max === $min) {
            return $min;
        }
        $attempts = $bits = $bytes = $mask = $valueShift = 0;
        $range = $max - $min;
        if (!is_int($range)) {
            $bytes = PHP_INT_SIZE;
            $mask = ~0;
        } else {
            while ($range > 0) {
                if ($bits % 8 === 0) {
                    ++$bytes;
                }
                ++$bits;
                $range >>= 1;
                $mask = $mask << 1 | 1;
            }
            $valueShift = $min;
        }
        do {
            if ($attempts > 128) {
                throw new Exception(
                    'random_int: RNG is broken - too many rejections'
                );
            }
            $randomByteString = self::random_bytes($bytes);
            if ($randomByteString === false) {
                throw new Exception(
                    'Random number generator failure'
                );
            }
            $val = 0;
            for ($i = 0; $i < $bytes; ++$i) {
                $val |= ord($randomByteString[$i]) << ($i * 8);
            }
            $val &= $mask;
            $val += $valueShift;
            ++$attempts;
        } while (!is_int($val) || $val > $max || $val < $min);
        return (int)$val;
    }

    public static function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[self::random_int(0, $max)];
        }
        return $str;
    }
}
