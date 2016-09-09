<?php
class UUID {

  public static function v4() {
    require_once('Random.php');
    $uuid = array(
      'time_low'  => 0,
      'time_mid'  => 0,
      'time_hi'  => 0,
      'clock_seq_hi' => 0,
      'clock_seq_low' => 0,
      'node'   => array()
     );

     $uuid['time_low'] = Random::random_int(0, 0xffff) + (Random::random_int(0, 0xffff) << 16);
     $uuid['time_mid'] = Random::random_int(0, 0xffff);
     $uuid['time_hi'] = (4 << 12) | (Random::random_int(0, 0x1000));
     $uuid['clock_seq_hi'] = (1 << 7) | (Random::random_int(0, 128));
     $uuid['clock_seq_low'] = Random::random_int(0, 255);

     for ($i = 0; $i < 6; $i++) {
      $uuid['node'][$i] = Random::random_int(0, 255);
     }

     $uuid = sprintf('%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
      $uuid['time_low'],
      $uuid['time_mid'],
      $uuid['time_hi'],
      $uuid['clock_seq_hi'],
      $uuid['clock_seq_low'],
      $uuid['node'][0],
      $uuid['node'][1],
      $uuid['node'][2],
      $uuid['node'][3],
      $uuid['node'][4],
      $uuid['node'][5]);
      return $uuid;
    // return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    //   Random::random_int(0, 0xffff), Random::random_int(0, 0xffff),
    //   Random::random_int(0, 0xffff),
    //   Random::random_int(0, 0x0fff) | 0x4000,
    //   Random::random_int(0, 0x3fff) | 0x8000,
    //   Random::random_int(0, 0xffff), Random::random_int(0, 0xffff), Random::random_int(0, 0xffff)
    // );
  }

  public static function is_valid($uuid) {
    return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                      '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
  }
  
  
   public static function is_uuid($uuid){
     return preg_match('/^([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})$/', $uuid);
  }
}
