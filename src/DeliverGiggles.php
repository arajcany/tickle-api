<?php


namespace App;

use App\Utility\Imaging\ArtifactImaging;

/**
 * Class DeliverGiggles
 *
 * Figure out the URL and return response based on URL
 *
 * @package App
 */
class DeliverGiggles
{

    public function getGiggles()
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = trim($url, '/');
        $url = explode("/", $url);

        $lastUrlPart = array_pop($url);
        $fileAndExt = pathinfo($lastUrlPart);

        $statusCodes = file_get_contents(DATA . 'httpStatusCodes.json');
        $statusCodes = json_decode($statusCodes, JSON_OBJECT_AS_ARRAY);
        //print_r($statusCodes);

        if (isset($fileAndExt['filename']) && isset($fileAndExt['extension'])) {

            //html
            $types = ['html', 'htm'];
            if (in_array($fileAndExt['extension'], $types)) {
                return file_get_contents(DATA . 'sample.html');
            }

            //txt
            $types = ['text', 'txt'];
            if (in_array($fileAndExt['extension'], $types)) {
                return file_get_contents(DATA . 'sample.txt');
            }

            //json
            $types = ['json'];
            if (in_array($fileAndExt['extension'], $types)) {
                $data = file_get_contents(DATA . 'css_colours.json');
                $data = json_encode(json_decode($data, JSON_OBJECT_AS_ARRAY), JSON_PRETTY_PRINT);
                return $data;
            }

            //image
            $types = ['png', 'jpeg', 'jpg'];
            if (in_array($fileAndExt['extension'], $types)) {
                $imaging = new ArtifactImaging();
                return $imaging->getImageResource();
            }

        } elseif (isset($statusCodes[$lastUrlPart]) && is_numeric($lastUrlPart)) {
            $return = $statusCodes[$lastUrlPart];
            $return['code'] = $lastUrlPart;
            return $return;
        } else {
            return file_get_contents(DATA . 'sample.html');
        }

    }

}