<?php

namespace MoneyPouch;

use onebone\economyapi\EconomyAPI;
use pocketmine\block\Block;
use pocketmine\block\ItemFrame;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener{

    /** @var self */
	private static $instance;
	/** @var array */
	private $cfg;
	/** @var array */
	private $tiers = [];

	public function onLoad(): void{
		self::$instance = $this;
		$this->saveDefaultConfig();
		$this->cfg = $this->getConfig()->getAll();
		$this->loadTiers();
	}

	public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public static function get(): self{
		return self::$instance;
	}

    /**
     * @return array
     */
    public function getCfg(): array{
        return $this->cfg;
    }

    public function loadTiers(): void{
        foreach($this->getCfg()["tiers"] as $tier => $data){
            $min = $data["min"];
            $max = $data["max"];
            $this->tiers[$tier] = [$min, $max];
        }
        $this->getLogger()->notice("Loaded all tiers!");
    }

    public function getTier(int $tier): array{
        if(!isset($this->tiers[$tier])){
            $this->getLogger()->error("Tier $tier does not seem to exists!");
            return [];
        }
        return $this->tiers[$tier];
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if($command->getName() === "moneypouch"){
            if(!$sender->isOp()){
                $sender->sendMessage(TextFormat::RED . "It does not seem like you can run that command");
                return false;
            }
            if(!isset($args[0])){
                $sender->sendMessage("Usage: /moneypouch <tier> <player>");
                return false;
            }
            if(!is_numeric($args[0])){
                $sender->sendMessage(TextFormat::RED . "Please use number as a tier");
                return false;
            }
            if(!isset($args[1])){
                if(!$sender instanceof Player){
                    $sender->sendMessage(TextFormat::RED . "You can't give yourself moneypouch");
                    return false;
                }
                $this->giveMoneyPouch($sender, $sender, $args);
                return false;
            }

            if(!$player = $this->getServer()->getPlayer($args[1])){
                $sender->sendMessage(TextFormat::RED . "That player cannot be found");
                return false;
            }
            $this->giveMoneyPouch($sender, $player, $args);
            return true;
        }
        return true;
    }

    public function giveMoneyPouch(CommandSender $sender, Player $player, array $args): void{
        if(!isset($this->tiers[$args[0]])){
            $sender->sendMessage(TextFormat::RED . "that tier cannot be found");
            return;
        }
        $item = Item::get(Item::ENDER_CHEST);
        $nbt = $item->getNamedTag();
        $nbt->setInt("tier", $args[0]);
        $item->setNamedTag($nbt);
        $item->setCustomName($this->getCfg()["messages"]["chest"]["name"]);
        $item->setLore([$this->translateLore($this->getCfg()["messages"]["chest"]["lore"], $args)]);
        $player->getInventory()->addItem($item);
        $sender->sendMessage($this->translateFrom($this->getCfg()["messages"]["from"], $args));
        $player->sendMessage($this->getCfg()["messages"]["given"]);
    }

    public function translateLore(string $message, array $args): string{
        $data = $this->getTier($args[0]);
        $message = str_replace("{level}", $args[0], $message);
        $message = str_replace("{min}", $data[0], $message);
        $message = str_replace("{max}", $data[1], $message);
        return $message;
    }

    public function translateFrom(string $message, array $args): string{
        if(!isset($args[1])){
            $user = "yourself";
        }else{
            $user = $args[1];
        }
        $message = str_replace("{level}", $args[0], $message);
        $message = str_replace("{player}", $user, $message);
        return $message;
    }

    public function translateWon(string $message, int $amount): string{
        $message = str_replace("{amount}", "$" . $amount, $message);
        return $message;
    }

    public function choose(Player $player, Item $item, Block $block): void{
        if($block instanceof ItemFrame){
            return;
        }
        $tier = $this->getTier($item->getNamedTag()->getInt("tier"));
        $rand = mt_rand($tier[0], $tier[1]);
        EconomyAPI::getInstance()->addMoney($player, $rand);
        $player->sendMessage($this->translateWon($this->getCfg()["messages"]["won"], $rand));
        $player->getInventory()->removeItem($item->setCount(1));
    }

    public function onInteract(PlayerInteractEvent $e): void{
        if($e->getItem()->getNamedTag()->hasTag("tier", IntTag::class)){
            $this->choose($e->getPlayer(), $e->getItem(), $e->getBlock());
            $e->setCancelled();
        }
    }

    public function onPlace(BlockPlaceEvent $e): void{
        if($e->getItem()->getNamedTag()->hasTag("tier", IntTag::class)){
            $this->choose($e->getPlayer(), $e->getItem(), $e->getBlock());
            $e->setCancelled();
        }
    }
}