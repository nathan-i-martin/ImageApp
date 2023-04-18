<?php
class FileSize {
    /**
     * Convert from bytes to kilobytes
     * @param int $bytes The bytes to be converted.
     * @return int The bytes in KB format.
     */
    static function toKB(int $bytes) {
        return $bytes / 1000;
    }
    
    /**
     * Convert from bytes to megabytes
     * @param int $bytes The bytes to be converted.
     * @return int The bytes in MB format.
     */
    static function toMB(int $bytes) {
        return $bytes / 1000000;
    }
    
    /**
     * Convert from bytes to gigabytes
     * @param int $bytes The bytes to be converted.
     * @return int The bytes in GB format.
     */
    static function toGB(int $bytes) {
        return $bytes / 1000000000;
    }
    
    /**
     * Convert from bytes to terabytes
     * @param int $bytes The bytes to be converted.
     * @return int The bytes in TB format.
     */
    static function toTB(int $bytes) {
        return $bytes / 1000000000000;
    }
    
    /**
     * Convert from bytes to petabytes
     * @param int $bytes The bytes to be converted.
     * @return int The bytes in PB format.
     */
    static function toPB(int $bytes) {
        return $bytes / 1000000000000000;
    }
    
    /**
     * Convert from kilobytes to bytes.
     * @param int $kilobytes The kilobytes to be converted.
     * @return int The kilobytes in byte format.
     */
    static function fromKB(int $kilobytes) {
        return $kilobytes * 1000;
    }
    
    /**
     * Convert from megabytes to bytes.
     * @param int $megabytes The megabytes to be converted.
     * @return int The megabytes in byte format.
     */
    static function fromMB(int $megabytes) {
        return $megabytes * 1000000;
    }
    
    /**
     * Convert from gigabytes to bytes.
     * @param int $gigabytes The gigabytes to be converted.
     * @return int The gigabytes in byte format.
     */
    static function fromGB(int $gigabytes) {
        return $gigabytes * 1000000000;
    }
    
    /**
     * Convert from terabytes to bytes.
     * @param int $terabytes The terabytes to be converted.
     * @return int The terabytes in byte format.
     */
    static function fromTB(int $terabytes) {
        return $terabytes * 1000000000000;
    }
    
    /**
     * Convert from bytes to petabytes
     * Convert from petabytes to bytes.
     * @param int $petabytes The petabytes to be converted.
     * @return int The petabytes in byte format.
     */
    static function fromPB(int $petabytes) {
        return $petabytes * 1000000000000000;
    }
}