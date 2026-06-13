<?php

namespace App\Support;

class YoutubeEmbed
{
    public static function extractId(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $url = trim($url);

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function embedUrl(?string $url): ?string
    {
        $id = self::extractId($url);

        return $id ? 'https://www.youtube.com/embed/'.$id : null;
    }
}
