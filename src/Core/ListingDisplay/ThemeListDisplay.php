<?php

namespace Oksydan\Module\IsThemeCore\Core\ListingDisplay;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeListDisplay {

  private $cookieName = 'listingDisplayType';
  private $displayList = [
    'grid',
    'list'
  ];

  public function setDisplay($display) {
    if(!in_array($display, $this->displayList)) {
      $display = GeneralConfiguration::THEMECORE_DISPLAY_LIST;
    }

    return (new Response())->headers->setCookie(new Cookie($this->cookieName, $display, strtotime('now + 30 days')));
  }

  public function getDisplay() {
    $displayFromCookie = (new Request)->cookie->get($this->cookieName);

    if($displayFromCookie) {
      return $displayFromCookie;
    }

    return GeneralConfiguration::THEMECORE_DISPLAY_LIST;
  }

  public function getDisplayOptions() {
    return $this->displayList;
  }
}