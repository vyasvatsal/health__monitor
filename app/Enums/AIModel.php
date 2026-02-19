<?php

namespace App\Enums;

enum AIModel: string
{
    case GROQ_LLAMA_3_70B = 'llama-3.3-70b-versatile';
    case GROQ_MIXTRAL_8X7B = 'mixtral-8x7b-32768';
    case GROQ_GEMMA_7B = 'gemma-7b-it';

    public static function default(): self
    {
        return self::GROQ_LLAMA_3_70B;
    }
}
