<?php

namespace App\Services;

class HtmlService
{
    protected $dom;

    public function __construct($html = null)
    {
        $this->dom = new \DomDocument;

        if ($html) $this->setHtml($html);
    }

    public function getTitle()
    {
        try {

            return $this->dom->getElementsByTagName('title')->item(0)->C14N();
        } catch(\Throwable $exception) {
            return "";
        }
    }

    public function setHtml($html)
    {
        @$this->dom->loadHTML($html);

        return $this;
    }

    public function getMetas()
    {
        try {

            $metas = $this->dom->getElementsByTagName('head')
                    ->item(0)->getElementsByTagName('meta');

            $storeMetas = "";

            foreach ($metas as $meta) {
                $storeMetas .= $meta->C14N();
            }

            return $storeMetas;
        } catch (\Throwable $exception) {
            return "";
        }

    }
}