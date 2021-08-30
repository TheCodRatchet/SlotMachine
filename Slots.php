<?php

$gamesIsLive = true;
$slotMachineRows = 3;
$slotMachineColumns = 3;

if ($slotMachineRows != 3 || $slotMachineColumns != 3 && $slotMachineColumns != 4) {
    echo "This Slot machine is only working in 2 grid modes: 3x3 and 3x4." . PHP_EOL;
    exit;
}

$slotMachine = [];
$payout = [
    "@" => 10,
    "#" => 20,
    "$" => 30,
    "%" => 40,
    "&" => 50
];
$chances = [
    "@" => 43,
    "#" => 22,
    "$" => 15,
    "%" => 11,
    "&" => 9
];
$multiplier = [
    10 => 1,
    20 => 2,
    40 => 3,
    80 => 4,
    160 => 5
];
$slotMachineSymbols = [];

foreach ($chances as $key => $value) {
    for ($i = 0; $i < $value; $i++) {
        $slotMachineSymbols[] = $key;
    }
}

$conditions = [];

if ($slotMachineColumns == 3) {
    $conditions = [
        [
            [0, 0], [0, 1], [0, 2]
        ],
        [
            [1, 0], [1, 1], [1, 2]
        ],
        [
            [2, 0], [2, 1], [2, 2]
        ],
        [
            [0, 0], [1, 1], [2, 2]
        ],
        [
            [0, 2], [1, 1], [2, 0]
        ],
    ];
} else if ($slotMachineColumns == 4) {
    $conditions = [
        [
            [0, 0], [0, 1], [0, 2], [0, 3]
        ],
        [
            [1, 0], [1, 1], [1, 2], [1, 3]
        ],
        [
            [2, 0], [2, 1], [2, 2], [2, 3]
        ],
        [
            [0, 0], [1, 1], [2, 2], [2, 3]
        ],
        [
            [0, 3], [0, 2], [1, 1], [2, 0]
        ],
        [
            [0, 3], [1, 2], [2, 1], [2, 0]
        ],
        [
            [0, 0], [0, 1], [1, 2], [2, 3]
        ],
    ];
}
$leftAmount = 0;

while ($gamesIsLive) {
    $player = readline("Welcome to game 'Book of Symbols'. Please input coins to start the game: ") * 100;

    if ($player == 6969) {
        exit;
    }

    while ($player % 100 !== 0) {
        echo "You can only put in 1$ coins. (100)" . PHP_EOL;
        $player = readline("Welcome to game 'Book of Symbols'. Please input coins to start the game: ") * 100;
    }

    $gameAmount = $player + $leftAmount;

    while ($gameAmount > 0) {
        $leftAmount = 0;
        echo "Available amount: $gameAmount" . PHP_EOL;
        $winningAmount = 0;
        $playAmount = (int)readline("Enter bet amount: ");
        while (in_array($playAmount, array_keys($multiplier)) == false || $playAmount > $gameAmount) {
            echo "Invalid Bet amount" . PHP_EOL;
            $playAmount = (int)readline("Enter bet amount: ");
        }

        $gameAmount -= $playAmount;

        for ($r = 0; $r < $slotMachineRows; $r++) {
            for ($c = 0; $c < $slotMachineColumns; $c++) {
                $slotMachine[$r][$c] = $slotMachineSymbols[array_rand($slotMachineSymbols)];
            }
        };

        foreach ($slotMachine as $rows) {
            foreach ($rows as $columns) {
                echo "| $columns |";
            }
            echo PHP_EOL;
        }

        foreach ($conditions as $condition) {
            $x = [];
            foreach ($condition as $positions) {
                $row = $positions[0];
                $column = $positions[1];
                $x[] = $slotMachine[$row][$column];
            }

            if (count(array_unique($x)) == 1) {
                {
                    $winningAmount += $payout[$x[0]];
                }
            }
        }

        echo "You won: " . $winningAmount * $multiplier[$playAmount] . PHP_EOL;

        $gameAmount += $winningAmount * $multiplier[$playAmount];

        if ($gameAmount == 0) {
            echo "You lost everything." . PHP_EOL;
        }

        $again = strtolower(readline("Do You want to play again? [y/n]: "));

        while ($again !== "y" && $again !== "n") {
            echo "Invalid input" . PHP_EOL;
            $again = strtolower(readline("Do You want to play again? [y/n]: "));
        }

        if ($again === "y") {
            continue;
        } else {
            if ($gameAmount % 100 == 0) {
                echo "Your withdraw: $" . $gameAmount / 100 . PHP_EOL;
                break;
            } else if ($gameAmount < 100) {
                $withdraw = strtolower(readline("Withdraw start from 1$, exit and leave amount for next player? [y/n]: "));
                while ($withdraw !== "y" && $withdraw !== "n") {
                    echo "Invalid input" . PHP_EOL;
                    $again = strtolower(readline("Withdraw start from 1$, exit and leave amount for next player? [y/n]: "));
                }
                if ($withdraw === "y") {
                    $leftAmount = (($gameAmount / 100) - floor($gameAmount / 100)) * 100;
                    break;
                }
            } else {
                $withdraw = strtolower(readline("You can only withdraw 1$ coins, Do you want to withdraw? [y/n]: "));
                while ($withdraw !== "y" && $withdraw !== "n") {
                    echo "Invalid input" . PHP_EOL;
                    $again = strtolower(readline("You can only withdraw in 1$ coins, Do you want to withdraw? [y/n]: "));
                }

                if ($withdraw === "y") {
                    $leftAmount = (($gameAmount / 100) - floor($gameAmount / 100)) * 100;
                    echo "Your withdraw: $" . floor($gameAmount / 100) . PHP_EOL;
                    break;
                }
            }
        }
    }
}