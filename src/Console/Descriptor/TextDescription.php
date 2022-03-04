<?php

namespace Shell\Console\Descriptor;

use Shell\Console\Command\AliasAwareInterface;
use Shell\Console\Command\UsageAwareInterface;
use Shell\Console\Command\UserLandCommandInterface;
use Symfony\Component\Console\Descriptor\TextDescriptor;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\OutputInterface;

class TextDescription extends TextDescriptor
{
    public function describe(OutputInterface $output, object $object, array $options = [])
    {
        $this->output = $output;

        if ($object instanceof UserLandCommandInterface) {
            $this->describeShellCommand($object);
        } else {
            parent::describe($output, $object, $options);
        }
    }

    private function describeShellCommand(UserLandCommandInterface $command)
    {
        if ($description = $command->getDescription()) {
            $this->writeText('<comment>Description:</comment>');
            $this->writeText("\n");
            $this->writeText('  '.$description);
            $this->writeText("\n");
        }

        if ($command instanceof UsageAwareInterface) {
            $this->writeText("\n<comment>Usage:</comment>");
            foreach ($command->getUsages() as $usage) {
                $this->writeText("\n");
                $this->writeText('  '.OutputFormatter::escape($usage));
            }
            $this->writeText("\n");
        }

        if ($command instanceof AliasAwareInterface) {
            $this->writeText("\n<comment>Aliases:</comment>");
            foreach ($command->getAliases() as $alias) {
                $this->writeText("\n");
                $this->writeText('  ' . $alias);
            }
            $this->writeText("\n");
        }

        $definition = $command->getDefinition();
        if ($definition->getOptions() || $definition->getArguments()) {
            $this->writeText("\n");
            $this->describeInputDefinition($definition);
            $this->writeText("\n");
        }
    }

    /**
     * {@inheritdoc}
     */
    private function writeText(string $content, array $options = [])
    {
        $this->write(
            isset($options['raw_text']) && $options['raw_text'] ? strip_tags($content) : $content,
            true
        );
    }
}
