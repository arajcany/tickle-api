<?php


namespace App\Utility\Imaging;


use Intervention\Image\ImageManager;

class ArtifactImaging
{

    public function getImageResource($settings = [])
    {
        $settingsDefault = [
            'width' => 256,
            'height' => 256,
            'background' => 'random',
            'format' => 'png',
            'quality' => '90',
            'inputFilename' => null,
        ];
        $settings = array_merge($settingsDefault, $settings);
        $settings['background'] = $this->convertToHex($settings['background']);


        //mime type overrides the format
        if (isset($settings['type'])) {
            $settings['format'] = $this->getExtensionFromMimeType($settings['type']);
        }


        //validate input file
        if (!empty($settings['inputFilename'])) {
            if (!is_readable($settings['inputFilename'])) {
                $settings['inputFilename'] = null;
            }
        }

        //add a watermark
        $watermarkFilename = DATA . 'copyright.png';
        $watermarkManager = new ImageManager();
        $watermarkWidth = ceil($settings['width'] * .8);
        $watermarkHeight = ceil($settings['width'] * .8);
        $watermarkResource = $watermarkManager
            ->make($watermarkFilename)
            ->fit($watermarkWidth, $watermarkHeight);


        //start the image
        $manager = new ImageManager();

        if ($settings['inputFilename'] == null) {
            $imageResource = $manager
                ->canvas($settings['width'], $settings['height'], $settings['background'])
                ->encode($settings['format'], $settings['quality']);
        } else {
            $imageResource = $manager
                ->make($settings['inputFilename'])
                ->encode($settings['format'], $settings['quality'])
                ->fit($settings['width'], $settings['height'])
                ->insert($watermarkResource, 'center');
        }

        return $imageResource;
    }

    /**
     * @param $unkownValue
     * @param string $default
     * @return string
     */
    public function convertToHex($unkownValue, $default = '#808080')
    {

        //check if valid hex
        if (preg_match('/#([a-f0-9]{3}){1,2}\b/i', $unkownValue)) {
            return $unkownValue;
        }

        //check if valid css colour
        $cssColours = $this->getCssColoursMap();
        if (isset($cssColours[$unkownValue])) {
            $rgb = $cssColours[$unkownValue];
            return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
        }

        //check if random
        if (strtolower($unkownValue) == 'random') {
            $rndKey = array_rand($cssColours);
            $rgb = $cssColours[$rndKey];
            return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
        }

        //check if rbg
        if (is_array($unkownValue)) {
            if (!count($unkownValue) == 3) {
                return $default;
            }

            if (!in_array($unkownValue[0], range(0, 255))) {
                return $default;
            }
            if (!in_array($unkownValue[1], range(0, 255))) {
                return $default;
            }
            if (!in_array($unkownValue[2], range(0, 255))) {
                return $default;
            }

            return sprintf("#%02x%02x%02x", $unkownValue[0], $unkownValue[1], $unkownValue[2]);
        }

        if (is_string($unkownValue)) {
            $re = '/\d+/';
            $str = $unkownValue;

            preg_match_all($re, $str, $matches);

            if (!isset($matches[0])) {
                return $default;
            }
            $matches = $matches[0];

            if (!count($matches) == 3) {
                return $default;
            }

            if (!in_array($matches[0], range(0, 255))) {
                return $default;
            }
            if (!in_array($matches[1], range(0, 255))) {
                return $default;
            }
            if (!in_array($matches[2], range(0, 255))) {
                return $default;
            }
            return sprintf("#%02x%02x%02x", $matches[0], $matches[1], $matches[2]);
        }

        return $default;
    }

    public function getCssColoursMap()
    {
        return json_decode(file_get_contents(DATA . 'css_colours.json'), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Extract the extension from a MIME TYPE string
     *
     * @param string $mimeType
     * @return string
     */
    public function getExtensionFromMimeType($mimeType = "")
    {
        $mimeType = explode("/", $mimeType);
        if (isset($mimeType[1])) {
            $extension = $mimeType[1];
            $extension = strtolower($extension);
            $in = ["jpeg"];
            $out = ["jpg"];
            $extension = str_replace($in, $out, $extension);
            return $extension;
        } else {
            return '';
        }
    }


    public function getRandomColour()
    {
        $rnd = mt_rand(1, 3);

        if ($rnd == 1) {
            return $this->getBlue();
        } elseif ($rnd == 2) {
            return $this->getGreen();
        } elseif ($rnd == 3) {
            return $this->getBlue();
        }
    }

    /**
     * Give back a test image
     *
     * @return false|string
     */
    public function getRed()
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAZElEQVR42u3QAQ0AAAjDMO5fNGCD0GUKmq7a/xYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADgfgMNmH/BjHDEMQAAAABJRU5ErkJggg==');
    }

    /**
     * Give back a test image
     *
     * @return false|string
     */
    public function getGreen()
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAY0lEQVR42u3QAQ0AAAjDMO5fNGCD0M1BU70/LgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMD9Bs3Jf8EvyPO9AAAAAElFTkSuQmCC');
    }

    /**
     * Give back a test image
     *
     * @return false|string
     */
    public function getBlue()
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAAYklEQVR42u3QAQ0AAAgDoL9/aM3hhAg0mcljFSBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAgQIECBAgAABAu5bjgl/wXGcqdoAAAAASUVORK5CYII=');
    }

}
