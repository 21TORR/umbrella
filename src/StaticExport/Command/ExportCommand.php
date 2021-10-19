<?php declare(strict_types=1);

namespace Torr\Umbrella\StaticExport\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Torr\Rad\Command\TorrCliStyle;
use Torr\Umbrella\StaticExport\Exception\ExportFailedException;
use Torr\Umbrella\StaticExport\Exporter\StaticSiteExporter;

final class ExportCommand extends Command
{
	protected static $defaultName = "umbrella:export";
	private StaticSiteExporter $exporter;

	/**
	 * @inheritDoc
	 */
	public function __construct (StaticSiteExporter $exporter)
	{
		parent::__construct(null);
		$this->exporter = $exporter;
	}


	/**
	 * @inheritDoc
	 */
	protected function configure () : void
	{
		$this
			->setDescription("Exports the umbrella project as static site.");
	}

	/**
	 * @inheritDoc
	 */
	protected function execute (
		InputInterface $input,
		OutputInterface $output
	) : int
	{
		$io = new TorrCliStyle($input, $output);
		$io->title("Umbrella: Static Export");

		try
		{
			$this->exporter->export($io);
			$io->success("Export finished.");

			return 0;
		}
		catch (ExportFailedException $exception)
		{
			$io->error("Export failed: {$exception->getMessage()}");

			return 1;
		}
	}
}
