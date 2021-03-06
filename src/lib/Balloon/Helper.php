<?php
declare(strict_types=1);

/**
 * Balloon
 *
 * @author      Raffael Sahli <sahli@gyselroth.net>
 * @copyright   Copryright (c) 2012-2017 gyselroth GmbH (https://gyselroth.com)
 * @license     GPLv3 https://opensource.org/licenses/GPL-3.0
 */

namespace Balloon;

use \Balloon\Exception;
use \MongoDB\BSON\UTCDateTime;
use \MongoDB\Model\BSONArray;
use \MongoDB\Model\BSONDocument;
use \Stdclass;

class Helper
{
    /**
     * Convert BSONDocument array to php
     *
     * @param   array|BSONArray|BSONDocument $doc
     * @return  array|BSONArray|BSONDocument
     */
    public static function convertBSONDocToPhp($doc)
    {
        if (is_array($doc)) {
            return $doc;
        }

        foreach ($doc as $attr => $value) {
            if ($value instanceof BSONArray || $value instanceof BSONDocument) {
                $doc[$attr] = $value->getArrayCopy();
            } else {
                $doc[$attr] = $value;
            }
        }

        return (array)$doc;
    }


    /**
     * Convert UTCDateTime to unix ts
     *
     * @param  UTCDateTime $date
     * @return StdClass
     */
    public static function DateTimeToUnix(?UTCDateTime $date): ?Stdclass
    {
        if ($date === null) {
            return null;
        }

        $date = $date->toDateTime();
        $ts = new StdClass();
        $ts->sec = $date->format('U');
        $ts->usec = $date->format('u');
        return $ts;
    }


    /**
     * Search array element
     *
     * @param   mixed $values
     * @param   mixed $key
     * @param   array $array
     * @return  void
     */
    public static function searchArray($value, $key, array $array)
    {
        foreach ($array as $k => $val) {
            if ($val[$key] == $value) {
                return $k;
            }
        }

        return null;
    }


    /**
     * Filter data
     *
     * @param  mixed $data
     * @return mixed
     */
    public static function filter($data)
    {
        if (is_array($data)) {
            foreach ($data as &$elem) {
                $elem = self::filter($elem);
            }
        } else {
            $data = strip_tags($data);
        }
 
        return $data;
    }


    /**
     * Escape data
     *
     * @param  mixed $data
     * @return mixed
     */
    public static function escape($data)
    {
        if (is_array($data)) {
            foreach ($data as &$elem) {
                $elem = self::escape($elem);
            }
        } elseif (is_string($data)) {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }
 
        return $data;
    }


    /**
     * Bool param (string => bool)
     *
     * @param   string|bool $param
     * @return  bool
     */
    public static function boolParam($param): bool
    {
        if ($param == 'true' || $param == 1) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Check if param is a valid unix timestamp
     *
     * @param  string $timestamp
     * @return bool
     */
    public static function isValidTimeStamp(string $timestamp): bool
    {
        return ((string) (int) $timestamp === $timestamp)
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }
}
