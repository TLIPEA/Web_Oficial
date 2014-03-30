<?php
//////////////////////////////////////////////////////////////
///  phpThumb() by James Heinrich <info@silisoftware.com>   //
//        available at http://phpthumb.sourceforge.net     ///
//////////////////////////////////////////////////////////////
///  canvasCrop() by Andrew Collington <php@amnuts.com>     //
//        available at http://php.amnuts.com                //
//                                                         ///
//////////////////////////////////////////////////////////////

define("ccTOPLEFT", 0);
define("ccTOP", 1);
define("ccTOPRIGHT", 2);
define("ccLEFT", 3);
define("ccCENTRE", 4);
define("ccCENTER", 4);
define("ccRIGHT", 5);
define("ccBOTTOMLEFT", 6);
define("ccBOTTOM", 7);
define("ccBOTTOMRIGHT", 8);

class canvasCrop{
    var $_imgOrig;
    var $_imgFinal;
    var $_showDebug;
    var $_gdVersion;

    /*
    *
    * @return canvasCrop
    * @param bool $debug
    * @desc Class initializer
    */
    function canvasCrop($debug = false)
    {
        $this->_showDebug = ($debug ? true : false);
        $this->_gdVersion = $this->gd_version();
    }

    /*
    *
    * @return bool
    * @param string $filename
    * @desc Load an image from the file system - method based on file extension
    */
    function loadImage($filename)
    {
        if (!@file_exists($filename)){
            $this->_debug('loadImage', "The supplied file name '$filename' does not point to a readable file.");
            return false;
        }

        $ext = strtolower($this->_getExtension($filename));
        $func = "imagecreatefrom$ext";

        if (!@function_exists($func)){
            $this->_debug('loadImage', "That file cannot be loaded with the function '$func'.");
            return false;
        }

        $this->_imgOrig = @$func($filename);

        if ($this->_imgOrig == null){
            $this->_debug('loadImage', 'The image could not be loaded.');
            return false;
        }

        return true;
    }

    /*
    *
    * @return bool
    * @param string $string
    * @desc Load an image from a string (eg. from a database table)
    */
    function loadImageFromString($string)
    {
        $this->_imgOrig = @ImageCreateFromString($string);
        if (!$this->_imgOrig){
            $this->_debug('loadImageFromString', 'The image could not be loaded.');
            return false;
        }
        return true;
    }

    /*
    *
    * @return bool
    * @param string $filename
    * @param int $quality
    * @desc Save the cropped image
    */
    function saveImage($filename, $quality = 100)
    {
        if ($this->_imgFinal == null){
            $this->_debug('saveImage', 'There is no processed image to save.');
            return false;
        }

        $ext = strtolower($this->_getExtension($filename));
        $func = "image$ext";

        if (!@function_exists($func)){
            $this->_debug('saveImage', "That file cannot be saved with the function '$func'.");
            return false;
        }

        $saved = false;
        if ($ext == 'png') $saved = $func($this->_imgFinal, $filename);
        if ($ext == 'jpeg') $saved = $func($this->_imgFinal, $filename, $quality);
        if ($saved == false){
            $this->_debug('saveImage', "Could not save the output file '$filename' as a $ext.");
            return false;
        }

        return true;
    }

    /*
    *
    * @return bool
    * @param string $type
    * @param int $quality
    * @desc Shows the cropped image without any saving
    */
    function showImage($type = 'png', $quality = 100)
    {
        if ($this->_imgFinal == null){
            $this->_debug('showImage', 'There is no processed image to show.');
            return false;
        }
        if ($type == 'png'){
            echo @ImagePNG($this->_imgFinal);
            return true;
        }else if ($type == 'jpg' || $type == 'jpeg'){
            echo @ImageJPEG($this->_imgFinal, '', $quality);
            return true;
        }else{
            $this->_debug('showImage', "Could not show the output file as a $type.");
            return false;
        }
    }

    /*
    *
    * @return int
    * @param int $x
    * @param int $y
    * @param int $position
    * @desc Determines the dimensions to crop to if using the 'crop by size' method
    */
    function cropBySize($x, $y, $position = ccCENTRE)
    {
        if ($x == 0){
            $nx = @ImageSX($this->_imgOrig);
        }else{
            $nx = @ImageSX($this->_imgOrig) - $x;
        }
        if ($y == 0){
            $ny = @ImageSY($this->_imgOrig);
        }else{
            $ny = @ImageSY($this->_imgOrig) - $y;
        }
        return ($this->_cropSize(-1, -1, $nx, $ny, $position, 'cropBySize'));
    }

    /*
    *
    * @return int
    * @param int $x
    * @param int $y
    * @param int $position
    * @desc Determines the dimensions to crop to if using the 'crop to size' method
    */
    function cropToSize($x, $y, $position = ccCENTRE)
    {
        if ($x == 0) $x = 1;
        if ($y == 0) $y = 1;
        return ($this->_cropSize(-1, -1, $x, $y, $position, 'cropToSize'));
    }

    /*
    *
    * @return int
    * @param int $sx
    * @param int $sy
    * @param int $ex
    * @param int $ey
    * @desc Determines the dimensions to crop to if using the 'crop to dimensions' method
    */
    function cropToDimensions($sx, $sy, $ex, $ey)
    {
        $nx = abs($ex - $sx);
        $ny = abs($ey - $sy);
        return ($this->_cropSize($sx, $sy, $nx, $ny, $position, 'cropToDimensions'));
    }

    /*
    *
    * @return int
    * @param int $percentx
    * @param int $percenty
    * @param int $position
    * @desc Determines the dimensions to crop to if using the 'crop by percentage' method
    */
    function cropByPercent($percentx, $percenty, $position = ccCENTRE)
    {
        if ($percentx == 0){
            $nx = @ImageSX($this->_imgOrig);
        }else{
            $nx = @ImageSX($this->_imgOrig) - (($percentx / 100) * @ImageSX($this->_imgOrig));
        }
        if ($percenty == 0){
            $ny = @ImageSY($this->_imgOrig);
        }else{
            $ny = @ImageSY($this->_imgOrig) - (($percenty / 100) * @ImageSY($this->_imgOrig));
        }
        return ($this->_cropSize(-1, -1, $nx, $ny, $position, 'cropByPercent'));
    }

    /*
    *
    * @return int
    * @param int $percentx
    * @param int $percenty
    * @param int $position
    * @desc Determines the dimensions to crop to if using the 'crop to percentage' method
    */
    function cropToPercent($percentx, $percenty, $position = ccCENTRE)
    {
        if ($percentx == 0){
            $nx = @ImageSX($this->_imgOrig);
        }else{
            $nx = ($percentx / 100) * @ImageSX($this->_imgOrig);
        }
        if ($percenty == 0){
            $ny = @ImageSY($this->_imgOrig);
        }else{
            $ny = ($percenty / 100) * @ImageSY($this->_imgOrig);
        }
        return ($this->_cropSize(-1, -1, $nx, $ny, $position, 'cropByPercent'));
    }

    /*
    *
    * @return bool
    * @param int $threshold
    * @desc Determines the dimensions to crop to if using the 'automatic crop by threshold' method
    */
    function cropByAuto($threshold = 254)
    {
        if ($threshold < 0) $threshold = 0;
        if ($threshold > 255) $threshold = 255;

        $sizex = @ImageSX($this->_imgOrig);
        $sizey = @ImageSY($this->_imgOrig);

        $sx = $sy = $ex = $ey = -1;
        for ($y = 0; $y < $sizey; $y++){
            for ($x = 0; $x < $sizex; $x++){
                if ($threshold >= $this->_getThresholdValue($this->_imgOrig, $x, $y)){
                    if ($sy == -1) $sy = $y;
                    else $ey = $y;

                    if ($sx == -1) $sx = $x;
                    else{
                        if ($x < $sx) $sx = $x;
                        else if ($x > $ex) $ex = $x;
                    }
                }
            }
        }
        $nx = abs($ex - $sx);
        $ny = abs($ey - $sy);
        return ($this->_cropSize($sx, $sy, $nx, $ny, ccTOPLEFT, 'cropByAuto'));
    }

    /*
    *
    * @return void
    * @desc Destroy the resources used by the images
    */
    function flushImages()
    {
        @ImageDestroy($this->_imgOrig);
        @ImageDestroy($this->_imgFinal);
        $this->_imgOrig = $this->_imgFinal = null;
    }

    /*
    *
    * @return bool
    * @param int $ox Original image width
    * @param int $oy Original image height
    * @param int $nx New width
    * @param int $ny New height
    * @param int $position Where to place the crop
    * @param string $function Name of the calling function
    * @desc Creates the cropped image based on passed parameters
    */
    function _cropSize($ox, $oy, $nx, $ny, $position, $function)
    {
        if ($this->_imgOrig == null){
            $this->_debug($function, 'The original image has not been loaded.');
            return false;
        }
        if (($nx <= 0) || ($ny <= 0)){
            $this->_debug($function, 'The image could not be cropped because the size given is not valid.');
            return false;
        }
        if (($nx > @ImageSX($this->_imgOrig)) || ($ny > @ImageSY($this->_imgOrig))){
            $this->_debug($function, 'The image could not be cropped because the size given is larger than the original image.');
            return false;
        }
        if ($ox == -1 || $oy == -1){
            list($ox, $oy) = $this->_getCopyPosition($nx, $ny, $position);
        }
        if ($this->_gdVersion >= 2){
            $this->_imgFinal = @ImageCreateTrueColor($nx, $ny);
            imagefill($this->_imgFinal,0,0,imagecolorallocate($this->_imgFinal, 255, 255, 255));
            @imagecopyresampled($this->_imgFinal, $this->_imgOrig, 0, 0, $ox, $oy, $nx, $ny, $nx, $ny);
        }else{
            $this->_imgFinal = @ImageCreate($nx, $ny);
            @ImageCopyResized($this->_imgFinal, $this->_imgOrig, 0, 0, $ox, $oy, $nx, $ny, $nx, $ny);
        }
        return true;
    }

    /*
    *
    * @return array
    * @param int $nx
    * @param int $ny
    * @param int $position
    * @desc Determines dimensions of the crop
    */
    function _getCopyPosition($nx, $ny, $position)
    {
        $ox = @ImageSX($this->_imgOrig);
        $oy = @ImageSY($this->_imgOrig);

        switch ($position){
            case ccTOPLEFT:
                return array(0, 0);
            case ccTOP:
                return array(ceil(($ox - $nx) / 2), 0);
            case ccTOPRIGHT:
                return array(($ox - $nx), 0);
            case ccLEFT:
                return array(0, ceil(($oy - $ny) / 2));
            case ccCENTRE:
                return array(ceil(($ox - $nx) / 2), ceil(($oy - $ny) / 2));
            case ccRIGHT:
                return array(($ox - $nx), ceil(($oy - $ny) / 2));
            case ccBOTTOMLEFT:
                return array(0, ($oy - $ny));
            case ccBOTTOM:
                return array(ceil(($ox - $nx) / 2), ($oy - $ny));
            case ccBOTTOMRIGHT:
                return array(($ox - $nx), ($oy - $ny));
        }
    }

    /*
    *
    * @return float
    * @param resource $im
    * @param int $x
    * @param int $y
    * @desc Determines the intensity value of a pixel at the passed co-ordinates
    */
    function _getThresholdValue($im, $x, $y)
    {
        $rgb = ImageColorAt($im, $x, $y);
        $r = ($rgb >> 16) &0xFF;
        $g = ($rgb >> 8) &0xFF;
        $b = $rgb &0xFF;
        $intensity = ($r + $g + $b) / 3;
        return $intensity;
    }

    /*
    *
    * @return string
    * @param string $filename
    * @desc Get the extension of a file name
    */
    function _getExtension($filename)
    {
        $ext = @strtolower(@substr($filename, (@strrpos($filename, ".") ? @strrpos($filename, ".") + 1 : @strlen($filename)), @strlen($filename)));
        return ($ext == 'jpg') ? 'jpeg' : $ext;
    }

    /*
    *
    * @return void
    * @param string $function
    * @param string $string
    * @desc Shows debugging information
    */
    function _debug($function, $string)
    {
        if ($this->_showDebug){
            echo "<p><strong style=\"color:#FF0000\">Error in function $function:</strong> $string</p>\n";
        }
    }

    function version_compare_replacement_sub($version1, $version2, $operator = '')
    {
        // If you specify the third optional operator argument, you can test for a particular relationship.
        // The possible operators are: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne respectively.
        // Using this argument, the function will return 1 if the relationship is the one specified by the operator, 0 otherwise.
        // If a part contains special version strings these are handled in the following order: dev < (alpha = a) < (beta = b) < RC < pl
        static $versiontype_lookup = array();
        if (empty($versiontype_lookup)){
            $versiontype_lookup['dev'] = 10001;
            $versiontype_lookup['a'] = 10002;
            $versiontype_lookup['alpha'] = 10002;
            $versiontype_lookup['b'] = 10003;
            $versiontype_lookup['beta'] = 10003;
            $versiontype_lookup['RC'] = 10004;
            $versiontype_lookup['pl'] = 10005;
        }
        if (isset($versiontype_lookup[$version1])){
            $version1 = $versiontype_lookup[$version1];
        }
        if (isset($versiontype_lookup[$version2])){
            $version2 = $versiontype_lookup[$version2];
        }

        switch ($operator){
            case '<':
            case 'lt':
                return intval($version1 < $version2);
                break;
            case '<=':
            case 'le':
                return intval($version1 <= $version2);
                break;
            case '>':
            case 'gt':
                return intval($version1 > $version2);
                break;
            case '>=':
            case 'ge':
                return intval($version1 >= $version2);
                break;
            case '==':
            case '=':
            case 'eq':
                return intval($version1 == $version2);
                break;
            case '!=':
            case '<>':
            case 'ne':
                return intval($version1 != $version2);
                break;
        }
        if ($version1 == $version2){
            return 0;
        }elseif ($version1 < $version2){
            return -1;
        }
        return 1;
    }

    function version_compare_replacement($version1, $version2, $operator = '')
    {
        if (function_exists('version_compare')){
            // built into PHP v4.1.0+
            return version_compare($version1, $version2, $operator);
        }
        // The function first replaces _, - and + with a dot . in the version strings
        $version1 = strtr($version1, '_-+', '...');
        $version2 = strtr($version2, '_-+', '...');
        // and also inserts dots . before and after any non number so that for example '4.3.2RC1' becomes '4.3.2.RC.1'.
        // Then it splits the results like if you were using explode('.',$ver). Then it compares the parts starting from left to right.
        $version1 = preg_replace('#([0-9]+)([A-Z]+)([0-9]+)#', '\\1.\\2.\\3', $version1);
        $version2 = preg_replace('#([0-9]+)([A-Z]+)([0-9]+)#', '\\1.\\2.\\3', $version2);

        $parts1 = explode('.', $version1);
        $parts2 = explode('.', $version1);
        $parts_count = max(count($parts1), count($parts2));
        for ($i = 0; $i < $parts_count; $i++){
            $comparison = $this->version_compare_replacement_sub($version1, $version2, $operator);
            if ($comparison != 0){
                return $comparison;
            }
        }
        return 0;
    }

    function gd_version($fullstring = false)
    {
        static $cache_gd_version = array();
        if (empty($cache_gd_version)){
            $gd_info = $this->gd_info();
            if (substr($gd_info['GD Version'], 0, strlen('bundled (')) == 'bundled ('){
                $cache_gd_version[1] = $gd_info['GD Version']; // e.g. "bundled (2.0.15 compatible)"
                $cache_gd_version[0] = (float) substr($gd_info['GD Version'], strlen('bundled ('), 3); // e.g. "2.0" (not "bundled (2.0.15 compatible)")
            }else{
                $cache_gd_version[1] = $gd_info['GD Version']; // e.g. "1.6.2 or higher"
                $cache_gd_version[0] = (float) substr($gd_info['GD Version'], 0, 3); // e.g. "1.6" (not "1.6.2 or higher")
            }
        }
        return $cache_gd_version[intval($fullstring)];
    }

    function gd_info()
    {
        if (function_exists('gd_info')){
            // built into PHP v4.3.0+ (with bundled GD2 library)
            return gd_info();
        }

        static $gd_info = array();
        if (empty($gd_info)){
            // based on code by johnschaefer at gmx dot de
            // from PHP help on gd_info()
            $gd_info = array('GD Version' => '',
                'FreeType Support' => false,
                'FreeType Linkage' => '',
                'T1Lib Support' => false,
                'GIF Read Support' => false,
                'GIF Create Support' => false,
                'JPG Support' => false,
                'PNG Support' => false,
                'WBMP Support' => false,
                'XBM Support' => false
                );
            $phpinfo_array = $this->phpinfo_array();
            foreach ($phpinfo_array as $line){
                $line = trim(strip_tags($line));
                foreach ($gd_info as $key => $value){
                    // if (strpos($line, $key) !== false) {
                    if (strpos($line, $key) === 0){
                        $newvalue = trim(str_replace($key, '', $line));
                        $gd_info[$key] = $newvalue;
                    }
                }
            }
            if (empty($gd_info['GD Version'])){
                // probable cause: "phpinfo() disabled for security reasons"
                if (function_exists('ImageTypes')){
                    $imagetypes = ImageTypes();
                    if ($imagetypes &IMG_PNG){
                        $gd_info['PNG Support'] = true;
                    }
                    if ($imagetypes &IMG_GIF){
                        $gd_info['GIF Create Support'] = true;
                    }
                    if ($imagetypes &IMG_JPG){
                        $gd_info['JPG Support'] = true;
                    }
                    if ($imagetypes &IMG_WBMP){
                        $gd_info['WBMP Support'] = true;
                    }
                }
                // to determine capability of GIF creation, try to use ImageCreateFromGIF on a 1px GIF
                if (function_exists('ImageCreateFromGIF')){
                    if ($tempfilename = $this->GetTempName()){
                        if ($fp_tempfile = @fopen($tempfilename, 'wb')){
                            fwrite($fp_tempfile, base64_decode('R0lGODlhAQABAIAAAH//AP///ywAAAAAAQABAAACAUQAOw==')); // very simple 1px GIF file base64-encoded as string
                            fclose($fp_tempfile);
                            // if we can convert the GIF file to a GD image then GIF create support must be enabled, otherwise it's not
                            $gd_info['GIF Read Support'] = (bool) @ImageCreateFromGIF($tempfilename);
                        }
                        unlink($tempfilename);
                    }
                }
                if (function_exists('ImageCreateTrueColor') && @ImageCreateTrueColor(1, 1)){
                    $gd_info['GD Version'] = '2.0.1 or higher (assumed)';
                }elseif (function_exists('ImageCreate') && @ImageCreate(1, 1)){
                    $gd_info['GD Version'] = '1.6.0 or higher (assumed)';
                }
            }
        }
        return $gd_info;
    }

    function phpinfo_array()
    {
        static $phpinfo_array = array();
        if (empty($phpinfo_array)){
            ob_start();
            phpinfo();
            $phpinfo = ob_get_contents();
            ob_end_clean();
            $phpinfo_array = explode("\n", $phpinfo);
        }
        return $phpinfo_array;
    }
}

class imgRedim extends canvasCrop{
    var $temp_directory;
    var $improve_thumbs;

    function imgRedim($debug = false, $filters = false, $temp_path)
    {
        $this->canvasCrop($debug);
        if (empty($temp_path)){
            $this->_debug($function, 'You must specify a temp directory.');
        }
        $this->improve_thumbs = $filters;
    }

    function redimToSize($x, $y, $crop = false)
    {
        return ($this->_reSize($x, $y, $crop, 'redimToSize'));
    }

    function _reSize($nx, $ny, $crop = false, $function)
    {
        if ($this->_imgOrig == null){
            $this->_debug($function, 'The original image has not been loaded.');
            return false;
        }
        if (($nx <= 0) || ($ny <= 0)){
            $this->_debug($function, 'The image could not be resized because the size given is not valid.');
            return false;
        }

        $ox = @ImageSX($this->_imgOrig);
        $oy = @ImageSY($this->_imgOrig);
        $nnx = $nx;
        $nny = $ny;

        if (($nx > $ox) || ($ny > $oy)){
            $this->_debug($function, 'The image could not be resized because the size given is larger than the original image.');
            return false;
        }

        if ($ox > $oy){
            $nx = $ox * $ny / $oy;
        }elseif ($ox < $oy){
            $ny = $oy * $nx / $ox;
        }

        if ($this->_gdVersion >= 2){
            $this->_imgFinal = @ImageCreateTrueColor($nx, $ny);
            imagefill($this->_imgFinal, 0, 0, imagecolorallocate($this->_imgFinal, 255, 255, 255));
            @ImageCopyResampled($this->_imgFinal, $this->_imgOrig, 0, 0, 0, 0, $nx, $ny, $ox, $oy);
        }else{
            $this->_imgFinal = @ImageCreate($nx, $ny);
            @ImageCopyResized($this->_imgFinal, $this->_imgOrig, 0, 0, 0, 0, $nx, $ny, $ox, $oy);
        }

        $cropNecessary = false;
        if (($nx != $nnx) || ($ny != $nny)) {
            $cropNecessary = true;
        }

        if ($crop && $cropNecessary) {
            $_imgOrigCopy = $this->_imgOrig;
            $this->_imgOrig = $this->_imgFinal;
            $result = $this->cropToSize($nnx, $nny);
            if ($this->improve_thumbs){
                $result = $this->WhiteBalance($this->_imgFinal);
                $result = $this->AutoContrast($this->_imgFinal);
                $result = $this->UnSharpMask($this->_imgFinal, "80|0.5|3");
                $result = $this->Desaturate($this->_imgFinal, -10);
            }
            $this->_imgOrig = $_imgOrigCopy;
            unset($_imgOrigCopy);

            return $result;
        }
        if ($this->improve_thumbs){
            $result = $this->WhiteBalance($this->_imgFinal);
            $result = $this->AutoContrast($this->_imgFinal);
            $result = $this->UnSharpMask($this->_imgFinal, "80|0.5|3");
            $result = $this->Desaturate($this->_imgFinal, -10);
        }

        return true;
    }

    function IsHexColor($HexColorString)
    {
        return preg_match('#^[0-9A-F]{6}$#i', $HexColorString);
    }

    function GetPixelColor(&$img, $x, $y)
    {
        return @ImageColorsForIndex($img, @ImageColorAt($img, $x, $y));
    }

    function GrayscalePixel($OriginalPixel)
    {
        $gray = $this->GrayscaleValue($OriginalPixel['red'], $OriginalPixel['green'], $OriginalPixel['blue']);
        return array('red' => $gray, 'green' => $gray, 'blue' => $gray);
    }

    function GrayscaleValue($r, $g, $b)
    {
        return round(($r * 0.30) + ($g * 0.59) + ($b * 0.11));
    }

    function ImageColorAllocateAlphaSafe(&$gdimg_hexcolorallocate, $R, $G, $B, $alpha = false)
    {
        if ($this->version_compare_replacement(phpversion(), '4.3.2', '>=') && ($alpha !== false)){
            return ImageColorAllocateAlpha($gdimg_hexcolorallocate, $R, $G, $B, intval($alpha));
        }else{
            return ImageColorAllocate($gdimg_hexcolorallocate, $R, $G, $B);
        }
    }

    function Desaturate(&$gdimg, $amount, $color = '')
    {
        return $this->Colorize($gdimg, $amount, ($this->IsHexColor($color) ? $color : 'gray'));
    }

    //////////////////////////////////////////////////////////////
    ////                                                      ////
    ////              p h p U n s h a r p M a s k             ////
    ////                                                      ////
    ////    Unsharp mask algorithm by Torstein H?nsi 2003.    ////
    ////               thoensi_at_netcom_dot_no               ////
    ////               Please leave this notice.              ////
    ////                                                      ////
    //////////////////////////////////////////////////////////////
    /// From: http://vikjavev.no/hovudsida/umtestside.php       //
    //////////////////////////////////////////////////////////////
    function UnSharpMask(&$img, $parameter)
    {
        @list($amount, $radius, $threshold) = explode('|', $parameter);
        $amount = ($amount ? $amount : 80);
        $radius = ($radius ? $radius : 0.5);
        $threshold = (strlen($threshold) ? $threshold : 3);
        if ($this->gd_version() >= 2.0){
            $this->applyUnsharpMask($img, $amount, $radius, $threshold);
        }else{
            $this->_debug($function, 'Skipping unsharp mask because gd_version is "' . $this->gd_version() . '"', __FILE__, __LINE__);
            return false;
        }
    }

    function applyUnsharpMask(&$img, $amount, $radius, $threshold)
    {
        // $img is an image that is already created within php using
        // imgcreatetruecolor. No url! $img must be a truecolor image.
        // Attempt to calibrate the parameters to Photoshop:
        $amount = min($amount, 500);
        $amount = $amount * 0.016;
        if ($amount == 0){
            return true;
        }

        $radius = min($radius, 50);
        $radius = $radius * 2;

        $threshold = min($threshold, 255);

        $radius = abs(round($radius)); // Only integers make sense.
        if ($radius == 0){
            return true;
        }

        $w = ImageSX($img);
        $h = ImageSY($img);
        $imgCanvas = ImageCreateTrueColor($w, $h);
        $imgCanvas2 = ImageCreateTrueColor($w, $h);
        $imgBlur = ImageCreateTrueColor($w, $h);
        $imgBlur2 = ImageCreateTrueColor($w, $h);
        ImageCopy($imgCanvas, $img, 0, 0, 0, 0, $w, $h);
        ImageCopy($imgCanvas2, $img, 0, 0, 0, 0, $w, $h);
        // Gaussian blur matrix:
        // 1    2    1
        // 2    4    2
        // 1    2    1
        // ////////////////////////////////////////////////
        // Move copies of the image around one pixel at the time and merge them with weight
        // according to the matrix. The same matrix is simply repeated for higher radii.
        for ($i = 0; $i < $radius; $i++){
            ImageCopy ($imgBlur, $imgCanvas, 0, 0, 1, 1, $w - 1, $h - 1); // up left
            ImageCopyMerge($imgBlur, $imgCanvas, 1, 1, 0, 0, $w, $h, 50); // down right
            ImageCopyMerge($imgBlur, $imgCanvas, 0, 1, 1, 0, $w - 1, $h, 33.33333); // down left
            ImageCopyMerge($imgBlur, $imgCanvas, 1, 0, 0, 1, $w, $h - 1, 25); // up right
            ImageCopyMerge($imgBlur, $imgCanvas, 0, 0, 1, 0, $w - 1, $h, 33.33333); // left
            ImageCopyMerge($imgBlur, $imgCanvas, 1, 0, 0, 0, $w, $h, 25); // right
            ImageCopyMerge($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 20); // up
            ImageCopyMerge($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 16.666667); // down
            ImageCopyMerge($imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h, 50); // center
            ImageCopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);
            // During the loop above the blurred copy darkens, possibly due to a roundoff
            // error. Therefore the sharp picture has to go through the same loop to
            // produce a similar image for comparison. This is not a good thing, as processing
            // time increases heavily.
            ImageCopy ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h);
            ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
            ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
            ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
            ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
            ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
            ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 20);
            ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 16.666667);
            ImageCopyMerge($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
            ImageCopy ($imgCanvas2, $imgBlur2, 0, 0, 0, 0, $w, $h);
        }
        // Calculate the difference between the blurred pixels and the original
        // and set the pixels
        for ($x = 0; $x < $w; $x++){ // each row
            for ($y = 0; $y < $h; $y++){ // each pixel
                $rgbOrig = ImageColorAt($imgCanvas2, $x, $y);
                $rOrig = (($rgbOrig >> 16) &0xFF);
                $gOrig = (($rgbOrig >> 8) &0xFF);
                $bOrig = ($rgbOrig &0xFF);

                $rgbBlur = ImageColorAt($imgCanvas, $x, $y);
                $rBlur = (($rgbBlur >> 16) &0xFF);
                $gBlur = (($rgbBlur >> 8) &0xFF);
                $bBlur = ($rgbBlur &0xFF);
                // When the masked pixels differ less from the original
                // than the threshold specifies, they are set to their original value.
                $rNew = (abs($rOrig - $rBlur) >= $threshold) ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig)) : $rOrig;
                $gNew = (abs($gOrig - $gBlur) >= $threshold) ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig)) : $gOrig;
                $bNew = (abs($bOrig - $bBlur) >= $threshold) ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig)) : $bOrig;

                if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)){
                    $pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
                    ImageSetPixel($img, $x, $y, $pixCol);
                }
            }
        }
        ImageDestroy($imgCanvas);
        ImageDestroy($imgCanvas2);
        ImageDestroy($imgBlur);
        ImageDestroy($imgBlur2);

        return true;
    }

    function Colorize(&$gdimg, $amount, $targetColor)
    {
        $amount = (is_numeric($amount) ? $amount : 25);
        $targetColor = ($this->IsHexColor($targetColor) ? $targetColor : 'gray');
        // overridden below for grayscale
        if ($targetColor != 'gray'){
            $TargetPixel['red'] = hexdec(substr($targetColor, 0, 2));
            $TargetPixel['green'] = hexdec(substr($targetColor, 2, 2));
            $TargetPixel['blue'] = hexdec(substr($targetColor, 4, 2));
        }

        for ($x = 0; $x < ImageSX($gdimg); $x++){
            for ($y = 0; $y < ImageSY($gdimg); $y++){
                $OriginalPixel = $this->GetPixelColor($gdimg, $x, $y);
                if ($targetColor == 'gray'){
                    $TargetPixel = $this->GrayscalePixel($OriginalPixel);
                }
                foreach ($TargetPixel as $key => $value){
                    $NewPixel[$key] = round(max(0, min(255, ($OriginalPixel[$key] * ((100 - $amount) / 100)) + ($TargetPixel[$key] * ($amount / 100)))));
                }
                // $newColor = phpthumb_functions::ImageColorAllocateAlphaSafe($gdimg, $NewPixel['red'], $NewPixel['green'], $NewPixel['blue'], $OriginalPixel['alpha']);
                $newColor = ImageColorAllocate($gdimg, $NewPixel['red'], $NewPixel['green'], $NewPixel['blue']);
                ImageSetPixel($gdimg, $x, $y, $newColor);
            }
        }
        return true;
    }

    function HistogramAnalysis(&$gdimg, $calculateGray = false)
    {
        $ImageSX = ImageSX($gdimg);
        $ImageSY = ImageSY($gdimg);
        for ($x = 0; $x < $ImageSX; $x++){
            for ($y = 0; $y < $ImageSY; $y++){
                $OriginalPixel = $this->GetPixelColor($gdimg, $x, $y);
                @$Analysis['red'][$OriginalPixel['red']]++;
                @$Analysis['green'][$OriginalPixel['green']]++;
                @$Analysis['blue'][$OriginalPixel['blue']]++;
                @$Analysis['alpha'][$OriginalPixel['alpha']]++;
                if ($calculateGray){
                    $GrayPixel = $this->GrayscalePixel($OriginalPixel);
                    @$Analysis['gray'][$GrayPixel['red']]++;
                }
            }
        }
        $keys = array('red', 'green', 'blue', 'alpha');
        if ($calculateGray){
            $keys[] = 'gray';
        }
        foreach ($keys as $key){
            ksort($Analysis[$key]);
        }
        return $Analysis;
    }

    function WhiteBalance(&$gdimg, $targetColor = '')
    {
        if ($this->IsHexColor($targetColor)){
            $targetPixel = array('red' => hexdec(substr($targetColor, 0, 2)),
                'green' => hexdec(substr($targetColor, 2, 2)),
                'blue' => hexdec(substr($targetColor, 4, 2))
                );
        }else{
            $Analysis = $this->HistogramAnalysis($gdimg, false);
            $targetPixel = array('red' => max(array_keys($Analysis['red'])),
                'green' => max(array_keys($Analysis['green'])),
                'blue' => max(array_keys($Analysis['blue']))
                );
        }
        $grayValue = $this->GrayscaleValue($targetPixel['red'], $targetPixel['green'], $targetPixel['blue']);
        $scaleR = $grayValue / $targetPixel['red'];
        $scaleG = $grayValue / $targetPixel['green'];
        $scaleB = $grayValue / $targetPixel['blue'];

        for ($x = 0; $x < ImageSX($gdimg); $x++){
            for ($y = 0; $y < ImageSY($gdimg); $y++){
                $currentPixel = $this->GetPixelColor($gdimg, $x, $y);
                $newColor = $this->ImageColorAllocateAlphaSafe($gdimg,
                    max(0, min(255, round($currentPixel['red'] * $scaleR))),
                    max(0, min(255, round($currentPixel['green'] * $scaleG))),
                    max(0, min(255, round($currentPixel['blue'] * $scaleB))),
                    $currentPixel['alpha']
                    );
                ImageSetPixel($gdimg, $x, $y, $newColor);
            }
        }
        return true;
    }

    function AutoContrast(&$gdimg, $band = '*', $min = -1, $max = -1)
    {
        // equivalent of "Auto Contrast" in Adobe Photoshop
        $Analysis = $this->HistogramAnalysis($gdimg, true);
        $keys = array('r' => 'red', 'g' => 'green', 'b' => 'blue', 'a' => 'alpha', '*' => 'gray');
        if (!isset($keys[$band])){
            return false;
        }
        $key = $keys[$band];
        // If the absolute brightest and darkest pixels are used then one random
        // pixel in the image could throw off the whole system. Instead, count up/down
        // from the limit and allow 0.1% of brightest/darkest pixels to be clipped to min/max
        $clip_threshold = ImageSX($gdimg) * ImageSX($gdimg) * 0.001;
        if ($min >= 0){
            $range_min = min($min, 255);
        }else{
            $countsum = 0;
            for ($i = 0; $i <= 255; $i++){
                $countsum += @$Analysis[$key][$i];
                if ($countsum >= $clip_threshold){
                    $range_min = $i - 1;
                    break;
                }
            }
            $range_min = max($range_min, 0);
        }
        if ($max >= 0){
            $range_max = max($max, 255);
        }else{
            $countsum = 0;
            $threshold = ImageSX($gdimg) * ImageSX($gdimg) * 0.001; // 0.1% of brightest and darkest pixels can be clipped
            for ($i = 255; $i >= 0; $i--){
                $countsum += @$Analysis[$key][$i];
                if ($countsum >= $clip_threshold){
                    $range_max = $i + 1;
                    break;
                }
            }
            $range_max = min($range_max, 255);
        }
        $range_scale = (($range_max == $range_min) ? 1 : (255 / ($range_max - $range_min)));
        if (($range_min == 0) && ($range_max == 255)){
            // no adjustment neccesary - don't waste CPU time!
            return true;
        }

        $ImageSX = ImageSX($gdimg);
        $ImageSY = ImageSY($gdimg);
        for ($x = 0; $x < $ImageSX; $x++){
            for ($y = 0; $y < $ImageSY; $y++){
                $OriginalPixel = $this->GetPixelColor($gdimg, $x, $y);
                if ($band == '*'){
                    $new['red'] = min(255, max(0, ($OriginalPixel['red'] - $range_min) * $range_scale));
                    $new['green'] = min(255, max(0, ($OriginalPixel['green'] - $range_min) * $range_scale));
                    $new['blue'] = min(255, max(0, ($OriginalPixel['blue'] - $range_min) * $range_scale));
                    $new['alpha'] = min(255, max(0, ($OriginalPixel['alpha'] - $range_min) * $range_scale));
                }else{
                    $new = $OriginalPixel;
                    $new[$key] = min(255, max(0, ($OriginalPixel[$key] - $range_min) * $range_scale));
                }
                $newColor = $this->ImageColorAllocateAlphaSafe($gdimg, $new['red'], $new['green'], $new['blue'], $new['alpha']);
                ImageSetPixel($gdimg, $x, $y, $newColor);
            }
        }

        return true;
    }

    function GetTempName()
    {
        return tempnam($this->temp_directory, 'temp_thumb');
    }
} ?>
