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

            return $this->formatTitleTag(
                $this->dom->getElementsByTagName('title')[0]->textContent
            );
        } catch(\Throwable $exception) {
            return "";
        }
    }

    public function setHtml($html)
    {
        @$this->dom->loadHTML($html);

        return $this;
    }

    protected function formatTitleTag($title)
    {
        return "<title>$title</title>";
    }

    public function getMetas()
    {
        try {

            $metas = $this->dom->getElementsByTagName('head')
                    ->item(0)->getElementsByTagName('meta');
    
            foreach ($metas as $meta) {
    
                $elString = $meta->C14N();
                $text = substr($elString, 0, strpos($elString, '>') + 1);
                $htmlMetas[] = $text;
            }

            return implode(' ', $htmlMetas);
        } catch (\Throwable $exception) {
            return "";
        }

    }
}