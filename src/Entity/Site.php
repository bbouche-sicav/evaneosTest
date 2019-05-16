<?php

class Site implements iEntity
{
    public $id;
    public $url;

    public function __construct($id, $url)
    {
        $this->id = $id;
        $this->url = $url;
    }
    public function methods() {
       echo 'test '.__CLASS__;
    }
}
