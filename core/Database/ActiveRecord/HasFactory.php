<?php

namespace Core\Database\ActiveRecord;

interface HasFactory
{
    public static function factory(): self;
}
