<?php

namespace WorldTP;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getLogger()->info(TF::GREEN . "" );
    }

    public function onDisable(): void {
        $this->getLogger()->info(TF::RED . "");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TF::RED . "This command can only be used in the game.");
            return false;
        }

        if ($command->getName() === "wtp") {
            if (count($args) < 1) {
                $sender->sendMessage(TF::YELLOW . "Usage: /wtp <world_name>");
                return false;
            }

            $worldName = $args[0];
            $worldManager = $this->getServer()->getWorldManager();

           
            if (!$worldManager->isWorldLoaded($worldName)) {
                if (!$worldManager->loadWorld($worldName)) {
                    $sender->sendMessage(TF::RED . "The world '$worldName' does not exist or could not be loaded.");
                    return false;
                }
            }

            $world = $worldManager->getWorldByName($worldName);
            if ($world instanceof World) {
                $sender->teleport($world->getSpawnLocation());
                $sender->sendMessage(TF::GREEN . "Teleported to world '$worldName'!");
            } else {
                $sender->sendMessage(TF::RED . "There was an error loading world '$worldName'.");
            }

            return true;
        }

        return false;
    }
}
