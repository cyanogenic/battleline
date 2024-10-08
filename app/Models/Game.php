<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = [
        'player1_id',
        'player2_id',
        'winner_id',
        'current_turn',
        'status',
    ];

    public function player1()
    {
        return $this->belongsTo(User::class, 'player1_id');
    }

    public function player2()
    {
        return $this->belongsTo(User::class, 'player2_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function flags()
    {
        return $this->hasMany(Flag::class);
    }

    // 检查游戏是否已经结束
    public function isFinished()
    {
        return $this->status === 'finished';
    }

    // 获取当前玩家
    public function currentPlayer()
    {
        return $this->checkTurn() == 1 ? $this->player1() : $this->player2();
    }

    // 检查阶段
    public function checkTurn()
    {
        return intdiv($this->current_turn, 3) == 0 ? 1 : 2;
    }

    // 切换阶段
    public function switchTurn()
    {
        if ($this->current_turn == 5) { $this->current_turn = 0; }
        else { $this->current_turn += 1; }
        $this->save();
    }

    // 确定赢家
    public function setWinner()
    {
        // TODO 实现确定赢家的逻辑
        // 示例：检查是否有三条相邻战线获胜，或者五条独立战线获胜
        $battlefields = $this->battlefields;

        $consecutiveWins = 0;
        $independentWins = 0;

        for ($i = 0; $i < 9; $i++) {
            if ($battlefields[$i]->winner_id) {
                $independentWins++;
                if ($i > 0 && $battlefields[$i]->winner_id == $battlefields[$i - 1]->winner_id) {
                    $consecutiveWins++;
                } else {
                    $consecutiveWins = 1;
                }
            }

            if ($consecutiveWins >= 3) {
                $this->status = 'finished';
                $this->winner_id = $battlefields[$i]->winner_id;
                $this->save();
                return $this->winner_id;
            }

            if ($independentWins >= 5) {
                $this->status = 'finished';
                $this->winner_id = $battlefields[$i]->winner_id;
                $this->save();
                return $this->winner_id;
            }
        }

        return null;
    }
}
