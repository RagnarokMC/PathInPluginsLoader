<?php

declare(strict_types=1);

namespace skh6075\PathInPluginsLoader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use Webmozart\PathUtil\Path;

final class Loader extends PluginBase{
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		static $logger = null;
		if($logger === null){
			$logger = $this->getServer()->getLogger();
		}

		if(count($args) < 1){
			throw new InvalidCommandSyntaxException();
		}

		$path = Path::join($this->getServer()->getDataPath(), ...$args);
		if(!is_dir($path)){
			$sender->sendMessage(TextFormat::RED . "The path you entered does not exist.");
			return false;
		}

		$logger->debug("Check all plugins inside the path folder you entered. path: $path");

		$loadErrorCount = 0;
		$loadPlugins = $this->getServer()->getPluginManager()->loadPlugins($path, $loadErrorCount);
		$logger->info(TextFormat::GREEN . "Successfully loaded all plugins in the path.");
		$logger->info(TextFormat::AQUA . "Scanned path : $path");
		$logger->info(TextFormat::AQUA . "success : " . count($loadPlugins) . ", failure : $loadErrorCount");
		if(count($loadPlugins) > 0){
			$logger->info(TextFormat::WHITE . "Load plugins : " . implode(', ', array_map(static fn(Plugin $plugin): string => $plugin->getName(), $loadPlugins)));
			foreach($loadPlugins as $plugin){
				$this->getServer()->getPluginManager()->enablePlugin($plugin);
			}
		}
		return true;
	}
}