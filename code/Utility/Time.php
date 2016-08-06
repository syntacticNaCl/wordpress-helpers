<?php
namespace Zawntech\WordPress\Utility;

class Time
{
    /**
     * @param $seconds
     * @return array
     */
    public static function parseSeconds($seconds)
    {
        // extract hours
        $hours = floor($seconds / (60 * 60));

        // extract minutes
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);

        // extract the remaining seconds
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);

        // return the final array
        return [
            'h' => (int) $hours,
            'm' => (int) $minutes,
            's' => (int) $seconds,
        ];
    }

    /**
     * Parses seconds into a human readable string; ie: 4 hours, 20 minutes.
     * @param $seconds
     * @return string
     */
    public static function humanize($seconds)
    {
        // Parse the seconds.
        $timeData = static::parseSeconds($seconds);

        // Declare output string.
        $output = '';

        if ( $timeData['h'] > 0 )
        {
            $output .= $timeData . ' ' . ( $timeData['h'] === 1 ? 'hour' : 'hours' ) . ' ';
        }

        if ( $timeData['m'] > 0 )
        {
            $output .= $timeData . ' ' . ( $timeData['m'] === 1 ? 'minute' : 'minutes' ) . ' ';
        }

        if ( $timeData['s'] > 0 )
        {
            $output .= $timeData . ' ' . ( $timeData['s'] === 1 ? 'second' : 'seconds' ) . ' ';
        }

        return $output;
    }
}