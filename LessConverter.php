<?php
namespace Bcremer\Sculpin\Bundle\LessBundle;

use Sculpin\Core\Event\SourceSetEvent;
use Sculpin\Core\Sculpin;
use Sculpin\Core\Source\FileSource;
use Sculpin\Core\Source\MemorySource;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LessConverter implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private $extensions = [];

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Sculpin::EVENT_BEFORE_RUN => 'beforeRun',
        );
    }

    /**
     * @param string[] $extensions
     */
    public function __construct(array $extensions = [])
    {
        $this->extensions = $extensions;
    }

    /**
     * Before run
     *
     * @param SourceSetEvent $sourceSetEvent Source Set Event
     */
    public function beforeRun(SourceSetEvent $sourceSetEvent)
    {
        $sourceSet = $sourceSetEvent->sourceSet();

        /** @var FileSource $source */
        foreach ($sourceSetEvent->updatedSources() as $source) {
            foreach ($this->extensions as $extension) {
                if (!fnmatch("*.{$extension}", $source->filename())) {
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
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function replaceFileExtension($fileName)
    {
        return str_replace('.less', '.css', $fileName);
    }

    /**
     * @param FileSource $source
     * @return MemorySource
     */
    private function createDuplicate(FileSource $source)
    {
        $options = [
            'filename'         => $this->replaceFileExtension($source->filename()),
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
    private function parseLess($filename)
    {
        $parser = new \Less_Parser();
        $parser->parseFile($filename);

        return trim($parser->getCss());
    }
}
