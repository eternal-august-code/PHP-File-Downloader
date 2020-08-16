<?php
/**
 * @author eternal-august-code
 */

/**
 * Simple version.
 */
// function fileDownload($url, $path="/", $fileName="") {
//     $fileFormat = end(explode('.', $url));
//     $defaultFileName = str_replace(".{$fileFormat}", "", end(explode('/', $url)));
//     $ch = curl_init($url);
//     if ($fileName != "") $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $path . $fileName . '.' . $fileFormat, 'wb');
//     else $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $path . $defaultFileName . '.' . $fileFormat, 'wb');
//     curl_setopt($ch, CURLOPT_FILE, $fp);
//     curl_setopt($ch, CURLOPT_HEADER, 0);
//     curl_exec($ch);
//     curl_close($ch);
//     fclose($fp);
// }

/**
 * Принимает массив аргументов, скачивает файл, возвращает строку с информацией о результате.
 * "url" - url
 * "path" - путь от корневой директории сервера(по умолчанию корневая директория)
 * "fileName" - имя файла(по умолчанию стандартное имя файла)
 * "redownloadExisting" => "Y"/"N" - повторное скачивание имеющегося файла в выбранной директории(по умолчанию "Y")
 * Пример:
 * fileDownload([
 *     "url" => "https://speed.hetzner.de/100MB.bin",
 *     "path" => "/download/video/fap/",
 *     "fileName" => "myFile",
 *     "redownloadExisting" => "Y"
 * ]); // Возвращает строку 'File myFile.bin download'.
*/
function fileDownload(array $params) {
    if (!array_key_exists("path", $params)) $params["path"] = "/";
    if (!array_key_exists("redownloadExisting", $params)) $params["redownloadExisting"] = "Y";
    if (!array_key_exists("fileName", $params)) $params["fileName"] = "";

    $filesInDir = scandir($_SERVER['DOCUMENT_ROOT'].$params["path"]);

    $explodedUrl = explode('.', $params["url"]);
    $fileFormat = end($explodedUrl);
    
    $explodedUrl = explode('/', $params["url"]);
    $defaultFileName = str_replace(".{$fileFormat}", "", end($explodedUrl));

    if ($params["redownloadExisting"] == "N" 
        && (in_array($defaultFileName.".".$fileFormat, $filesInDir) 
            || in_array($params["fileName"].".".$fileFormat, $filesInDir))) {
        if ($params["fileName"] != "") {
            return "File ".$params["fileName"].".".$fileFormat." Exists";
        } else {
            return "File ".$defaultFileName.".".$fileFormat." Exists";
        }     
    }

    if ($params["fileName"] != "") {
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $params["path"] . $params["fileName"] . '.' . $fileFormat, 'wb');
        $resultLog = "File ".$params["fileName"].".".$fileFormat." download"; 
    } else {
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $params["path"] . $defaultFileName . '.' . $fileFormat, 'wb');
        $resultLog = "File ".$defaultFileName.".".$fileFormat." download";
    }

    $ch = curl_init($params["url"]);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    return $resultLog;
}
