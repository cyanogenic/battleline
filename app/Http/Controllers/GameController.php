<?php

namespace App\Http\Controllers;

use App\Games\Deck;
use App\Games\Hand;
use App\Models\Flag;
use App\Models\Card;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    // 创建新游戏
    public function createGame(Request $request)
    {
        // 验证表单
        $request->validate([
            
        ]);

        $player1 = Auth::user();
        $player2 = User::find($request->opponent);

        // 创建对局
        $game = Game::create([
            'player1_id' => $player1->id,
            'player2_id' => $player2->id,
            'current_turn' => 0,
            'status' => 'ongoing',
        ]);

        // 创建战线
        for ($i = 0; $i < 9; $i++) {
            Flag::create([
                'game_id' => $game->id,
                'index' => $i,
            ]);
        }

        // 生成牌堆
        Deck::create($game->id);
        // 初始手牌
        for ($i=0; $i < 7; $i++) { 
            Hand::draw($game->id, $player1->id);
            Hand::draw($game->id, $player2->id);
        }

        return response()->json(['game' => $game], 201);
    }

    // 在战线放置卡牌
    public function playCard(Request $request)
    {
        // 验证表单
        $request->validate([
            
        ]);

        $player = Auth::user();
        $game = Game::find($request->game);
        $flag = $game->flags()->where('index', $request->flag)->first();
        $card = Card::find($request->card);

        // 玩家出牌
        Hand::play($game->id, $player->id, $card->id);
        $flag->cards()->attach($card->id, ['player_id' => $player->id, 'position' => $flag->cards()->count() + 1]);

        // 切换玩家
        $game->switchTurn();
        $game->save();

        return response()->json(['success' => true]);
    }

    public function claimLine(Request $request)
    {
        // 验证表单
        $request->validate([
            'flags'  => 'required|array',
        ]);

        $player = Auth::user();
        $game = Game::find($request->game);
        $flags = $game->flags()->whereIn('index', (array)$request->flags)->get();

        foreach ($flags as $flag) {
            $flag->setWinner();
        }
        
        // TODO 根据checkWinner的返回值处理
        return response()->json(['success' => true]);
    }

    public function drawCard(Request $request)
    {
        // 验证表单
        $request->validate([
            
        ]);

        $player = Auth::user();
        $game = Game::find($request->game);

        $draw = Hand::draw($game->id, $player->id);

        // TODO 根据执行结果处理
        if ($draw != null) {
            return response()->json(['success' => true]);
        }
        else {
            return response()->json(['success' => false], 400);
        }
        
    }

    public function checkHand(Request $request)
    {
        // 验证表单
        $request->validate([
            
        ]);

        $game = Game::find($request->game);

        return response()->json(['hand' => Hand::check($game->id, Auth::id())], 201);
    }
}
