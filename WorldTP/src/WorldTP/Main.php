<?php

declare(strict_types=1);

namespace WorldTP;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\world\WorldManager;

class Main extends PluginBase {

    /** @var array */
    private array $messages = [];

    public function onEnable() : void {
        // Establece el idioma predeterminado
        $this->setLanguage("es");

        // Mensaje de consola al habilitar el plugin
        $this->getLogger()->info($this->messages["plugin_enabled"]);
    }

    public function onDisable() : void {
        // Mensaje de consola al deshabilitar el plugin
        $this->getLogger()->info($this->messages["plugin_disabled"]);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if ($command->getName() === "wtp") {
            if ($sender instanceof Player) {
                if (isset($args[0])) {
                    $worldName = $args[0];
                    $worldManager = $this->getServer()->getWorldManager();

                    // Verificar si el mundo ya está generado
                    if ($worldManager->isWorldGenerated($worldName)) {
                        // Si el mundo no está cargado, lo cargamos
                        if (!$worldManager->isWorldLoaded($worldName)) {
                            $worldManager->loadWorld($worldName);
                        }

                        // Obtener el mundo al que queremos teletransportar
                        $world = $worldManager->getWorldByName($worldName);

                        if ($world instanceof World) {
                            // Establecemos una posición en el mundo de destino
                            $spawnPosition = $world->getSafeSpawn();

                            // Teletransportar al jugador
                            $sender->teleport($spawnPosition);
                            $sender->sendMessage(str_replace("%world%", $worldName, $this->messages["teleport_success"]));
                        } else {
                            $sender->sendMessage(str_replace("%world%", $worldName, $this->messages["world_not_found"]));
                        }
                    } else {
                        $sender->sendMessage(str_replace("%world%", $worldName, $this->messages["world_not_generated"]));
                    }
                } else {
                    $sender->sendMessage($this->messages["usage"]);
                }
            } else {
                $sender->sendMessage($this->messages["only_players"]);
            }
            return true;
        }
        return false;
    }

    /**
     * Configura el idioma para los mensajes del plugin.
     *
     * @param string $lang
     */
    public function setLanguage(string $lang) : void {
        // Ruta hacia la carpeta de recursos de idiomas
        $resourcePath = $this->getFile() . "resources/lang/{$lang}.php";
        
        if (file_exists($resourcePath)) {
            // Cargar los mensajes del archivo correspondiente
            $this->messages = include($resourcePath);
        } else {
            $this->getLogger()->error("No se pudo cargar el archivo de idioma: " . $resourcePath);
        }
    }
}

