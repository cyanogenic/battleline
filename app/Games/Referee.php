<?php

namespace App\Games;

use App\Models\Flag;
use App\Models\User;
use Illuminate\Support\Collection;

class Referee
{
    public static function flush(Collection $cards)
    {
        // 判断是否为同花色
        $firstCardColor = $cards->first()->color;
        $isSameColor = $cards->every(fn($card) => $card->color === $firstCardColor);

        return $isSameColor ? true : false;
    }

    public static function straight(Collection $cards)
    {
        // 获取点数组并排序
        $values = $cards->pluck('value')->sort()->values();

        // 判断是否为顺子
        $isStraight = ($values[1] === $values[0] + 1) && ($values[2] === $values[1] + 1);

        return $isStraight ? true : false;
    }

    public static function quads(Collection $cards)
    {
        // 判断是否为炸弹
        $firstCardValue = $cards->first()->value;
        $isQuads = $cards->every(fn($card) => $card->value === $firstCardValue);

        return $isQuads ? true : false;
    }

    public static function settleScore(Collection $cards): int
    {
        // 牌点数和作为基础分
        $point = $cards->pluck('value')->sum();

        // 炸弹加300分
        if (self::quads($cards))    { $point += 300; };
        // 同花加250分
        if (self::flush($cards))    { $point += 250; };
        // 顺子加150分
        if (self::straight($cards)) { $point += 150; };

        return $point;
    }

    public static function highestPossibleScore(Collection $cards): int
    {
        $point = 0;
        

        return $point;
    }

    public static function opinion(Flag $flag, User $user)
    {
        $game = $flag->game()->first();
        $players_id = [
            $game->player1_id,
            $game->player2_id,
        ];

        foreach ($players_id as $player_id) {
            $cards = $flag->cards()->wherePivot('player_id', $player_id)->get();
            // 牌点数和作为基础分
            $point[$player_id] = $cards->pluck('value')->sum();

            // 炸弹加300分
            if (self::quads($cards))    { $point[$player_id] += 300; };
            // 同花加250分
            if (self::flush($cards))    { $point[$player_id] += 250; };
            // 顺子加150分
            if (self::straight($cards)) { $point[$player_id] += 150; };
        }

        // 返回该战线胜者的ID
        return array_search(max($point), $point);
    }
}