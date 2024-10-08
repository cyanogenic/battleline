<?php

namespace App\Games;

use Illuminate\Support\Facades\Redis;

class Hand
{
    // 玩家摸牌
    public static function draw($gameId, $userId)
    {
        // 获取一副牌堆
        $deckKey = "game:{$gameId}:deck";

        // 从牌堆中取出卡牌并添加到用户手牌
        $cardId = Redis::lpop($deckKey);
        if ($cardId) {
            $handKey = "game:{$gameId}:user:{$userId}:hand";
            Redis::rpush($handKey, $cardId);

            return $cardId;
        }

        return null;
    }

    // 检视玩家手牌
    public static function check($gameId, $userId)
    {
        $handKey = "game:{$gameId}:user:{$userId}:hand";
        $hand = Redis::lrange($handKey, 0, -1); // 获取手牌中的所有卡牌 ID

        return $hand;
    }

    // 玩家出牌
    public static function play($gameId, $userId, $cardId)
    {
        $handKey = "game:{$gameId}:user:{$userId}:hand";

        // 获取当前手牌数量
        $handSize = Redis::llen($handKey);
        if ($handSize == 0) {
            return null;
        }

        Redis::lrem($handKey, 1, $cardId); // 从 Redis 列表中移除卡牌 ID

        return true;
    }

    // 清空玩家手牌
    public static function clear($gameId, $userId)
    {
        $handKey = "game:{$gameId}:user:{$userId}:hand";
        Redis::del($handKey); // 删除 Redis 键

        return true;
    }
}
