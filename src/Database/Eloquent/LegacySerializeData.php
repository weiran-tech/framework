<?php
declare(strict_types = 1);

namespace Weiran\Framework\Database\Eloquent;

/**
 * 使用旧的序列化格式
 */
trait LegacySerializeData
{
    /**
     * Prepare a date for array / JSON serialization.
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}