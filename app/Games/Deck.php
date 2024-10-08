<?php

namespace App\Games;

use App\Models\Card;
use Illuminate\Support\Facades\Redis;

class Deck
{
    // 生成牌堆
    static function create($gameId)
    {
        // 获取所有卡牌，并打乱顺序
        $deck = Card::inRandomOrder()->get();
        

        // Redis 键名，使用游戏 ID 作为唯一标识
        $deckKey = "game:{$gameId}:deck";

        // 将卡牌存储到 Redis 中
        foreach ($deck as $card) {
            Redis::rpush($deckKey, $card->id);  // 将卡牌 ID 推入 Redis 列表
        }

        return true;
    }

    // 检视牌堆
    public static function check($gameId)
    {
        $deckKey = "game:{$gameId}:deck";
        $deck = Redis::lrange($deckKey, 0, -1); // 获取手牌中的所有卡牌 ID

        return $deck;
    }

    // 清空牌堆
    static function clear($gameId)
    {
        $deckKey = "game:{$gameId}:deck";
        Redis::del($deckKey);  // 删除 Redis 键

        return true;
    }
}