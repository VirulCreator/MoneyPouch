# MoneyPouch
This plugin will allow you to give your player money pouches which will give your players money within a given range. You can configure each pouch to give either money.

Check this out! https://virulcreator.github.io/MoneyPouch/

![MoneyPouch creation](media/MoneyPouch.gif)

## Features
- [x] **Customizable:**
You can change the the pouches money & add how many pouchs you like!

- [x] **Flexible:**
With all this customizability you can use it for tons of gamemodes from kitpvp to factions.

### Economy
The plugin currently supports 1 economy plugins: EconomyAPI.
It will automaticly detect which plugin is loaded.
If you use MultiEconomy, please change the currency you want to use in the config.

### Suggestions
If you have any suggestion to add onto the plugin, feel free to open an issue on github giving a detailed explanation of what you want to get added.
If I feel like the suggestion is good for the plugin, I will add it.

### Issues
Experiencing issues with the plugin? If so please open an issue on Github (and not by reviewing on poggit).
I will fix the issue as soon as possible.

### Contributions
You are free to contribute to the project.
If you open a pull request, make sure you contribute to the **development** branch.
Your code has to be readable, tested and bug-free.

## Todo
- [ ] **Claim effects:** Add (optional)cosmetic effects when claiming a MoneyPouch

## Commands
/moneypouch : base command
/moneypouch <user> <tier> : giving a player a pouch
/moneypouch c
/reload : reload the config

## Commands

| Command        | Description           |
| ------------- |:--------------|
| /moneypouch      | base command     |
| /moneypouch | <user> <tier> : giving a player a pouch|
| /moneypouch |<tier> [player](required if using as console) [amount] : give/receive a money pouch |
| /reload | OP Command to reload |

---
## Config
```yaml
---
messages:
  chest:
    name: "§l§dMoneyPouch"
    lore: "\n§eTier Level: {level}\n§6Min: §f{min}\n§6Max: §f{max}"
  given: "§aYou have received MoneyPouch"
  from: "§eYou gave {player} MoneyPouch {level}"

tiers:
  1:
    min: 100
    max: 1000
  2:
    min: 200
    max: 5000
  3:
    min: 500
    max: 7000
...
```
---

README.md By VirulCreator & help from others xD
