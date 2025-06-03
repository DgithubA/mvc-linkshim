<?php


use Lms\Core\Response;

function view($view, array $data = []){
    $viewFile = __DIR__ . '/../app/Views/' . $view . '.php';

    if (!file_exists($viewFile)) {
        die("View not found: $view");
    }

    extract($data);

    ob_start();
    include $viewFile;
    $content = ob_get_clean();

    Response::html($content);
}


function to_json(array $data):string|false{
    return json_encode($data,448);
}

function json(array $data){
    Response::json($data);
}

if (!function_exists('dd')) {
    if (!function_exists('dd')) {
        function dd(...$vars): void
        {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
            $file = $backtrace['file'];
            $line = $backtrace['line'];

            echo '<style>pre{background:#222;color:#0f0;padding:10px;border-radius:5px;}</style>';
            echo "<pre>{$file}:{$line}</pre>";

            foreach ($vars as $i => $var) {
                echo "<pre>".var_export($var,true)."</pre>";
            }

            die();
        }
    }

}


if (!function_exists('abort')) {
    function abort(int $code = Response::HTTP_FORBIDDEN , string $message = ''): void
    {
        $defaultMessages = [
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        ];

        Response::error(($message ?: ($defaultMessages[$code] ?? "Error $code")),$code);
    }
}


function storage(string $file):void
{
    if (file_exists(STORAGE_DIR.'public/'.$file)) {
        echo "storage/$file";
    }else throw new Exception("Storage file not exists: $file");
}

function serveFile(string $file)
{
    if(file_exists($file)){
        $mime = mime_content_type($file);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }
}