<?php


namespace Amap\Result\Base;


use Amap\Kernel\Exceptions\InvalidIndexException;

class SimpleData implements \ArrayAccess
{
    public function __construct($data)
    {
        $this->parseData($data);
    }

    protected function parseData($data)
    {
        foreach (get_object_vars($this) as $c => $v) {
            if (is_null($this->{$c})) {
                $this->{$c} = array_key_exists($c, $data) && !is_array($data[$c]) ? $data[$c] : null;
            }
        }
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     * @throws InvalidIndexException
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->{$offset};
        } else {
            throw new InvalidIndexException();
        }
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws InvalidIndexException
     */
    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            return $this->{$offset} = $value;
        } else {
            throw new InvalidIndexException();
        }
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->{$offset} = null;
        }
    }
}