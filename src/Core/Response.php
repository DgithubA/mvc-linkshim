<?php
namespace Lms\Core;
class Response
{
    use ResumeDownload;
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    public const DEFAULT_JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;
    // ارسال پاسخ JSON
    public static function json(array $data, int $status = self::HTTP_OK,int $flags = self::DEFAULT_JSON_FLAGS): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, $flags);
        exit;
    }
    public static function html(string $html, int $status = self::HTTP_OK): void
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    public static function status(int $status): void
    {
        http_response_code($status);
        exit;
    }

    public static function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }

    /**
     * Send a file to the browser for viewing or download.
     *
     * @param string $path Absolute file path
     * @param string $mode 'inline' to view, 'download' to download
     * @param string|null $filename Optional file name override
     */
    public static function send(string $path, string $mode = 'download', ?string $filename = null): void
    {
        $base_name = urldecode(basename($path));
        if (!file_exists($path) || !is_file($path))
            self::error('File not found. ('.$base_name.')',self::HTTP_NOT_FOUND);

        set_time_limit(0);
        self::file($path, mode: $mode,file_name: $filename);
        die();
    }

    public static function text(string $text, int $status = self::HTTP_OK): void
    {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
        echo $text;
        exit;
    }

    public static function error(string $message, int $status = self::HTTP_BAD_REQUEST): void
    {
        self::text($message, $status);
    }

    public static function success(string $message, int $status_code = self::HTTP_OK): void
    {
        $request = Request::getInstance();
        if($request->acceptJson()){
            Response::json(['success'=>true,'message'=>$message],$status_code);
        }else Response::text($message, $status_code);
    }

    public static function unSuccess(string $message, int $status_code = self::HTTP_BAD_REQUEST): void
    {
        $request = Request::getInstance();
        if($request->acceptJson()){
            Response::json(['success'=>false,'message'=>$message],$status_code);
        }else Response::error($message, $status_code);
    }
}
