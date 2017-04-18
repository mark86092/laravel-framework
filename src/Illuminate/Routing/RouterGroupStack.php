<?php

namespace Illuminate\Routing;

use Illuminate\Support\Arr;

class RouterGroupStack
{
    protected $stack = [];

    public function get()
    {
        return $this->stack;
    }

    public function has()
    {
        return ! empty($this->stack);
    }

    public function pop()
    {
        return array_pop($this->stack);
    }
    /**
     * Update the group stack with the given attributes.
     *
     * @param  array  $attributes
     */
    public function push(array $attributes)
    {
        $this->stack[] = $attributes;
    }
    public function getLast($key = null, $default = null)
    {
        if (is_null($key)) {
            return end($this->stack);
        }

        if ($this->has()) {
            $last = end($this->stack);

            return isset($last[$key]) ? $last[$key] : $default;
        }

        return $default;
    }

    /**
     * Merge the given group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     *
     * @return array
     */
    public static function mergeGroup($new, $old)
    {
        $new['namespace'] = static::formatUsesPrefix($new, $old);

        $new['prefix'] = static::formatGroupPrefix($new, $old);

        if (isset($new['domain'])) {
            unset($old['domain']);
        }

        $new['where'] = array_merge(
            isset($old['where']) ? $old['where'] : [],
            isset($new['where']) ? $new['where'] : []
        );

        if (isset($old['as'])) {
            $new['as'] = $old['as'].(isset($new['as']) ? $new['as'] : '');
        }

        return array_merge_recursive(Arr::except($old, ['namespace', 'prefix', 'where', 'as']), $new);
    }
    /**
     * Format the uses prefix for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     *
     * @return string|null
     */
    protected static function formatUsesPrefix($new, $old)
    {
        if (isset($new['namespace'])) {
            return isset($old['namespace'])
                    ? trim($old['namespace'], '\\').'\\'.trim($new['namespace'], '\\')
                    : trim($new['namespace'], '\\');
        }

        return isset($old['namespace']) ? $old['namespace'] : null;
    }

    /**
     * Format the prefix for the new group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     *
     * @return string|null
     */
    protected static function formatGroupPrefix($new, $old)
    {
        $oldPrefix = isset($old['prefix']) ? $old['prefix'] : null;

        if (isset($new['prefix'])) {
            return trim($oldPrefix, '/').'/'.trim($new['prefix'], '/');
        }

        return $oldPrefix;
    }
}
