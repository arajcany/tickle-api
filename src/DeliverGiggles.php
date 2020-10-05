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

        $fileAndExt = array_pop($url);

        $fileAndExt = pathinfo($fileAndExt);


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
                $data = json_decode(json_encode($data), JSON_OBJECT_AS_ARRAY);
                return $data;
            }

            //image
            $types = ['png', 'jpeg', 'jpg'];
            if (in_array($fileAndExt['extension'], $types)) {
                $imaging = new ArtifactImaging();
                return $imaging->getImageResource();
            }

        } else {
            return file_get_contents(DATA . 'sample.html');
        }

    }

}