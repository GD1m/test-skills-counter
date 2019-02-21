<?php declare(strict_types=1);

/**
 * Class Counter
 */
final class Counter
{
    /**
     * @var string
     */
    private $filename = './counter.txt';

    /**
     * @var resource
     */
    private $handle;

    public function __construct()
    {
        $this->handle = fopen($this->filename, 'a+');
    }

    public function __destruct()
    {
        flock($this->handle, LOCK_UN);

        fclose($this->handle);
    }

    /**
     * @return string
     */
    public function increase(): string
    {
        $this->setLock();

        $count = $this->getCurrentCount();

        return $this->rewrite(
            $this->bigSum($count, '1')
        );
    }

    private function setLock(): void
    {
        if (!flock($this->handle, LOCK_EX)) {
            throw new RuntimeException('Exclusive locking failed');
        }
    }

    /**
     * @return string
     */
    private function getCurrentCount(): string
    {
        if ($fileSize = filesize($this->filename)) {
            $count = fread($this->handle, $fileSize);
        } else {
            $count = '0';
        }

        return $count;
    }

    /**
     * @param string $count
     * @return string
     */
    private function rewrite(string $count): string
    {
        ftruncate($this->handle, 0);

        fwrite($this->handle, $count);

        return $count;
    }

    /**
     * @param string $x
     * @param string $y
     * @return string
     */
    private function bigSum(string $x, string $y): string
    {
        return bcadd($x, $y);
    }
}
