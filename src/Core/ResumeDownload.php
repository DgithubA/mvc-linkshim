<?php

namespace Lms\Core;
//
trait ResumeDownload
{
    private static $file;
    private static string $name;
    private static string $boundary;
    private static int $delay = 0;
    private static int $size = 0;
    private static string $mode = 'attachment';


    public static function file(string $filePath, int $delay = 0, string $mode = 'download',?string $file_name = null): void
    {

        if (!is_file($filePath)) {
            Response::error('File not found. ('.$filePath.')',self::HTTP_NOT_FOUND);
        }

        self::$size = filesize($filePath);
        self::$file = fopen($filePath, 'rb');
        self::$boundary = md5($filePath);
        self::$delay = $delay;
        self::$name = $file_name ?? basename($filePath);
        self::$mode = strtolower($mode) === 'inline' ? 'inline' : 'attachment';

        $ranges = null;
        $totalRanges = 0;

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SERVER['HTTP_RANGE'])) {
            $range = trim(str_ireplace('bytes=', '', $_SERVER['HTTP_RANGE']));
            $ranges = explode(',', $range);
            $totalRanges = count($ranges);
        }

        header("Accept-Ranges: bytes");
        if(self::$mode === 'inline'){
            $mime = mime_content_type($filePath);
            header('Content-Type: '.$mime);
        }else header('Content-Type: application/octet-stream');

        header('Content-Transfer-Encoding: binary');
        header(sprintf('Content-Disposition: %s; filename="%s"', self::$mode, self::$name));

        if ($totalRanges > 0) {
            header('HTTP/1.1 206 Partial Content');
            $totalRanges === 1 ? self::pushSingle($ranges[0]) : self::pushMulti($ranges);
        } else {
            header('Content-Length: '.self::$size);
            self::readFile();
        }

        flush();
    }

    private static function pushSingle(string $range): void
    {
        [$start, $end] = self::getRange($range);
        header("Content-Length: " . ($end - $start + 1));
        header("Content-Range: bytes $start-$end/".self::$size);
        fseek(self::$file, $start);
        self::readBuffer($end - $start + 1);
    }

    private static function pushMulti(array $ranges): void
    {
        $length = 0;
        $boundary = self::$boundary;
        $metaHeader = "Content-Type: application/octet-stream\r\n";
        $formatRange = "Content-Range: bytes %d-%d/%d\r\n\r\n";

        foreach ($ranges as $range) {
            [$start, $end] = self::getRange($range);
            $length += strlen("\r\n--$boundary\r\n");
            $length += strlen($metaHeader);
            $length += strlen(sprintf($formatRange, $start, $end, self::$size));
            $length += $end - $start + 1;
        }

        $length += strlen("\r\n--$boundary--\r\n");

        header("Content-Length: $length");
        header("Content-Type: multipart/byteranges; boundary=$boundary");

        foreach ($ranges as $range) {
            [$start, $end] = self::getRange($range);
            echo "\r\n--$boundary\r\n";
            echo $metaHeader;
            echo sprintf($formatRange, $start, $end, self::$size);
            fseek(self::$file, $start);
            self::readBuffer($end - $start + 1);
        }

        echo "\r\n--$boundary--\r\n";
    }

    private static function getRange(string $range): array
    {
        [$start, $end] = explode('-', $range) + [null, null];

        $start = ($start === '' || $start === null) ? null : (int)$start;
        $end = ($end === '' || $end === null) ? null : (int)$end;

        if ($start === null && $end !== null) {
            $start = max(0, self::$size - $end);
            $end = self::$size - 1;
        } elseif ($start !== null && $end === null) {
            $end = self::$size - 1;
        }

        if ($start === null || $end === null || $start > $end || $end >= self::$size) {
            http_response_code(416);
            header("Content-Range: */".self::$size);
            exit;
        }

        return [$start, $end];
    }

    private static function readFile(): void
    {
        while (!feof(self::$file)) {
            echo fread(self::$file, 8192);
            flush();
            usleep(self::$delay);
        }
    }

    private static function readBuffer(int $bytes, int $chunkSize = 8192): void
    {
        $remaining = $bytes;
        while ($remaining > 0 && !feof(self::$file)) {
            $readSize = min($chunkSize, $remaining);
            echo fread(self::$file, $readSize);
            flush();
            usleep(self::$delay);
            $remaining -= $readSize;
        }
    }
}
