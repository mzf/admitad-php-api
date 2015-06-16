<?php

namespace Admitad\Api;

class Object extends \ArrayObject
{

    /**
     * Extend with array function as methods
     * @param type $func
     * @param type $argv
     * @return type
     * @throws BadMethodCallException
     */
    public function __call($func, $argv)
    {
        if (!is_callable($func) || substr($func, 0, 6) !== 'array_')
        {
            throw new \BadMethodCallException(__CLASS__ . '->' . $func);
        }
        return call_user_func_array($func, array_merge(array($this->getArrayCopy()), $argv));
    }

    public function __construct($data = array())
    {
        if (is_array($data))
        {
            foreach ($data as $key => $value)
            {
                if (is_array($value))
                {
                    $value = new Object($value);
                }
                $this[$key] = $value;
            }
        }
    }

    /**
     * Возвращает путь до корневого элемента
     */
    public function unshift($value)
    {
        $tmp = $this->getArrayCopy();
        $tmp = array($value) + $tmp;
        $this->exchangeArray($tmp);
        return $this;
    }

    /**
     * Возвращает путь до корневого элемента
     */
    public function getPath($name = 'id', $sep = '/', $path = '')
    {
        if (empty($path))
        {
            $path = $sep;
        }

        foreach ($this as $key => $item)
        {
            if (is_a($item, __CLASS__))
            {
                $path = $item->getPath($name, $sep, $path);
            } elseif ($key === $name)
            {
                $path .= $item . $sep;
            }
        }

        return $path;
    }

    public function toArray()
    {
        $arr = [];
        foreach ($this as $key => $item)
        {
            if (is_a($item, __CLASS__))
            {
                $arr[$key] = $item->toArray();
            } else
            {
                $arr[$key] = $item;
            }
        }

        return $arr;
    }

    public function __get($key)
    {
        return $this[$key];
    }

    public function offsetGet($key)
    {
        return $this->offsetExists($key) ? parent::offsetGet($key) : null;
    }

}
