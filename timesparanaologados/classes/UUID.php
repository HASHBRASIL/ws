<?php

class UUID
{

    public static function v4()
    {
        require_once 'Random.php';
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            Random::random_int(0, 0xffff), Random::random_int(0, 0xffff),
            Random::random_int(0, 0xffff),
            Random::random_int(0, 0x0fff) | 0x4000,
            Random::random_int(0, 0x3fff) | 0x8000,
            Random::random_int(0, 0xffff), Random::random_int(0, 0xffff), Random::random_int(0, 0xffff)
        );
    }

    public static function is_valid($uuid)
    {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?' .
            '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }
}
