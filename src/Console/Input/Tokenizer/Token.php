<?php

namespace Shell\Console\Input\Tokenizer;

enum Token
{
    case COMMAND;
    case ARGUMENT;
    case PIPE;
    case GREATER;
    case LESS;
}
