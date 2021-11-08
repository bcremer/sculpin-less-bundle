<?php

namespace Bcremer\Sculpin\Bundle\LessBundle;

use Sculpin\Core\Event\SourceSetEvent;
use Sculpin\Core\Sculpin;
use Sculpin\Core\Source\AbstractSource;
use Sculpin\Core\Source\FileSource;
use Sculpin\Core\Source\MemorySource;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LessConverter implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private array $extensions;

    /**
     * @var string[]
     */
    private array $files;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Sculpin::EVENT_BEFORE_RUN => 'beforeRun',
        ];
    }

    /**
     * @param string[] $extensions
     * @param string[] $files
     */
    public function __construct(array $extensions = [], array $files = [])
    {
        $this->extensions = $extensions;
        $this->files = $files;
    }

    /**
     * Before run
     *
     * @param SourceSetEvent $sourceSetEvent Source Set Event
     */
    public function beforeRun(SourceSetEvent $sourceSetEvent): void
    {
        $sourceSet = $sourceSetEvent->sourceSet();

        /** @var FileSource $source */
        foreach ($sourceSetEvent->updatedSources() as $source) {

            if (!$this->shouldBeConverted($source)) {
                continue;
            }

            $source->setShouldBeSkipped();

            $css = $this->parseLess($source->file()->getPathname());
            if (!$css) {
                continue;
            }

            $generatedSource = $this->createDuplicate($source);
            $generatedSource->setContent($css);

            $sourceSet->mergeSource($generatedSource);
        }
    }

    /**
     * @param AbstractSource $source
     * @return bool
     */
    private function shouldBeConverted(AbstractSource $source): bool
    {
        // File based whitelist has precedence
        if (!empty($this->files)) {
            foreach ($this->files as $fileName) {
                if ($source->relativePathname() === $fileName) {
                    return true;
                }
            }

            return false;
        }

        foreach ($this->extensions as $extension) {
            if (fnmatch("*.{$extension}", $source->filename())) {
                return true;
            }
        }

        return false;
    }

    private function replaceFileExtension(string $fileName): string
    {
        return str_replace('.less', '.css', $fileName);
    }

    private function createDuplicate(FileSource $source): MemorySource
    {
        $options = [
            'filename' => $this->replaceFileExtension($source->filename()),
            'relativePathname' => $this->replaceFileExtension($source->relativePathname()),
        ];

        $generatedSource = $source->duplicate(
            $source->sourceId() . ':' . 'css',
            $options
        );

        $generatedSource->setIsGenerated();

        return $generatedSource;
    }

    /**
     * @param string $filename
     * @return string
     */
    private function parseLess(string $filename): string
    {
        $options = ['compress' => true];
        $parser = new \Less_Parser($options);

        $parser->parseFile($filename);

        return trim($parser->getCss());
    }
}
