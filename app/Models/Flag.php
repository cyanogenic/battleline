<?php

namespace App\Models;

use App\Games\Referee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Flag extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'index',
        'winner_id'
    ];

    // 定义模型的关系
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function cards()
    {
        return $this->belongsToMany(Card::class)->withPivot('player_id', 'position');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    // 获取战线上的所有卡牌
    public function getCardsInOrder()
    {
        return $this->cards()->orderBy('pivot_position')->get();
    }

    // 检查战线是否已满（最多3张卡牌）
    public function isFull()
    {
        return $this->cards()->count() >= 3;
    }

    // 检查战线赢家
    public function setWinner()
    {
        $this->winner_id = Referee::opinion($this, Auth::user());
        $this->save();

        // 检查游戏胜者
        $this->game()->setWinner();
        return true;
    }
}
