<?php

namespace Shell\Tokenizer;

enum Token
{
    case COMMAND;
    case ARGUMENT;
    case PIPE;
}
