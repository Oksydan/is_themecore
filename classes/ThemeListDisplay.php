<?php


class ThemeListDisplay {

  protected static $cookieName = 'listingDisplayType';
  protected static $displayList = ['grid', 'list'];

  static function setDisplay($display) {
    if(in_array($display, self::$displayList)) {
      setcookie(self::$cookieName, $display, time() + (86400 * 30), "/"); // 86400 = 1 day
    } else {
      setcookie(self::$cookieName, Configuration::get('THEMECORE_DISPLAY_LIST'), time() + (86400 * 30), "/"); // 86400 = 1 day
    }
  }

  static function getDisplay() {
    if(isset($_COOKIE[self::$cookieName]) && $_COOKIE[self::$cookieName]) {
      return $_COOKIE[self::$cookieName];
    } else {
      return Configuration::get('THEMECORE_DISPLAY_LIST');
    }
  }
}
