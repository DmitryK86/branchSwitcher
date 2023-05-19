<?php

namespace app\helpers;

class LogDto
{
    private string $fileName;
    private string $content;

    public function __construct(string $fileName = '', string $content = '')
    {
        $this->fileName = $fileName;
        $this->content = $content;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isLogExist(): bool
    {
        return $this->fileName && $this->content;
    }
}
