<?php

namespace Itav;

class AbstractController {

    private $container;

    public function __construct() {
        $this->container = ServiceContainer::getInstance();         
    }

    public function get($index) {
        return $this->container->get($index);
    }

}
