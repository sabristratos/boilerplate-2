<?php

namespace App\Services\ResourceSystem\Columns;

class ImageColumn extends Column
{
    /**
     * The width of the image.
     *
     * @var int
     */
    protected $width = 50;

    /**
     * The height of the image.
     *
     * @var int
     */
    protected $height = 50;

    /**
     * Whether to make the image circular.
     *
     * @var bool
     */
    protected $circular = false;

    /**
     * Set the width of the image.
     *
     * @return $this
     */
    public function width(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Set the height of the image.
     *
     * @return $this
     */
    public function height(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Set the size of the image.
     *
     * @return $this
     */
    public function size(int $size): static
    {
        $this->width = $size;
        $this->height = $size;

        return $this;
    }

    /**
     * Make the image circular.
     *
     * @return $this
     */
    public function circular(bool $value = true): static
    {
        $this->circular = $value;

        return $this;
    }

    /**
     * Get the width of the image.
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Get the height of the image.
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Determine if the image is circular.
     */
    public function isCircular(): bool
    {
        return $this->circular;
    }

    /**
     * Get the component name for the column.
     */
    public function component(): string
    {
        return 'resource-system::columns.image-column';
    }

    /**
     * Get the column's attributes.
     */
    public function getAttributes(): array
    {
        return array_merge(parent::getAttributes(), [
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
            'circular' => $this->isCircular(),
        ]);
    }
}
