<?php

namespace Z38\SwissPayment\Money;

/**
 * Base class for all currencies.
 */
abstract class Money
{
    /**
     * @var int
     */
    protected $cents;

    /**
     * Constructor.
     *
     * @param int $cents Amount of money in cents.
     */
    public function __construct($cents)
    {
        $this->cents = intval($cents);
    }

    /**
     * Gets the currency code
     *
     * @return string|null An ISO 4217 currency code or null if currency is not known
     */
    abstract public function getCurrency();

    /**
     * Gets the number of decimals
     *
     * @return int
     */
    abstract protected function getDecimals();

    /**
     * Returns a formatted string (e.g. 15.560)
     *
     * @return string The formatted value
     */
    public function format()
    {
        $base = pow(10, $this->getDecimals());
        $sign = ($this->cents < 0 ? '-' : '');

        return sprintf('%s%d.%0'.$this->getDecimals().'d', $sign, intval(abs($this->cents) / $base), abs($this->cents) % $base);
    }

    /**
     * Returns the amount of money in cents
     *
     * @return string The amount in cents
     */
    public function getAmount()
    {
        return $this->cents;
    }

    /**
     * Returns the sum of this and an other amount of money
     *
     * @param Money $addend The addend
     *
     * @return Money The sum
     *
     * @throws \InvalidArgumentException When the currencies do not match
     */
    public function plus(Money $addend)
    {
        if ($this->getCurrency() !== $addend->getCurrency()) {
            throw new \InvalidArgumentException('Can not add different currencies');
        }

        return new static($this->cents + $addend->cents);
    }

    /**
     * Returns the subtraction of this and an other amount of money
     *
     * @param Money $subtrahend The subtrahend
     *
     * @return Money The difference
     *
     * @throws \InvalidArgumentException When the currencies do not match
     */
    public function minus(Money $subtrahend)
    {
        if ($this->getCurrency() !== $subtrahend->getCurrency()) {
            throw new \InvalidArgumentException('Can not subtract different currencies');
        }

        return new static($this->cents - $subtrahend->cents);
    }

    /**
     * Compares this instance with an other instance.
     *
     * @param Money $b The instance to which this instance is to be compared.
     *
     * @return int -1, 0 or 1 as this instance is less than, equal to, or greater than $b
     *
     * @throws \InvalidArgumentException When the currencies do not match
     */
    public function compareTo(Money $b)
    {
        if ($this->getCurrency() !== $b->getCurrency()) {
            throw new \InvalidArgumentException('Can not compare different currencies');
        }

        if ($this->getAmount() < $b->getAmount()) {
            return -1;
        } elseif ($this->getAmount() == $b->getAmount()) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Returns true if the argument contains the same amount and the same currency.
     *
     * @param object $obj
     *
     * @return bool True if $obj is equal to this instance
     */
    public function equals($obj)
    {
        if (!($obj instanceof Money)) {
            return false;
        }

        if ($this->getCurrency() !== $obj->getCurrency()) {
            return false;
        }

        return ($this->getAmount() == $obj->getAmount());
    }
}
